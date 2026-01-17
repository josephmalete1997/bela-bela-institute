<?php
require_once __DIR__ . "/../app/bootstrap.php";
require_role('student');

http_response_code(403);
require __DIR__ . "/layout/header.php";
?>
<main class="section">
  <div class="container">
    <h2>Submissions Disabled</h2>
    <p>Task submissions are not available for students. Please use the Tasks board to view assigned work.</p>
    <a class="btn" href="tasks_board.php">Go to Tasks</a>
  </div>
</main>
<?php require_once __DIR__ . "/layout/footer.php";
