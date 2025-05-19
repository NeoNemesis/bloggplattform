<?php
require_once __DIR__ . '/../db.php';

try {
    $pdo = get_database_connection();
    
    // Hämta alla inlägg med författarnamn
    $stmt = $pdo->prepare("
        SELECT p.*, u.username 
        FROM post p 
        JOIN user u ON p.userId = u.id 
        ORDER BY p.created DESC
    ");
    $stmt->execute();
    $posts = $stmt->fetchAll();
    
} catch (PDOException $e) {
    echo '<div class="alert alert-danger">Ett fel uppstod: ' . htmlspecialchars($e->getMessage()) . '</div>';
    exit();
}
?>

<h2>Alla Inlägg</h2>

<?php if (empty($posts)): ?>
    <div class="alert alert-info">Inga inlägg hittades.</div>
<?php else: ?>
    <?php foreach ($posts as $post): ?>
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h5>
                <h6 class="card-subtitle mb-2 text-muted">
                    Av: <?php echo htmlspecialchars($post['username']); ?>
                </h6>
                <p class="card-text">
                    <?php echo nl2br(htmlspecialchars(substr($post['content'], 0, 200))); ?>...
                </p>
                <p class="card-text">
                    <small class="text-muted">
                        Publicerad: <?php echo date('Y-m-d H:i', strtotime($post['created'])); ?>
                    </small>
                </p>
                <a href="blogg.php?post=<?php echo $post['id']; ?>" class="btn btn-primary">Läs mer</a>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?> 