<?php
require_once '../db.php';

function get_menu_items() {
    try {
        $pdo = get_database_connection();
        $stmt = $pdo->query("SELECT * FROM menu_items WHERE active = 1 ORDER BY position");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Fel vid hämtning av menyobjekt: " . $e->getMessage());
        return [];
    }
}

$menu_items = get_menu_items();
?>

<div class="card mb-4">
    <div class="card-body">
        <h3 class="mb-3">Meny</h3>
        <ul class="nav flex-column sidebar-menu">
            <?php foreach ($menu_items as $item): ?>
                <li class="nav-item">
                    <a href="<?php echo htmlspecialchars($item['url']); ?>" class="nav-link">
                        <?php echo htmlspecialchars($item['title']); ?>
                    </a>
                </li>
            <?php endforeach; ?>
            
            <?php if (empty($menu_items)): ?>
                <!-- Standardmenyobjekt om inga finns i databasen -->
                <li class="nav-item"><a href="blogg.php" class="nav-link">Alla inlägg</a></li>
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <li class="nav-item"><a href="register.php" class="nav-link">Bli medlem</a></li>
                <?php endif; ?>
                <li class="nav-item"><a href="kontakt.php" class="nav-link">Kontakta oss</a></li>
            <?php endif; ?>
        </ul>
    </div>
</div> 