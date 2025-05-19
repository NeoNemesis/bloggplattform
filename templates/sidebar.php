<?php
// Left sidebar template
?>
<div class="card">
    <h3 class="card-header">Meny</h3>
    <div class="card-body">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="blogg.php">Alla inlägg</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="register.php">Bli medlem</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="pages/kontakt.php">Kontakta oss</a>
            </li>
        </ul>
    </div>
</div>

<?php if (!isset($_SESSION['user_id'])): ?>
<div class="card mt-3">
    <h3 class="card-header">Bli medlem</h3>
    <div class="card-body">
        <p>Skapa ett konto för att börja blogga!</p>
        <a href="register.php" class="btn btn-primary">Registrera dig</a>
    </div>
</div>
<?php endif; ?> 