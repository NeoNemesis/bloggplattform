<?php
session_start();
require_once '../db.php';

// Kontrollera om användaren är inloggad
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

try {
    // Validera indata
    if (empty($_POST['title']) || empty($_POST['content'])) {
        throw new Exception("Titel och innehåll måste fyllas i.");
    }

    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $userId = $_SESSION['user_id'];

    $pdo = get_database_connection();
    $pdo->beginTransaction();

    // Spara inlägget
    $stmt = $pdo->prepare("INSERT INTO post (title, content, userId, created) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$title, $content, $userId]);
    $postId = $pdo->lastInsertId();

    // Hantera bilduppladdning om en bild har valts
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/';
        
        // Skapa uploads-mappen om den inte finns
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Generera ett unikt filnamn
        $fileExtension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $newFileName = uniqid() . '.' . $fileExtension;
        $uploadFile = $uploadDir . $newFileName;

        // Validera filtyp
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($fileExtension, $allowedTypes)) {
            throw new Exception("Endast jpg, jpeg, png och gif-filer är tillåtna.");
        }

        // Validera filstorlek (max 5MB)
        if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
            throw new Exception("Filen är för stor. Maximal storlek är 5MB.");
        }

        // Flytta den uppladdade filen
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
            // Spara bildinformation i databasen
            $stmt = $pdo->prepare("INSERT INTO image (filename, postId) VALUES (?, ?)");
            $stmt->execute([basename($newFileName), $postId]);
        } else {
            throw new Exception("Det gick inte att ladda upp bilden.");
        }
    }

    $pdo->commit();
    
    // Omdirigera tillbaka till inläggssidan med success-meddelande
    $_SESSION['success_message'] = "Inlägget har sparats!";
    header("Location: ../dashboard.php?page=posts");
    exit();

} catch (Exception $e) {
    if (isset($pdo)) {
        $pdo->rollBack();
    }
    $_SESSION['error_message'] = "Ett fel uppstod: " . $e->getMessage();
    header("Location: ../dashboard.php?page=posts&action=new");
    exit();
} 