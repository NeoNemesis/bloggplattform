<?php
/**
 * Startsida för bloggplattformen
 * 
 * Denna sida visar:
 * - Välkomstmeddelande
 * - Senaste inläggen
 * - Nya bloggare
 * - Navigationsmeny
 */

session_start();
require_once 'templates/header.php';
require_once 'db.php';

// Hämta data för startsidan
$latest_posts = get_latest_posts(3);  // Hämta de 3 senaste inläggen
$newest_bloggers = get_newest_bloggers(3);  // Hämta de 3 senaste bloggarna
?>

<style>
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
        <!-- Vänster sidofält -->
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-body">
                    <h3 class="mb-3">Meny</h3>
                    <ul class="nav flex-column sidebar-menu">
                        <li class="nav-item"><a href="blogg.php" class="nav-link">Alla inlägg</a></li>
                        <?php if (!isset($_SESSION['user_id'])): ?>
                            <li class="nav-item"><a href="register.php" class="nav-link">Bli medlem</a></li>
                        <?php endif; ?>
                        <li class="nav-item"><a href="kontakt.php" class="nav-link">Kontakta oss</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Huvudinnehåll -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-body">
                    <h1 class="card-title">Välkommen till Bloggplattformen</h1>
                    <p class="lead">Här kan du dela dina tankar och idéer med världen!</p>
                    
                    <?php if (!isset($_SESSION['user_id'])): // Visa endast för icke inloggade användare ?>
                        <div class="cta-buttons mt-4">
                            <a href="register.php" class="btn btn-primary me-2">Bli medlem</a>
                            <a href="auth/login.php" class="btn btn-outline-primary">Logga in</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($latest_posts)): // Visa senaste inlägg om det finns några ?>
                <div class="card mb-4">
                    <div class="card-body">
                        <h2 class="h4">Senaste inläggen</h2>
                        <div class="list-group list-group-flush">
                            <?php foreach ($latest_posts as $post): ?>
                                <a href="blogg.php?post=<?php echo $post['id']; ?>" class="list-group-item list-group-item-action">
                                    <h5 class="mb-1"><?php echo htmlspecialchars($post['title']); ?></h5>
                                    <p class="mb-1"><?php echo substr(htmlspecialchars($post['content']), 0, 150) . '...'; ?></p>
                                    <small>Av: <?php echo htmlspecialchars($post['username']); ?></small>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Höger sidofält -->
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-body">
                    <h3>Nya bloggare</h3>
                    <?php if (!empty($newest_bloggers)): // Visa nya bloggare om det finns några ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($newest_bloggers as $blogger): ?>
                                <a href="blogg.php?blogger=<?php echo $blogger['id']; ?>" class="list-group-item list-group-item-action">
                                    <?php echo htmlspecialchars($blogger['username']); ?>
                                    <?php if (!empty($blogger['title'])): ?>
                                        <small class="d-block text-muted"><?php echo htmlspecialchars($blogger['title']); ?></small>
                                    <?php endif; ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p>Inga bloggare än.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once 'templates/footer.php'; ?> 