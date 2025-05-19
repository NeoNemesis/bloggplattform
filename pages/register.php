<?php
require_once '../includes/header.php';
require_once '../footer.php';

// Skriv ut header med aktiv sida
echo getHeader('Registrera dig', 'register');
?>

<div class="container">
    <main class="main-content card">
        <h1>Skapa ett konto på Bloggplattformen</h1>
        
        <form action="process_register.php" method="POST" class="form">
            <div class="form-group">
                <label for="username">Användarnamn:</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="email">E-post:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Lösenord:</label>
                <input type="password" id="password" name="password" required>
                <small class="form-text">Minst 8 tecken, innehålla både stora och små bokstäver, siffror och specialtecken.</small>
            </div>

            <div class="form-group">
                <label for="confirm_password">Bekräfta lösenord:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>

            <div class="form-group">
                <label for="presentation">Presentation:</label>
                <textarea id="presentation" name="presentation" rows="4" placeholder="Berätta lite om dig själv..."></textarea>
            </div>

            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="terms" id="terms" required>
                    Jag accepterar <a href="terms.php">användarvillkoren</a>
                </label>
            </div>

            <button type="submit" class="btn">Skapa konto</button>

            <div class="form-footer">
                <p>Har du redan ett konto? <a href="login.php">Logga in här</a></p>
            </div>
        </form>
    </main>
</div>

<?php echo getFooter(); ?> 