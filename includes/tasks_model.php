<?php

require_once __DIR__ . "/../app/db.php";
require_once __DIR__ . "/../app/helpers.php";

function tasks_find_all_by_status(string $status): array {
  $stmt = db()->prepare("SELECT * FROM tasks WHERE status = :s ORDER BY position ASC, created_at DESC");
  $stmt->execute([':s'=>$status]);
  return $stmt->fetchAll();
}

function tasks_find_all_for_user(int $user_id): array {
  $stmt = db()->prepare("
    SELECT t.*,
      COALESCE(tp.status, 'backlog') AS user_status,
      COALESCE(tp.position, t.position) AS user_position
    FROM tasks t
    LEFT JOIN task_progress tp
      ON tp.task_id = t.id AND tp.user_id = :uid
    ORDER BY user_position ASC, t.created_at DESC
  ");
  $stmt->execute([':uid'=>$user_id]);
  return $stmt->fetchAll();
}

function tasks_list_assigned_reviewers(int $task_id, int $submitter_id): array {
  $stmt = db()->prepare("
    SELECT u.id, u.full_name, u.email
    FROM task_reviewer_assignments tra
    JOIN users u ON u.id = tra.reviewer_id
    WHERE tra.task_id = :tid AND tra.submitter_id = :sid
    ORDER BY tra.assigned_at ASC
  ");
  $stmt->execute([':tid'=>$task_id, ':sid'=>$submitter_id]);
  return $stmt->fetchAll();
}

function tasks_list_course_educators(int $course_id): array {
  $stmt = db()->prepare("
    SELECT u.id, u.full_name, u.email
    FROM course_educators ce
    JOIN users u ON u.id = ce.educator_id
    WHERE ce.course_id = :cid
  ");
  $stmt->execute([':cid'=>$course_id]);
  return $stmt->fetchAll();
}

function tasks_is_course_educator(int $course_id, int $educator_id): bool {
  $stmt = db()->prepare("SELECT COUNT(*) as c FROM course_educators WHERE course_id = :cid AND educator_id = :eid");
  $stmt->execute([':cid'=>$course_id, ':eid'=>$educator_id]);
  $row = $stmt->fetch();
  return ($row && (int)($row['c'] ?? 0) > 0);
}

function tasks_is_assigned_reviewer(int $task_id, int $submitter_id, int $reviewer_id): bool {
  $stmt = db()->prepare("SELECT COUNT(*) as c FROM task_reviewer_assignments WHERE task_id = :tid AND submitter_id = :sid AND reviewer_id = :rid");
  $stmt->execute([':tid'=>$task_id, ':sid'=>$submitter_id, ':rid'=>$reviewer_id]);
  $row = $stmt->fetch();
  return ($row && (int)($row['c'] ?? 0) > 0);
}

function tasks_assign_reviewers(int $task_id, int $submitter_id, int $limit = 5): array {
  $existing = tasks_list_assigned_reviewers($task_id, $submitter_id);
  if (count($existing) >= $limit) return $existing;

  $t = tasks_find($task_id);
  $course_id = $t['course_id'] ?? null;
  if (empty($course_id)) return $existing;

  $need = $limit - count($existing);
  $pdo = db();
  $stmt = $pdo->prepare("
    SELECT DISTINCT u.id
    FROM users u
    JOIN task_progress tp ON tp.user_id = u.id AND tp.status = 'completed'
    JOIN tasks t ON t.id = tp.task_id AND t.type = 'project' AND t.course_id = :cid
    LEFT JOIN task_reviewer_assignments tra
      ON tra.task_id = :tid AND tra.submitter_id = :sid_join AND tra.reviewer_id = u.id
    WHERE u.role = 'student'
      AND u.id <> :sid_exclude
      AND tra.reviewer_id IS NULL
    ORDER BY RAND()
    LIMIT {$need}
  ");
  $stmt->execute([
    ':cid'=>$course_id,
    ':tid'=>$task_id,
    ':sid_join'=>$submitter_id,
    ':sid_exclude'=>$submitter_id
  ]);
  $candidates = $stmt->fetchAll(PDO::FETCH_COLUMN);

  if ($candidates) {
    $ins = $pdo->prepare("INSERT INTO task_reviewer_assignments (task_id, submitter_id, reviewer_id) VALUES (:tid, :sid, :rid)");
    foreach ($candidates as $rid) {
      $ins->execute([':tid'=>$task_id, ':sid'=>$submitter_id, ':rid'=>$rid]);
    }
  }

  return tasks_list_assigned_reviewers($task_id, $submitter_id);
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

function tasks_update_status_for_user(int $task_id, int $user_id, string $status, int $position = 0): bool {
  $stmt = db()->prepare("
    INSERT INTO task_progress (task_id, user_id, status, position)
    VALUES (:tid, :uid, :s, :p)
    ON DUPLICATE KEY UPDATE status=VALUES(status), position=VALUES(position)
  ");
  $ok = $stmt->execute([':tid'=>$task_id, ':uid'=>$user_id, ':s'=>$status, ':p'=>$position]);
  if ($ok && $status === 'in_review') {
    require_once __DIR__ . "/../app/helpers.php";
    $t = tasks_find($task_id);
    $course_id = $t['course_id'] ?? null;
    if ($course_id) {
      if (($t['type'] ?? '') === 'project') {
        $assigned = tasks_assign_reviewers($task_id, $user_id, 5);
        foreach ($assigned as $r) {
          $rid = (int)($r['id'] ?? 0);
          if ($rid) notify_user($rid, 'Review requested', 'A project is awaiting your review: #'.$task_id, '/student/task_view.php?id='.$task_id.'&submitter_id='.$user_id);
        }
      }
      $pdo = db();
      $stmtu = $pdo->prepare("SELECT DISTINCT u.id FROM users u LEFT JOIN tasks tt ON tt.submitter_id = u.id AND tt.course_id = :cid WHERE (tt.status = 'completed' AND tt.type = 'project') OR u.role = 'admin'");
      $stmtu->execute([':cid'=>$course_id]);
      $users = $stmtu->fetchAll();
      $ovs = tasks_list_overrides_for_task($task_id);
      $override_ids = array_map(function($r){ return (int)$r['user_id']; }, $ovs);
      $educators = tasks_list_course_educators((int)$course_id);
      foreach ($users as $u) {
        $uid = (int)($u['id'] ?? 0);
        if ($uid) notify_user($uid, 'Review requested', 'A project is awaiting review: #'.$task_id, '/student/task_view.php?id='.$task_id.'&submitter_id='.$user_id);
      }
      foreach ($educators as $ed) {
        $eid = (int)($ed['id'] ?? 0);
        if ($eid) notify_user($eid, 'Review requested', 'A project is awaiting review: #'.$task_id, '/admin/review.php?id='.$task_id.'&submitter_id='.$user_id);
      }
      foreach ($override_ids as $uid) {
        if ($uid) notify_user($uid, 'Review requested (override)', 'You have been allowed to review project #'.$task_id, '/student/task_view.php?id='.$task_id.'&submitter_id='.$user_id);
      }
    }
  }
  return $ok;
}

function tasks_update(int $id, array $data): bool {
  $stmt = db()->prepare("UPDATE tasks SET title=:title,type=:type,description=:description,course_id=:course_id,assigned_user_id=:assigned_user_id,status=:status,url=:url WHERE id = :id");
  return $stmt->execute([
    ':title'=>$data['title'] ?? '',
    ':type'=>$data['type'] ?? 'topic',
    ':description'=>$data['description'] ?? null,
    ':course_id'=>$data['course_id'] ?? null,
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

function tasks_add_review(int $task_id, int $submitter_id, int $reviewer_id, string $comment, ?bool $is_competent): int {
  if ($reviewer_id === $submitter_id) {
    return 0; // no self-review
  }
  if (!tasks_user_can_review($reviewer_id, $task_id)) {
    return 0;
  }

  $stmt = db()->prepare("INSERT INTO task_reviews (task_id,submitter_id,reviewer_id,comment,is_competent) VALUES (:task_id,:submitter_id,:reviewer_id,:comment,:is_competent)");
  $stmt->execute([':task_id'=>$task_id,':submitter_id'=>$submitter_id,':reviewer_id'=>$reviewer_id,':comment'=>$comment,':is_competent'=>is_null($is_competent)?null:($is_competent?1:0)]);
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
  if ($u && ($u['role'] ?? '') === 'educator') {
    return tasks_is_course_educator((int)$course_id, $reviewer_id);
  }
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

function tasks_get_reviews(int $task_id, ?int $submitter_id = null): array {
  if ($submitter_id) {
    $stmt = db()->prepare("SELECT tr.*, u.full_name as reviewer_name FROM task_reviews tr LEFT JOIN users u ON u.id = tr.reviewer_id WHERE tr.task_id = :id AND tr.submitter_id = :sid ORDER BY tr.created_at DESC");
    $stmt->execute([':id'=>$task_id, ':sid'=>$submitter_id]);
  } else {
    $stmt = db()->prepare("SELECT tr.*, u.full_name as reviewer_name FROM task_reviews tr LEFT JOIN users u ON u.id = tr.reviewer_id WHERE tr.task_id = :id ORDER BY tr.created_at DESC");
    $stmt->execute([':id'=>$task_id]);
  }
  return $stmt->fetchAll();
}

function tasks_get_review_summary(int $task_id, int $submitter_id): array {
  $stmt = db()->prepare("
    SELECT
      SUM(is_competent = 1) AS competent_count,
      SUM(is_competent = 0) AS not_competent_count
    FROM task_reviews
    WHERE task_id = :id AND submitter_id = :sid
  ");
  $stmt->execute([':id'=>$task_id, ':sid'=>$submitter_id]);
  $row = $stmt->fetch() ?: [];
  return [
    'competent' => (int)($row['competent_count'] ?? 0),
    'not_competent' => (int)($row['not_competent_count'] ?? 0),
  ];
}

function tasks_get_user_status(int $task_id, int $user_id): ?string {
  $stmt = db()->prepare("SELECT status FROM task_progress WHERE task_id = :tid AND user_id = :uid LIMIT 1");
  $stmt->execute([':tid'=>$task_id, ':uid'=>$user_id]);
  $row = $stmt->fetch();
  return $row ? (string)$row['status'] : null;
}

function tasks_add_message(int $task_id, int $submitter_id, int $sender_id, int $recipient_id, string $message): int {
  $stmt = db()->prepare("INSERT INTO task_messages (task_id, submitter_id, sender_id, recipient_id, message) VALUES (:tid,:sid,:sender,:recipient,:message)");
  $stmt->execute([
    ':tid'=>$task_id,
    ':sid'=>$submitter_id,
    ':sender'=>$sender_id,
    ':recipient'=>$recipient_id,
    ':message'=>$message,
  ]);
  return (int)db()->lastInsertId();
}

function tasks_get_messages(int $task_id, int $submitter_id): array {
  $stmt = db()->prepare("
    SELECT tm.*, us.full_name AS sender_name, ur.full_name AS recipient_name
    FROM task_messages tm
    LEFT JOIN users us ON us.id = tm.sender_id
    LEFT JOIN users ur ON ur.id = tm.recipient_id
    WHERE tm.task_id = :tid AND tm.submitter_id = :sid
    ORDER BY tm.created_at ASC
  ");
  $stmt->execute([':tid'=>$task_id, ':sid'=>$submitter_id]);
  return $stmt->fetchAll();
}
