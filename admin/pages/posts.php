<?php
require_once __DIR__ . '/../../db.php';

try {
    $pdo = get_database_connection();
    
    // Hämta användarens inlägg
    $stmt = $pdo->prepare("
        SELECT * FROM post 
        WHERE userId = ? 
        ORDER BY created DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $posts = $stmt->fetchAll();
    
} catch (PDOException $e) {
    echo '<div class="alert alert-danger">Ett fel uppstod: ' . htmlspecialchars($e->getMessage()) . '</div>';
    exit();
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Hantera inlägg</h2>
    <a href="dashboard.php?page=posts&action=new" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nytt inlägg
    </a>
</div>

<?php if (isset($_GET['action']) && $_GET['action'] === 'new'): ?>
    <div class="card mb-4">
        <div class="card-body">
            <h3>Skapa nytt inlägg</h3>
            <form action="actions/save_post.php" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="title" class="form-label">Titel</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>
                <div class="mb-3">
                    <label for="content" class="form-label">Innehåll</label>
                    <textarea class="form-control" id="content" name="content" rows="5" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="image" class="form-label">Bild</label>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                </div>
                <button type="submit" class="btn btn-primary">Spara inlägg</button>
                <a href="?page=posts" class="btn btn-secondary">Avbryt</a>
            </form>
        </div>
    </div>
<?php endif; ?>

<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Titel</th>
                <th>Skapad</th>
                <th>Status</th>
                <th>Åtgärder</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($posts)): ?>
                <tr>
                    <td colspan="4" class="text-center">Inga inlägg hittades.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($post['title']); ?></td>
                        <td><?php echo date('Y-m-d H:i', strtotime($post['created'])); ?></td>
                        <td>
                            <span class="badge bg-success">Publicerad</span>
                        </td>
                        <td>
                            <a href="?page=posts&action=edit&id=<?php echo $post['id']; ?>" 
                               class="btn btn-sm btn-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="actions/delete_post.php?id=<?php echo $post['id']; ?>" 
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('Är du säker på att du vill ta bort detta inlägg?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div> 