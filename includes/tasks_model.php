<?php

require_once __DIR__ . "/../app/db.php";
require_once __DIR__ . "/../app/helpers.php";

function tasks_find_all_by_status(string $status): array {
  $stmt = db()->prepare("SELECT * FROM tasks WHERE status = :s ORDER BY position ASC, created_at DESC");
  $stmt->execute([':s'=>$status]);
  return $stmt->fetchAll();
}

function tasks_find(int $id): ?array {
  $stmt = db()->prepare("SELECT * FROM tasks WHERE id = :id LIMIT 1");
  $stmt->execute([':id'=>$id]);
  $r = $stmt->fetch(); return $r ?: null;
}

function tasks_create(array $data): int {
  $stmt = db()->prepare("INSERT INTO tasks (title,type,description,course_id,submitter_id,assigned_user_id,status,position,url) VALUES (:title,:type,:description,:course_id,:submitter_id,:assigned_user_id,:status,:position,:url)");
  $stmt->execute([
    ':title'=>$data['title'] ?? 'Untitled',
    ':type'=>$data['type'] ?? 'topic',
    ':description'=>$data['description'] ?? null,
    ':course_id'=>$data['course_id'] ?? null,
    ':submitter_id'=>$data['submitter_id'] ?? null,
    ':assigned_user_id'=>$data['assigned_user_id'] ?? null,
    ':status'=>$data['status'] ?? 'backlog',
    ':position'=>$data['position'] ?? 0,
    ':url'=>$data['url'] ?? null,
  ]);
  return (int)db()->lastInsertId();
}

function tasks_update_status_and_position(int $id, string $status, int $position): bool {
  $stmt = db()->prepare("UPDATE tasks SET status=:s, position=:p WHERE id = :id");
  $ok = $stmt->execute([':s'=>$status,':p'=>$position,':id'=>$id]);
  if ($ok && $status === 'in_review') {
    // notify eligible reviewers for this task
    require_once __DIR__ . "/../app/helpers.php";
    $t = tasks_find($id);
    $course_id = $t['course_id'] ?? null;
    if ($course_id) {
      $pdo = db();
      $stmtu = $pdo->prepare("SELECT DISTINCT u.id FROM users u LEFT JOIN tasks tt ON tt.submitter_id = u.id AND tt.course_id = :cid WHERE (tt.status = 'completed' AND tt.type = 'project') OR u.role = 'admin'");
      $stmtu->execute([':cid'=>$course_id]);
      $users = $stmtu->fetchAll();
      // include overrides
      $ovs = tasks_list_overrides_for_task($id);
      $override_ids = array_map(function($r){ return (int)$r['user_id']; }, $ovs);
      foreach ($users as $u) {
        $uid = (int)($u['id'] ?? 0);
        if ($uid) notify_user($uid, 'Review requested', 'A task is awaiting review: #'.$id, '/student/task_view.php?id='.$id);
      }
      foreach ($override_ids as $uid) {
        if ($uid) notify_user($uid, 'Review requested (override)', 'You have been allowed to review task #'.$id, '/student/task_view.php?id='.$id);
      }
    }
  }
  return $ok;
}

function tasks_update(int $id, array $data): bool {
  $stmt = db()->prepare("UPDATE tasks SET title=:title,type=:type,description=:description,assigned_user_id=:assigned_user_id,status=:status,url=:url WHERE id = :id");
  return $stmt->execute([
    ':title'=>$data['title'] ?? '',
    ':type'=>$data['type'] ?? 'topic',
    ':description'=>$data['description'] ?? null,
    ':assigned_user_id'=>$data['assigned_user_id'] ?? null,
    ':status'=>$data['status'] ?? 'backlog',
    ':url'=>$data['url'] ?? null,
    ':id'=>$id,
  ]);
}

function tasks_delete(int $id): bool {
  $stmt = db()->prepare("DELETE FROM tasks WHERE id = :id");
  return $stmt->execute([':id'=>$id]);
}

function tasks_list_statuses(): array {
  return ['backlog','studying','in_review','review_feedback','completed'];
}

