<?php
require_once '../db.php';

function get_newest_bloggers($limit = 5) {
    try {
        $pdo = get_database_connection();
        $stmt = $pdo->prepare("
            SELECT u.id, u.username, u.title, u.created,
                   COUNT(p.id) as post_count
            FROM users u
            LEFT JOIN post p ON u.id = p.userId
            GROUP BY u.id
            ORDER BY u.created DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Fel vid hämtning av nya bloggare: " . $e->getMessage());
        return [];
    }
}

$newest_bloggers = get_newest_bloggers();
?>

<div class="card mb-4 info-sidebar">
    <div class="card-body">
        <h3>Nya bloggare</h3>
        <?php if (!empty($newest_bloggers)): ?>
            <div class="list-group list-group-flush blogger-list">
                <?php foreach ($newest_bloggers as $blogger): ?>
                    <a href="blogg.php?blogger=<?php echo $blogger['id']; ?>" 
                       class="list-group-item list-group-item-action">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong><?php echo htmlspecialchars($blogger['username']); ?></strong>
                                <?php if (!empty($blogger['title'])): ?>
                                    <small class="d-block text-muted">
                                        <?php echo htmlspecialchars($blogger['title']); ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                            <span class="badge bg-primary rounded-pill">
                                <?php echo $blogger['post_count']; ?> inlägg
                            </span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>Inga bloggare än.</p>
        <?php endif; ?>
    </div>
</div> 