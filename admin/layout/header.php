<?php
require_once __DIR__ . "/../../app/bootstrap.php";
require_role('admin');
$user = auth_user();
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin | Bela-Bela Institute</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
            background: white;
        }
    </style>
    <link rel="stylesheet" href="./assets/admin.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="admin-body">

    <div class="admin-layout">

        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="admin-brand">
                <img src="./../images/logo.png" alt="Bela-Bela Institute logo" width="100%">
                <span>Admin</span>
            </div>

            <nav class="admin-nav">
                <a href="./dashboard">Dashboard</a>
                <a href="./courses">Courses</a>
                <a href="./articles.php">News</a>
                <a href="./intakes">Intakes</a>
                <a href="./applications">Applications</a>
                <a href="./students">Students</a>
                <a href="./task_list">Tasks</a>
                <a href="./overrides">Overrides</a>
                <a href="./payments">Payments</a>
                <a href="./../public/logout" class="logout">Logout</a>
            </nav>
        </aside>

        <!-- Main -->
        <div class="admin-main">

            <!-- Top bar -->
            <header class="admin-topbar">
                <span>Welcome, <strong><?= e($user["full_name"]) ?></strong></span>
            </header>

            <main class="admin-content">
