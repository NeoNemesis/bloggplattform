<?php
session_start();
require_once 'templates/header.php';
require_once 'includes/csrf.php';

// Generera CSRF-token
$csrf_token = generate_csrf_token();

// Hämta och rensa eventuella felmeddelanden
$errors = $_SESSION['register_errors'] ?? [];
$success = $_SESSION['register_success'] ?? false;
unset($_SESSION['register_errors'], $_SESSION['register_success']);

// Hämta tidigare inmatade värden
$old_values = $_SESSION['old_values'] ?? [];
unset($_SESSION['old_values']);
?>
<style>
.input-group .bi {
    font-size: 1rem !important;
    line-height: 1 !important;
    width: 1rem !important;
    height: 1rem !important;
}
.input-group-text {
    padding: 0.375rem 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.sidebar-menu .nav-link,
.card-body .nav-link {
    color: #000 !important;
    background-color: transparent !important;
}

.card-body .nav-link:hover {
    color: #0d6efd !important;
    text-decoration: none;
}

.card-body h3 {
    color: #000 !important;
    font-weight: 500;
}
</style>

<main class="container mt-4">
    <div class="row">
        <!-- Left Sidebar -->
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-body">
                    <h3 class="mb-3">Meny</h3>
                    <ul class="nav flex-column sidebar-menu">
                        <li class="nav-item"><a href="blogg.php" class="nav-link">Alla inlägg</a></li>
                        <li class="nav-item"><a href="kontakt.php" class="nav-link">Kontakta oss</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h1 class="card-title text-center mb-4">Registrera nytt konto</h1>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <p class="mb-0">Ditt konto har skapats! Du kan nu <a href="auth/login.php">logga in här</a>.</p>
                        </div>
                    <?php else: ?>
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <?php foreach ($errors as $error): ?>
                                    <p class="mb-0"><?php echo htmlspecialchars($error); ?></p>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <form action="process_register.php" method="POST" class="needs-validation" novalidate>
                            <!-- CSRF-token -->
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                            
                            <div class="mb-3">
                                <label for="username" class="form-label h6">Användarnamn:</label>
                                <div class="input-group">
                                    <span class="input-group-text d-flex align-items-center justify-content-center">
                                        <i class="bi bi-person small"></i>
                                    </span>
                                    <input type="text" 
                                           id="username" 
                                           name="username" 
                                           class="form-control" 
                                           required 
                                           minlength="3"
                                           pattern="[a-zA-Z0-9_-]+"
                                           value="<?php echo htmlspecialchars($old_values['username'] ?? ''); ?>">
                                </div>
                                <div class="form-text">Använd endast bokstäver, siffror, bindestreck och understreck.</div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label h6">E-post:</label>
                                <div class="input-group">
                                    <span class="input-group-text d-flex align-items-center justify-content-center">
                                        <i class="bi bi-envelope small"></i>
                                    </span>
                                    <input type="email" 
                                           id="email" 
                                           name="email" 
                                           class="form-control" 
                                           required
                                           value="<?php echo htmlspecialchars($old_values['email'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label h6">Lösenord:</label>
                                <div class="input-group">
                                    <span class="input-group-text d-flex align-items-center justify-content-center">
                                        <i class="bi bi-lock small"></i>
                                    </span>
                                    <input type="password" 
                                           id="password" 
                                           name="password" 
                                           class="form-control" 
                                           required 
                                           minlength="6">
                                    <button class="btn btn-outline-secondary d-flex align-items-center justify-content-center" 
                                            type="button" 
                                            id="togglePassword">
                                        <i class="bi bi-eye small"></i>
                                    </button>
                                </div>
                                <div class="form-text">
                                    Lösenordet måste vara minst 6 tecken långt.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="confirm_password" class="form-label h6">Bekräfta lösenord:</label>
                                <div class="input-group">
                                    <span class="input-group-text d-flex align-items-center justify-content-center">
                                        <i class="bi bi-lock small"></i>
                                    </span>
                                    <input type="password" 
                                           id="confirm_password" 
                                           name="confirm_password" 
                                           class="form-control" 
                                           required>
                                    <button class="btn btn-outline-secondary d-flex align-items-center justify-content-center" 
                                            type="button" 
                                            id="toggleConfirmPassword">
                                        <i class="bi bi-eye small"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" 
                                       class="form-check-input" 
                                       id="terms" 
                                       name="terms" 
                                       required>
                                <label class="form-check-label" for="terms">
                                    Jag accepterar <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">användarvillkoren</a>
                                </label>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-person-plus"></i> Skapa konto
                                </button>
                            </div>
                        </form>

                        <div class="mt-3 text-center">
                            <p>Har du redan ett konto? <a href="auth/login.php">Logga in här</a></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right Sidebar -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h3 class="h5 mb-3">Fördelar med medlemskap</h3>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="bi bi-pencil-square text-primary"></i>
                            Skapa och publicera egna inlägg
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-chat-dots text-primary"></i>
                            Kommentera på andras inlägg
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-bell text-primary"></i>
                            Få notifieringar om nya inlägg
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-people text-primary"></i>
                            Delta i communitydiskussioner
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Terms Modal -->
<div class="modal fade" id="termsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Användarvillkor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h6>1. Medlemskap</h6>
                <p>För att bli medlem måste du vara minst 13 år gammal och acceptera dessa villkor.</p>
                
                <h6>2. Innehåll</h6>
                <p>Du ansvarar för allt innehåll du publicerar. Innehållet får inte bryta mot svensk lag eller vara stötande.</p>
                
                <h6>3. Uppförande</h6>
                <p>Vi förväntar oss ett respektfullt uppträdande mot andra medlemmar.</p>
                
                <h6>4. Sekretess</h6>
                <p>Vi värnar om din integritet och hanterar dina personuppgifter enligt GDPR.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Stäng</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    function setupPasswordToggle(inputId, toggleId) {
        const input = document.getElementById(inputId);
        const toggle = document.getElementById(toggleId);
        
        toggle.addEventListener('click', function() {
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            
            const icon = this.querySelector('i');
            icon.classList.toggle('bi-eye');
            icon.classList.toggle('bi-eye-slash');
        });
    }

    setupPasswordToggle('password', 'togglePassword');
    setupPasswordToggle('confirm_password', 'toggleConfirmPassword');

    // Form validation
    const form = document.querySelector('.needs-validation');
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    });

    // Password strength check
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    
    function checkPasswordStrength() {
        const value = password.value;
        const hasLetter = /[a-zA-Z]/.test(value);
        const hasNumber = /[0-9]/.test(value);
        const isLongEnough = value.length >= 6;
        
        if (value && (!hasLetter || !hasNumber || !isLongEnough)) {
            password.setCustomValidity('Lösenordet måste innehålla både bokstäver och siffror och vara minst 6 tecken långt.');
        } else {
            password.setCustomValidity('');
        }
    }

    function checkPasswordMatch() {
        if (confirmPassword.value && password.value !== confirmPassword.value) {
            confirmPassword.setCustomValidity('Lösenorden matchar inte.');
    } else {
            confirmPassword.setCustomValidity('');
        }
    }

    password.addEventListener('input', function() {
        checkPasswordStrength();
        checkPasswordMatch();
    });

    confirmPassword.addEventListener('input', checkPasswordMatch);
});
</script>

<?php require_once 'templates/footer.php'; ?>
