<?php
/**
 * Hantering av inloggningsförsök
 * 
 * Detta skript hanterar:
 * - Validering av inloggningsuppgifter
 * - Verifiering av användare mot databasen
 * - Sessionshantering vid lyckad inloggning
 * - Felhantering och omdirigering
 */

session_start();
require_once 'db.php';

// Initialisera felarray
$errors = [];

// Kontrollera om formuläret har skickats via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Hämta och rensa inmatad data
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Validera användarinput
    if (empty($username)) {
        $errors[] = "Användarnamn måste anges";
    }
    if (empty($password)) {
        $errors[] = "Lösenord måste anges";
    }

    // Försök logga in om valideringen är OK
    if (empty($errors)) {
        $user_id = verify_user($username, $password);
        
        if ($user_id) {
            // Spara användarinformation i sessionen
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            
            // Logga lyckad inloggning
            error_log("Användare $username loggade in framgångsrikt");
            
            // Omdirigera till användarens dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            // Hantera misslyckad inloggning
            $errors[] = "Felaktigt användarnamn eller lösenord";
            error_log("Misslyckad inloggning för användare: $username");
        }
    }
}

// Spara eventuella fel i sessionen och omdirigera tillbaka till inloggningssidan
$_SESSION['login_errors'] = $errors;
header("Location: login2.0.php");
exit();
?> 