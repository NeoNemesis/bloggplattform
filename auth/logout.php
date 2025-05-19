<?php
session_start();
require_once '../includes/csrf.php';

// Kontrollera om användaren är inloggad
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Rensa alla sessionsvariabler
$_SESSION = array();

// Förstör sessionen
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}
session_destroy();

// Omdirigera till inloggningssidan
header("Location: login.php");
exit();
?> 