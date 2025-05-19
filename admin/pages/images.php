<?php
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../includes/image_handler.php';

try {
    $pdo = get_database_connection();
    
    // Hämta användarens bilder
    $stmt = $pdo->prepare("
        SELECT i.* 
        FROM image i 
        JOIN post p ON i.postId = p.id 
        WHERE p.userId = ? 
        ORDER BY i.created DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $images = $stmt->fetchAll();
    
} catch (PDOException $e) {
    echo '<div class="alert alert-danger">Ett fel uppstod: ' . htmlspecialchars($e->getMessage()) . '</div>';
    exit();
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Bildhantering</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
        <i class="bi bi-cloud-upload"></i> Ladda upp bild
    </button>
</div>

<div class="row">
    <?php if (empty($images)): ?>
        <div class="col-12">
            <div class="alert alert-info">Inga bilder hittades.</div>
        </div>
    <?php else: ?>
        <?php foreach ($images as $image): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="<?php echo get_image_path($image['filename']); ?>" 
                         class="card-img-top" 
                         alt="<?php echo htmlspecialchars($image['description']); ?>">
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($image['description']); ?></p>
                        <small class="text-muted">
                            Uppladdad: <?php echo date('Y-m-d', strtotime($image['created'])); ?>
                        </small>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-sm btn-danger" 
                                onclick="deleteImage(<?php echo $image['id']; ?>)">
                            <i class="bi bi-trash"></i> Ta bort
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ladda upp bild</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="actions/upload_image.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="image" class="form-label">Välj bild</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Beskrivning</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Avbryt</button>
                    <button type="submit" class="btn btn-primary">Ladda upp</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function deleteImage(imageId) {
    if (confirm('Är du säker på att du vill ta bort denna bild?')) {
        window.location.href = 'actions/delete_image.php?id=' + imageId;
    }
}
</script> 