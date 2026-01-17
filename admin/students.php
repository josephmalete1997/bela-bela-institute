<?php

declare(strict_types=1);
require_once dirname(__DIR__) . '/app/bootstrap.php';

$q = trim($_GET["q"] ?? "");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    csrf_verify();

    $userId = (int)($_POST["user_id"] ?? 0);
    $action = $_POST["action"] ?? "";

    if ($userId && in_array($action, ["block", "unblock"], true)) {
        $status = $action === "block" ? "blocked" : "active";
        $stmt = db()->prepare("UPDATE users SET status=? WHERE id=? AND role='student'");
        $stmt->execute([$status, $userId]);
    }

    $redirectQ = $q ? "?q=" . urlencode($q) : "";
    redirect("./students{$redirectQ}");
}

$params = [];
$sql = "
  SELECT
    u.id, u.full_name, u.email, u.phone, u.avatar ,u.status, u.created_at,
    (SELECT COUNT(*) FROM enrollments e WHERE e.user_id=u.id AND e.status='enrolled') AS active_enrollments,
    (SELECT COUNT(*) FROM applications a WHERE a.email=u.email) AS applications_count
  FROM users u
  WHERE u.role='student'
";

if ($q !== "") {
    $sql .= " AND (u.full_name LIKE ? OR u.email LIKE ? OR u.phone LIKE ?)";
    $like = "%" . $q . "%";
    $params = [$like, $like, $like];
}

$sql .= " ORDER BY u.created_at DESC";

$stmt = db()->prepare($sql);
$stmt->execute($params);
$students = $stmt->fetchAll();

require_once __DIR__ . '/layout/header.php';
?>

<div class="admin-card">
    <div style="display:flex; justify-content:space-between; gap:1rem; align-items:center; flex-wrap:wrap;">
        <div>
            <h2>Students</h2>
            <p>Manage student accounts, view enrollments, and block/unblock access.</p>
        </div>

        <form method="get" style="display:flex; gap:.5rem; align-items:center;">
            <input
                type="text"
                name="q"
                placeholder="Search name, email, phone..."
                value="<?= e($q) ?>"
                style="padding:.65rem .8rem; border:1px solid #cbd5e1; border-radius:10px; min-width:260px;">
            <button class="btn-admin btn-primary" type="submit">Search</button>
            <?php if ($q !== ""): ?>
                <a class="btn-admin btn-secondary" href="./students">Clear</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="admin-card">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Student</th>
                <th>Phone</th>
                <th>Status</th>
                <th>Enrollments</th>
                <th>Applications</th>
                <th>Joined</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            <?php if (!$students): ?>
                <tr>
                    <td colspan="7" style="text-align:center; padding:1.5rem;">No students found.</td>
                </tr>
            <?php endif; ?>

            <?php foreach ($students as $s): ?>
                <?php
                $isActive = ($s["status"] ?? "active") === "active";
                ?>

                <tr>
                    <td>
                        <img
                            src="../public/<?= e($s["avatar"] ?? "assets/avatar-placeholder.png") ?>"
                            width="36"
                            height="36"
                            style="border-radius:50%; object-fit:cover; vertical-align:middle; margin-right:8px;"
                            alt="avatar">
                        <strong><?= e($s["full_name"]) ?></strong><br>
                        <small><?= e($s["email"]) ?></small>
                    </td>

                    <td><?= e($s["phone"] ?? "—") ?></td>
                    <td>
                        <?php if ($isActive): ?>
                            <span class="badge badge-green">Active</span>
                        <?php else: ?>
                            <span class="badge badge-red">Blocked</span>
                        <?php endif; ?>
                    </td>
                    <td><?= (int)$s["active_enrollments"] ?></td>
                    <td><?= (int)$s["applications_count"] ?></td>
                    <td><?= e($s["created_at"]) ?></td>
                    <td style="white-space:nowrap;">
                        <a class="btn-admin btn-secondary" href="./student_view?id=<?= (int)$s["id"] ?>">View</a>

                        <form method="post" style="display:inline;">
                            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                            <input type="hidden" name="user_id" value="<?= (int)$s["id"] ?>">

                            <?php if ($isActive): ?>
                                <button class="btn-admin btn-danger" name="action" value="block" type="submit"
                                    onclick="return confirm('Block this student?');">Block</button>
                            <?php else: ?>
                                <button class="btn-admin btn-primary" name="action" value="unblock" type="submit"
                                    onclick="return confirm('Unblock this student?');">Unblock</button>
                            <?php endif; ?>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/layout/footer.php'; ?>