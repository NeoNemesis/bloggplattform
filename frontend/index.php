<?php
session_start();
require_once '../db.php';

// Inkludera header
require_once '../templates/header.php';
?>

<!-- Lägg till layout.css -->
<link rel="stylesheet" href="../css/layout.css">

<main class="container mt-4">
    <div class="row">
        <!-- Left Sidebar -->
        <div class="col-md-3">
            <?php include 'menu.php'; ?>
        </div>

        <!-- Main Content -->
        <div class="col-md-6">
            <?php include 'content.php'; ?>
        </div>

        <!-- Right Sidebar -->
        <div class="col-md-3">
            <?php include 'info.php'; ?>
        </div>
    </div>
</main>

<?php require_once '../templates/footer.php'; ?> 