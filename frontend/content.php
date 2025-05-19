<?php
require_once '../db.php';

function get_welcome_content() {
    try {
        $pdo = get_database_connection();
        $stmt = $pdo->query("SELECT * FROM page_content WHERE page_key = 'welcome' LIMIT 1");
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Fel vid hämtning av välkomstinnehåll: " . $e->getMessage());
        return null;
    }
}

function get_latest_posts($limit = 3) {
    try {
        $pdo = get_database_connection();
        $stmt = $pdo->prepare("
            SELECT p.*, u.username 
            FROM post p 
            JOIN users u ON p.userId = u.id 
            ORDER BY p.created DESC 
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Fel vid hämtning av senaste inlägg: " . $e->getMessage());
        return [];
    }
}

$welcome_content = get_welcome_content();
$latest_posts = get_latest_posts();
?>

<div class="card mb-4 content-area">
    <div class="card-body">
        <h1 class="card-title">
            <?php echo $welcome_content ? htmlspecialchars($welcome_content['title']) : 'Välkommen till Bloggplattformen'; ?>
        </h1>
        <p class="lead">
            <?php echo $welcome_content ? htmlspecialchars($welcome_content['content']) : 'Här kan du dela dina tankar och idéer med världen!'; ?>
        </p>
        
        <?php if (!isset($_SESSION['user_id'])): ?>
            <div class="cta-buttons mt-4">
                <a href="register.php" class="btn btn-primary me-2">Bli medlem</a>
                <a href="auth/login.php" class="btn btn-outline-primary">Logga in</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($latest_posts)): ?>
    <div class="card mb-4 blog-card">
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