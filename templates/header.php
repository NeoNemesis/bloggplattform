<?php
/**
 * Sidhuvud för alla sidor på Bloggplattformen
 * 
 * Denna mall innehåller:
 * - Metadata och sidtitel
 * - CSS och JavaScript-länkar
 * - Huvudnavigering
 * - Inloggningskontroller
 */

require_once dirname(__DIR__) . '/includes/paths.php';
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bloggplattformen</title>
    
    <!-- Externa CSS-bibliotek -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Egna stilmallar -->
    <link rel="stylesheet" href="<?php echo get_css_url('style.css'); ?>">
    <link rel="stylesheet" href="<?php echo get_css_url('components/footer.css'); ?>">
    
    <!-- JavaScript-filer -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="<?php echo get_js_url('main.js'); ?>" defer></script>
</head>
<body>
    <!-- Huvudnavigering -->
    <header class="main-header">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container">
                <!-- Logotyp/Hemsidelänk -->
                <a class="navbar-brand" href="<?php echo get_page_url('index.php'); ?>">Bloggplattformen</a>
                
                <!-- Mobilmeny-knapp -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <!-- Navigationslänkar -->
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo get_page_url('blogg.php'); ?>">Bloggar</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo get_page_url('om.php'); ?>">Om oss</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo get_page_url('kontakt.php'); ?>">Kontakt</a>
                        </li>
                    </ul>
                    
                    <!-- Användarnavigering -->
                    <ul class="navbar-nav">
                        <?php if (isset($_SESSION['user_id'])): // Meny för inloggade användare ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo get_page_url('dashboard.php'); ?>">Min sida</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo get_page_url('auth/logout.php'); ?>">Logga ut</a>
                            </li>
                        <?php else: // Meny för ej inloggade användare ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo get_page_url('auth/login.php'); ?>">Logga in</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo get_page_url('register.php'); ?>">Registrera</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
</body>
</html>
