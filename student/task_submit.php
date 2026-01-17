<?php
require_once __DIR__ . "/../app/bootstrap.php";
require_role('student');
require_once __DIR__ . "/../includes/tasks_model.php";

// Get enrolled courses for the student
$user_id = $_SESSION['user']['id'];
$stmt = db()->prepare("
    SELECT c.id, c.title 
    FROM enrollments e 
    JOIN intakes i ON e.intake_id = i.id 
    JOIN courses c ON i.course_id = c.id 
    WHERE e.user_id = ? AND e.status = 'enrolled'
    ORDER BY c.title
");
$stmt->execute([$user_id]);
$enrolled_courses = $stmt->fetchAll();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = $_POST['title'] ?? '';
  $type = $_POST['type'] ?? 'project';
  $desc = $_POST['description'] ?? '';
  $course_id = (int)($_POST['course_id'] ?? 0) ?: null;
  
  // Validate course_id is one of enrolled courses
  if ($course_id && !in_array($course_id, array_column($enrolled_courses, 'id'))) {
    $errors[] = 'Invalid course selected.';
  }
  
  if (empty($errors)) {
    $data = [
      'title'=>$title,
      'type'=>$type,
      'description'=>$desc,
      'course_id'=>$course_id,
      'submitter_id'=>$user_id,
      'status'=>$type === 'topic' ? 'studying' : 'in_review'
    ];
    tasks_create($data);
    redirect('tasks_board.php');
  }
}

require __DIR__ . "/layout/header.php";
?>
<main class="section">
  <div class="container">
    <h2>Submit Task / Project</h2>
    <?php if ($errors): ?>
      <div style="color:red; margin-bottom:1rem;">
        <?php foreach($errors as $e): ?>
          <p><?= e($e) ?></p>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
    <form method="post" class="form">
      <label>Title<input name="title" required></label>
      <label>Course<select name="course_id">
        <option value="">-- General (not course-specific) --</option>
        <?php foreach($enrolled_courses as $c): ?>
          <option value="<?= e($c['id']) ?>"><?= e($c['title']) ?></option>
        <?php endforeach; ?>
      </select></label>
      <label>Type<select name="type"><option value="topic">Topic (reading/video)</option><option value="project">Project (needs review)</option></select></label>
      <label>Description<textarea name="description"></textarea></label>
      <button class="btn">Submit</button>
    </form>
  </div>
</main>
<?php require_once __DIR__ . "/layout/footer.php";