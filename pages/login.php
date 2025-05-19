<?php
require_once '../includes/header.php';
require_once '../footer.php';

// Skriv ut header med aktiv sida
echo getHeader('Logga in', 'login');
?>

<div class="container">
    <main class="main-content card">
        <h1>Logga in på Bloggplattformen</h1>
        
        <form action="process_login.php" method="POST" class="form">
            <div class="form-group">
                <label for="username">Användarnamn:</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="password">Lösenord:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="remember" id="remember">
                    Kom ihåg mig
                </label>
            </div>

            <button type="submit" class="btn">Logga in</button>

            <div class="form-footer">
                <p>Har du inget konto? <a href="register.php">Registrera dig här</a></p>
                <p><a href="reset-password.php">Glömt lösenord?</a></p>
            </div>
        </form>
    </main>
</div>

<?php echo getFooter(); ?> 