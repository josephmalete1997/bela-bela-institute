<?php
// Student portal header
if (!defined('APP_INIT')) { /* fallback bootstrap */ require_once __DIR__ . "/../../app/bootstrap.php"; }
$u = auth_user();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= htmlspecialchars($meta_title ?? 'Student Portal - Bela-Bela Institute', ENT_QUOTES, 'UTF-8') ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Crimson+Text:wght@600;700&family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" referrerpolicy="no-referrer" />
</head>
<body class="student-portal">
  <header class="student-header">
    <div class="container">
      <div class="left">
        <a class="brand" href="portal.php">
          <img src="../images/logo.png" alt="Bela-Bela Institute logo" width="20%">
          <span>Bela-Bela Student</span>
        </a>
      </div>
      <nav class="student-nav">
        <a href="portal.php"><i class="fa fa-home"></i> Portal</a>
        <a href="tasks_board.php"><i class="fa fa-list-check"></i> Tasks</a>
        <a href="profile.php"><i class="fa fa-user"></i> Profile</a>
        <a href="../notifications.php"><i class="fa fa-bell"></i> Notifications</a>
        <a href="../public/logout.php" class="logout"><i class="fa fa-sign-out-alt"></i> Logout</a>
      </nav>
    </div>
  </header>
  <main class="student-main container">
