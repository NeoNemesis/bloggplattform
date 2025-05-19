<?php
session_start();

// Kontrollera om användaren är inloggad
if (!isset($_SESSION['username'])) {
    header("Location: auth/login.php");
    exit();
}

// Kontrollera om användaren är admin
$is_admin = ($_SESSION['username'] === 'admin');

require_once 'templates/header.php';
require_once 'db.php';
?>

<main class="container mt-4">
    <div class="row">
        <!-- Sidmeny -->
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-body">
                    <h3><?php echo $is_admin ? 'Admin Dashboard' : 'Min Dashboard'; ?></h3>
                    <ul class="nav flex-column">
                        <?php if ($is_admin): ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'manage_users') ? 'active' : ''; ?>" 
                                   href="dashboard.php?page=manage_users">
                                    <i class="bi bi-people"></i> Hantera bloggare
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo (!isset($_GET['page']) || $_GET['page'] == 'posts') ? 'active' : ''; ?>" 
                               href="dashboard.php?page=posts">
                                <i class="bi bi-pencil-square"></i> <?php echo $is_admin ? 'Hantera inlägg' : 'Mina inlägg'; ?>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'images') ? 'active' : ''; ?>" 
                               href="dashboard.php?page=images">
                                <i class="bi bi-image"></i> Bildhantering
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'profile') ? 'active' : ''; ?>" 
                               href="dashboard.php?page=profile">
                                <i class="bi bi-person"></i> Min profil
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="blogg.php">
                                <i class="bi bi-eye"></i> Visa min blogg
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="auth/logout.php">
                                <i class="bi bi-box-arrow-right"></i> Logga ut
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Huvudinnehåll -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-body">
                    <?php
                    // Dynamiskt laddande av sidor
                    if (isset($_GET['page'])) {
                        $page = $_GET['page'];
                        $valid_pages = ['posts', 'images', 'profile'];
                        
                        // Lägg till admin-sidor om användaren är admin
                        if ($is_admin) {
                            $valid_pages[] = 'manage_users';
                            $valid_pages[] = 'view_user_posts';
                        }
                        
                        if (in_array($page, $valid_pages)) {
                            // Ladda admin-sidor från admin-mappen om det är admin-sidor eller om användaren är admin
                            if ($is_admin && ($page === 'posts' || in_array($page, ['manage_users', 'view_user_posts']))) {
                                include("admin/pages/$page.php");
                            } else {
                                include("pages/$page.php");
                            }
                        } else {
                            echo "<h2>Välkommen " . htmlspecialchars($_SESSION['username']) . "!</h2>";
                            echo "<p>Välj en funktion från menyn för att börja.</p>";
                        }
                    } else {
                        echo "<h2>Välkommen " . htmlspecialchars($_SESSION['username']) . "!</h2>";
                        echo "<p>Välj en funktion från menyn för att börja.</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once 'templates/footer.php'; ?>
