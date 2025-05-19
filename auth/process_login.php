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
        error_log("CSRF validation failed for login attempt");
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $remember = isset($_POST['remember']);

        // Validera input
        if (empty($username)) {
            $errors[] = "Användarnamn måste anges";
        }
        if (empty($password)) {
            $errors[] = "Lösenord måste anges";
        }

        // Kontrollera antal inloggningsförsök
        $attempts = $_SESSION['login_attempts'][$username] ?? 0;
        if ($attempts >= 5) {
            $last_attempt = $_SESSION['last_attempt'][$username] ?? 0;
            $time_passed = time() - $last_attempt;
            
            if ($time_passed < 900) { // 15 minuter
                $errors[] = "För många försök. Vänta " . ceil((900 - $time_passed) / 60) . " minuter innan nästa försök.";
            } else {
                // Återställ försök efter 15 minuter
                $_SESSION['login_attempts'][$username] = 0;
                $attempts = 0;
            }
        }

        // Om inga valideringsfel, försök logga in
        if (empty($errors)) {
            try {
                $user_id = verify_user($username, $password);
                
                if ($user_id) {
                    // Återställ inloggningsförsök vid framgångsrik inloggning
                    unset($_SESSION['login_attempts'][$username]);
                    unset($_SESSION['last_attempt'][$username]);
                    
                    // Spara användarinformation i session
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['username'] = $username;
                    
                    // Hantera "Kom ihåg mig"
                    if ($remember) {
                        setcookie(
                            'remembered_username',
                            $username,
                            [
                                'expires' => time() + (30 * 24 * 60 * 60), // 30 dagar
                                'path' => '/',
                                'secure' => true,
                                'httponly' => true,
                                'samesite' => 'Strict'
                            ]
                        );
                    } else {
                        // Ta bort cookie om den finns
                        setcookie('remembered_username', '', time() - 3600, '/');
                    }
                    
                    // Logga framgångsrik inloggning
                    error_log("Användare $username loggade in framgångsrikt");
                    
                    // Omdirigera till dashboard
                    header("Location: ../dashboard.php");
                    exit();
                } else {
                    // Öka antal försök vid misslyckat försök
                    $_SESSION['login_attempts'][$username] = $attempts + 1;
                    $_SESSION['last_attempt'][$username] = time();
                    
                    $errors[] = "Felaktigt användarnamn eller lösenord";
                    error_log("Misslyckad inloggning för användare: $username");
                }
            } catch (Exception $e) {
                $errors[] = "Ett systemfel har inträffat. Vänligen försök igen senare.";
                error_log("Login error: " . $e->getMessage());
            }
        }
    }
}

// Om vi kommer hit har något gått fel
$_SESSION['login_errors'] = $errors;
header("Location: login.php");
exit(); 