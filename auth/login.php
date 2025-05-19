<?php
session_start();
require_once '../templates/header.php';
require_once '../includes/csrf.php';

// Generera CSRF-token
$csrf_token = generate_csrf_token();

// Hämta och rensa eventuella felmeddelanden
$errors = $_SESSION['login_errors'] ?? [];
unset($_SESSION['login_errors']);

// Hämta eventuellt sparat användarnamn från cookie
$remembered_username = isset($_COOKIE['remembered_username']) ? htmlspecialchars($_COOKIE['remembered_username']) : '';
?>

<main class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h1 class="card-title text-center mb-4">Logga in</h1>
                    
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <?php foreach ($errors as $error): ?>
                                <p class="mb-0"><?php echo htmlspecialchars($error); ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <form action="process_login.php" method="post" class="mt-4">
                        <!-- CSRF-token -->
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        
                        <div class="mb-3">
                            <label for="username" class="form-label">Användarnamn:</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="username" 
                                   name="username" 
                                   value="<?php echo $remembered_username; ?>"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Lösenord:</label>
                            <div class="input-group">
                                <input type="password" 
                                       class="form-control" 
                                       id="password" 
                                       name="password" 
                                       required>
                                <button class="btn btn-outline-secondary" 
                                        type="button" 
                                        id="togglePassword">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" 
                                   class="form-check-input" 
                                   id="remember" 
                                   name="remember" 
                                   <?php echo $remembered_username ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="remember">Kom ihåg mig</label>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Logga in</button>
                        </div>
                    </form>

                    <div class="mt-3 text-center">
                        <p class="mb-2">Har du inget konto? <a href="../register.php">Registrera dig här</a></p>
                        <p><a href="reset_password.php">Glömt lösenord?</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
document.getElementById('togglePassword').addEventListener('click', function() {
    const passwordInput = document.getElementById('password');
    const icon = this.querySelector('i');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
});
</script>

<?php require_once '../templates/footer.php'; ?> 