function tasks_add_review(int $task_id, int $reviewer_id, string $comment, ?bool $is_competent): int {
  // Only allow reviewers who have completed a relevant project for the same course, or admins
  $stmt = db()->prepare("SELECT role FROM users WHERE id = :id LIMIT 1");
  $stmt->execute([':id' => $reviewer_id]);
  $u = $stmt->fetch();
  if ($u && ($u['role'] ?? '') === 'admin') {
    // admin allowed
  } else {
    // check reviewer has completed a project for the same course
    $t = tasks_find($task_id);
    $course_id = $t['course_id'] ?? null;
    if (empty($course_id)) {
      return 0;
    }
    $chk = db()->prepare("SELECT COUNT(*) as c FROM tasks WHERE submitter_id = :uid AND course_id = :cid AND status = 'completed' AND type = 'project'");
    $chk->execute([':uid' => $reviewer_id, ':cid' => $course_id]);
    $row = $chk->fetch();
    if (!($row && (int)($row['c'] ?? 0) > 0)) {
      return 0; // not allowed to review
    }
  }

  $stmt = db()->prepare("INSERT INTO task_reviews (task_id,reviewer_id,comment,is_competent) VALUES (:task_id,:reviewer_id,:comment,:is_competent)");
  $stmt->execute([':task_id'=>$task_id,':reviewer_id'=>$reviewer_id,':comment'=>$comment,':is_competent'=>is_null($is_competent)?null:($is_competent?1:0)]);
  return (int)db()->lastInsertId();
}

function tasks_user_can_review(int $reviewer_id, int $task_id): bool {
  $stmt = db()->prepare("SELECT role FROM users WHERE id = :id LIMIT 1");
  $stmt->execute([':id' => $reviewer_id]);
  $u = $stmt->fetch();
  if ($u && ($u['role'] ?? '') === 'admin') {
    return true;
  }
  $t = tasks_find($task_id);
  $course_id = $t['course_id'] ?? null;
  if (empty($course_id)) return false;
  // check override table first
  $ov = db()->prepare("SELECT COUNT(*) as c FROM task_review_overrides WHERE task_id = :tid AND user_id = :uid");
  $ov->execute([':tid'=>$task_id, ':uid'=>$reviewer_id]);
  $or = $ov->fetch();
  if ($or && (int)($or['c'] ?? 0) > 0) return true;

  $chk = db()->prepare("SELECT COUNT(*) as c FROM tasks WHERE submitter_id = :uid AND course_id = :cid AND status = 'completed' AND type = 'project'");
  $chk->execute([':uid' => $reviewer_id, ':cid' => $course_id]);
  $row = $chk->fetch();
  return ($row && (int)($row['c'] ?? 0) > 0);
}

function tasks_grant_review_override(int $task_id, int $user_id, ?int $granted_by = null): bool {
  try {
    $stmt = db()->prepare("INSERT INTO task_review_overrides (task_id,user_id,granted_by) VALUES (:tid,:uid,:gb) ON DUPLICATE KEY UPDATE granted_by=VALUES(granted_by)");
    $ok = $stmt->execute([':tid'=>$task_id, ':uid'=>$user_id, ':gb'=>$granted_by]);
    if ($ok) {
      // notify the user that they were granted review rights
      require_once __DIR__ . "/../app/helpers.php";
      $title = 'Review access granted';
      $msg = 'You were granted permission to review task #'.$task_id.' by user '.($granted_by ?? 'system');
      notify_user($user_id, $title, $msg, '/student/task_view.php?id='.$task_id);
    }
    return $ok;
  } catch (Exception $e) {
    return false;
  }
}

function tasks_revoke_review_override(int $task_id, int $user_id): bool {
  $stmt = db()->prepare("DELETE FROM task_review_overrides WHERE task_id = :tid AND user_id = :uid");
  $ok = $stmt->execute([':tid'=>$task_id, ':uid'=>$user_id]);
  if ($ok) {
    require_once __DIR__ . "/../app/helpers.php";
    notify_user($user_id, 'Review access revoked', 'Your override to review task #'.$task_id.' was revoked.', '/student/task_view.php?id='.$task_id);
  }
  return $ok;
}

function tasks_list_overrides_for_task(int $task_id): array {
  $stmt = db()->prepare("SELECT tro.user_id, u.full_name AS user_name, tro.granted_by, tro.created_at FROM task_review_overrides tro LEFT JOIN users u ON u.id = tro.user_id WHERE tro.task_id = :tid");
  $stmt->execute([':tid'=>$task_id]);
  return $stmt->fetchAll();
}

function tasks_get_reviews(int $task_id): array {
  $stmt = db()->prepare("SELECT tr.*, u.full_name as reviewer_name FROM task_reviews tr LEFT JOIN users u ON u.id = tr.reviewer_id WHERE tr.task_id = :id ORDER BY tr.created_at DESC");
  $stmt->execute([':id'=>$task_id]);
  return $stmt->fetchAll();
}
