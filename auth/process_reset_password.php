<?php
session_start();
require_once '../db.php';
require_once '../includes/csrf.php';

$errors = [];

// Kontrollera om formuläret har skickats
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validera CSRF-token
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = "Ogiltig formulärsession. Vänligen försök igen.";
        error_log("CSRF validation failed for password reset attempt");
    } else {
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);

        if (!$email) {
            $errors[] = "Vänligen ange en giltig e-postadress.";
        } else {
            try {
                // TODO: Implementera faktisk e-postfunktionalitet här
                // För nu, simulerar vi bara en framgångsrik återställning
                
                // Generera en unik token för återställning
                $reset_token = bin2hex(random_bytes(32));
                
                // I en riktig implementation skulle vi:
                // 1. Spara token i databasen med tidsstämpel
                // 2. Skicka e-post med återställningslänk
                // 3. Implementera en sida för att hantera återställningen
                
                $_SESSION['reset_success'] = true;
                error_log("Password reset requested for email: $email");
                
                header("Location: reset_password.php");
                exit();
            } catch (Exception $e) {
                $errors[] = "Ett systemfel har inträffat. Vänligen försök igen senare.";
                error_log("Password reset error: " . $e->getMessage());
            }
        }
    }
}

// Om vi kommer hit har något gått fel
$_SESSION['reset_errors'] = $errors;
header("Location: reset_password.php");
exit(); 