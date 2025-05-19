<?php
require_once __DIR__ . '/../../db.php';

if (!isset($_GET['user_id'])) {
    header("Location: index.php?page=manage_users");
    exit();
}

$userId = $_GET['user_id'];

try {
    $pdo = get_database_connection();
    
    // Hämta användarinformation
    $stmt = $pdo->prepare("SELECT username FROM user WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    if (!$user) {
        throw new Exception("Användaren hittades inte.");
    }
    
    // Hämta användarens inlägg med bildantal
    $stmt = $pdo->prepare("
        SELECT p.*, 
               COUNT(DISTINCT i.id) as image_count,
               GROUP_CONCAT(DISTINCT i.filename) as image_files
        FROM post p
        LEFT JOIN image i ON p.id = i.postId
        WHERE p.userId = ?
        GROUP BY p.id
        ORDER BY p.created DESC
    ");
    $stmt->execute([$userId]);
    $posts = $stmt->fetchAll();
    
} catch (Exception $e) {
    $error = "Ett fel uppstod: " . $e->getMessage();
}

// Hantera borttagning av inlägg
if (isset($_POST['delete_post']) && isset($_POST['post_id'])) {
    try {
        $postId = $_POST['post_id'];
        
        // Börja en transaktion
        $pdo->beginTransaction();
        
        // Ta bort alla bilder kopplade till inlägget
        $stmt = $pdo->prepare("DELETE FROM image WHERE postId = ?");
        $stmt->execute([$postId]);
        
        // Ta bort inlägget
        $stmt = $pdo->prepare("DELETE FROM post WHERE id = ? AND userId = ?");
        $stmt->execute([$postId, $userId]);
        
        $pdo->commit();
        $success = "Inlägget har tagits bort.";
        
        // Uppdatera sidan
        header("Location: index.php?page=view_user_posts&user_id=" . $userId);
        exit;
    } catch (PDOException $e) {
        $pdo->rollBack();
        $error = "Ett fel uppstod vid borttagning: " . $e->getMessage();
    }
}
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Inlägg av <?php echo htmlspecialchars($user['username']); ?></h2>
        <a href="index.php?page=manage_users" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Tillbaka
        </a>
    </div>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <?php if (empty($posts)): ?>
        <div class="alert alert-info">Denna användare har inga inlägg.</div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($posts as $post): ?>
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <?php if ($post['image_count'] > 0): ?>
                            <div class="card-img-top bg-light p-2">
                                <?php 
                                $images = explode(',', $post['image_files']);
                                $firstImage = $images[0];
                                ?>
                                <img src="/uploads/<?php echo htmlspecialchars($firstImage); ?>" 
                                     class="img-fluid" 
                                     alt="Inläggsbild">
                            </div>
                        <?php endif; ?>
                        
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h5>
                            <p class="card-text">
                                <?php echo nl2br(htmlspecialchars(substr($post['content'], 0, 200))); ?>
                                <?php if (strlen($post['content']) > 200) echo '...'; ?>
                            </p>
                            <p class="text-muted">
                                Publicerad: <?php echo date('Y-m-d H:i', strtotime($post['created'])); ?>
                                <br>
                                Bilder: <?php echo $post['image_count']; ?>
                            </p>
                            
                            <div class="btn-group">
                                <a href="index.php?page=edit_post&post_id=<?php echo $post['id']; ?>" 
                                   class="btn btn-sm btn-primary">
                                    <i class="bi bi-pencil"></i> Redigera
                                </a>
                                <form method="post" class="d-inline" 
                                      onsubmit="return confirm('Är du säker på att du vill ta bort detta inlägg?');">
                                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                    <button type="submit" name="delete_post" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i> Ta bort
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div> 