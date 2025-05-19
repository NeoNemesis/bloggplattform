<?php
require_once 'db.php';

try {
    $pdo = get_database_connection();
    
    // Om det är admin, hämta alla inlägg, annars bara användarens egna
    if ($is_admin) {
        $stmt = $pdo->query("
            SELECT p.*, u.username, 
                   COUNT(DISTINCT i.id) as image_count,
                   GROUP_CONCAT(DISTINCT i.filename) as image_files
            FROM post p
            LEFT JOIN user u ON p.userId = u.id
            LEFT JOIN image i ON p.id = i.postId
            GROUP BY p.id
            ORDER BY p.created DESC
        ");
        $posts = $stmt->fetchAll();
    } else {
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
        $stmt->execute([$_SESSION['user_id']]);
        $posts = $stmt->fetchAll();
    }
    
} catch (PDOException $e) {
    $error = "Ett fel uppstod: " . $e->getMessage();
}

// Hantera borttagning av inlägg
if (isset($_POST['delete_post']) && isset($_POST['post_id'])) {
    try {
        $postId = $_POST['post_id'];
        
        // Kontrollera att användaren har rätt att ta bort inlägget
        if (!$is_admin) {
            $stmt = $pdo->prepare("SELECT userId FROM post WHERE id = ?");
            $stmt->execute([$postId]);
            $post = $stmt->fetch();
            
            if ($post['userId'] != $_SESSION['user_id']) {
                throw new Exception("Du har inte behörighet att ta bort detta inlägg.");
            }
        }
        
        // Börja en transaktion
        $pdo->beginTransaction();
        
        // Ta bort alla bilder kopplade till inlägget
        $stmt = $pdo->prepare("DELETE FROM image WHERE postId = ?");
        $stmt->execute([$postId]);
        
        // Ta bort inlägget
        $stmt = $pdo->prepare("DELETE FROM post WHERE id = ?");
        $stmt->execute([$postId]);
        
        $pdo->commit();
        $success = "Inlägget har tagits bort.";
        
        // Uppdatera sidan
        header("Location: dashboard.php?page=posts");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Ett fel uppstod vid borttagning: " . $e->getMessage();
    }
}
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><?php echo $is_admin ? 'Alla inlägg' : 'Mina inlägg'; ?></h2>
        <a href="dashboard.php?page=posts&action=new" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nytt inlägg
        </a>
    </div>
    
    <?php
    // Visa success-meddelande om det finns
    if (isset($_SESSION['success_message'])) {
        echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
        unset($_SESSION['success_message']);
    }
    
    // Visa error-meddelande om det finns
    if (isset($_SESSION['error_message'])) {
        echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
        unset($_SESSION['error_message']);
    }
    ?>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

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

    <?php if (empty($posts)): ?>
        <div class="alert alert-info">Inga inlägg hittades.</div>
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
                                <img src="uploads/<?php echo htmlspecialchars($firstImage); ?>" 
                                     class="img-fluid" 
                                     alt="Inläggsbild">
                            </div>
                        <?php endif; ?>
                        
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h5>
                            <?php if ($is_admin): ?>
                                <h6 class="card-subtitle mb-2 text-muted">
                                    Av: <?php echo htmlspecialchars($post['username']); ?>
                                </h6>
                            <?php endif; ?>
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
                                <a href="dashboard.php?page=edit_post&post_id=<?php echo $post['id']; ?>" 
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