<?php
session_start();
require_once '../templates/header.php';
require_once '../includes/csrf.php';

// Generera CSRF-token
$csrf_token = generate_csrf_token();

// Hämta och rensa eventuella felmeddelanden/success meddelanden
$errors = $_SESSION['reset_errors'] ?? [];
$success = $_SESSION['reset_success'] ?? false;
unset($_SESSION['reset_errors'], $_SESSION['reset_success']);
?>

<main class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h1 class="card-title text-center mb-4">Återställ lösenord</h1>
                    
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <?php foreach ($errors as $error): ?>
                                <p class="mb-0"><?php echo htmlspecialchars($error); ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <p class="mb-0">Ett e-postmeddelande har skickats med instruktioner för att återställa ditt lösenord.</p>
                        </div>
                    <?php else: ?>
                        <form action="process_reset_password.php" method="post" class="mt-4">
                            <!-- CSRF-token -->
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">E-postadress:</label>
                                <input type="email" 
                                       class="form-control" 
                                       id="email" 
                                       name="email" 
                                       required>
                                <div class="form-text">
                                    Ange den e-postadress som är kopplad till ditt konto.
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Skicka återställningslänk</button>
                            </div>
                        </form>
                    <?php endif; ?>

                    <div class="mt-3 text-center">
                        <p><a href="login.php">Tillbaka till inloggning</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once '../templates/footer.php'; ?> 