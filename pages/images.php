<?php
require_once dirname(__DIR__) . '/includes/paths.php';
require_once dirname(__DIR__) . '/db.php';

try {
    $pdo = get_database_connection();
    
    // Hämta användarens inlägg för att koppla bilden till
    if ($is_admin) {
        $stmt = $pdo->query("SELECT id, title FROM post ORDER BY created DESC");
    } else {
        $stmt = $pdo->prepare("SELECT id, title FROM post WHERE userId = ? ORDER BY created DESC");
        $stmt->execute([$_SESSION['user_id']]);
    }
    $posts = $stmt->fetchAll();

    // Om det är admin, hämta alla bilder, annars bara användarens egna
    if ($is_admin) {
        $stmt = $pdo->query("
            SELECT i.*, p.title as post_title, u.username
            FROM image i
            JOIN post p ON i.postId = p.id
            JOIN user u ON p.userId = u.id
            ORDER BY i.created DESC
        ");
        $images = $stmt->fetchAll();
    } else {
        $stmt = $pdo->prepare("
            SELECT i.*, p.title as post_title
            FROM image i
            JOIN post p ON i.postId = p.id
            WHERE p.userId = ?
            ORDER BY i.created DESC
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $images = $stmt->fetchAll();
    }
    
} catch (PDOException $e) {
    $error = "Ett fel uppstod: " . $e->getMessage();
}

// Hantera bilduppladdning
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_image'])) {
    try {
        // Validera indata
        if (!isset($_POST['post_id']) || empty($_POST['post_id'])) {
            throw new Exception("Du måste välja ett inlägg att koppla bilden till.");
        }
        
        $postId = $_POST['post_id'];
        $description = trim($_POST['description'] ?? '');
        
        // Om inte admin, kontrollera att inlägget tillhör användaren
        if (!$is_admin) {
            $stmt = $pdo->prepare("SELECT userId FROM post WHERE id = ?");
            $stmt->execute([$postId]);
            $post = $stmt->fetch();
            
            if ($post['userId'] != $_SESSION['user_id']) {
                throw new Exception("Du har inte behörighet att ladda upp bilder till detta inlägg.");
            }
        }
        
        // Kontrollera att en fil har laddats upp
        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Ingen fil valdes eller ett fel uppstod vid uppladdningen.");
        }
        
        $file = $_FILES['image'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        
        // Validera filtyp
        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception("Endast JPG, PNG och GIF-bilder är tillåtna.");
        }
        
        // Kontrollera filstorlek (max 5MB)
        $maxFileSize = 5 * 1024 * 1024; // 5MB i bytes
        if ($file['size'] > $maxFileSize) {
            throw new Exception("Bilden är för stor. Maximal storlek är 5MB.");
        }
        
        // Skapa uploads-mappen om den inte finns
        if (!file_exists(UPLOADS_DIR)) {
            mkdir(UPLOADS_DIR, 0777, true);
        }
        
        // Generera unikt filnamn
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('img_') . '.' . $extension;
        $filepath = UPLOADS_DIR . $filename;
        
        // Flytta filen
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            // Spara i databasen
            $stmt = $pdo->prepare("
                INSERT INTO image (filename, description, postId, created) 
                VALUES (?, ?, ?, NOW())
            ");
            $stmt->execute([$filename, $description, $postId]);
            
            $success = "Bilden har laddats upp.";
            header("Location: dashboard.php?page=images");
            exit;
        } else {
            throw new Exception("Kunde inte spara bilden.");
        }
        
    } catch (Exception $e) {
        $error = "Ett fel uppstod: " . $e->getMessage();
    }
}

// Hantera borttagning av bild
if (isset($_POST['delete_image']) && isset($_POST['image_id'])) {
    try {
        $imageId = $_POST['image_id'];
        
        // Om inte admin, kontrollera att bilden tillhör användaren
        if (!$is_admin) {
            $stmt = $pdo->prepare("
                SELECT p.userId 
                FROM image i
                JOIN post p ON i.postId = p.id
                WHERE i.id = ?
            ");
            $stmt->execute([$imageId]);
            $image = $stmt->fetch();
            
            if ($image['userId'] != $_SESSION['user_id']) {
                throw new Exception("Du har inte behörighet att ta bort denna bild.");
            }
        }
        
        // Hämta filnamnet först
        $stmt = $pdo->prepare("SELECT filename FROM image WHERE id = ?");
        $stmt->execute([$imageId]);
        $image = $stmt->fetch();
        
        if ($image) {
            // Ta bort filen från uploads-mappen
            $filepath = UPLOADS_DIR . $image['filename'];
            if (file_exists($filepath)) {
                unlink($filepath);
            }
            
            // Ta bort från databasen
            $stmt = $pdo->prepare("DELETE FROM image WHERE id = ?");
            $stmt->execute([$imageId]);
            
            $success = "Bilden har tagits bort.";
            header("Location: dashboard.php?page=images");
            exit;
        }
    } catch (Exception $e) {
        $error = "Ett fel uppstod vid borttagning: " . $e->getMessage();
    }
}
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><?php echo $is_admin ? 'Alla bilder' : 'Mina bilder'; ?></h2>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
            <i class="bi bi-cloud-upload"></i> Ladda upp bild
        </button>
    </div>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <?php if (empty($images)): ?>
        <div class="alert alert-info">Inga bilder hittades.</div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($images as $image): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <img src="<?php echo get_upload_url($image['filename']); ?>" 
                             class="card-img-top" 
                             alt="<?php echo htmlspecialchars($image['description']); ?>">
                        
                        <div class="card-body">
                            <h5 class="card-title">
                                <?php echo htmlspecialchars($image['post_title']); ?>
                            </h5>
                            <?php if ($is_admin): ?>
                                <h6 class="card-subtitle mb-2 text-muted">
                                    Av: <?php echo htmlspecialchars($image['username']); ?>
                                </h6>
                            <?php endif; ?>
                            <p class="card-text">
                                <?php echo htmlspecialchars($image['description']); ?>
                            </p>
                            <p class="text-muted">
                                Uppladdad: <?php echo date('Y-m-d H:i', strtotime($image['created'])); ?>
                            </p>
                            
                            <div class="btn-group">
                                <a href="<?php echo get_upload_url($image['filename']); ?>" 
                                   class="btn btn-sm btn-info"
                                   target="_blank">
                                    <i class="bi bi-eye"></i> Visa
                                </a>
                                <form method="post" class="d-inline" 
                                      onsubmit="return confirm('Är du säker på att du vill ta bort denna bild?');">
                                    <input type="hidden" name="image_id" value="<?php echo $image['id']; ?>">
                                    <button type="submit" name="delete_image" class="btn btn-sm btn-danger">
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

<!-- Modal för bilduppladdning -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">Ladda upp bild</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Stäng"></button>
            </div>
            <div class="modal-body">
                <?php if (empty($posts)): ?>
                    <div class="alert alert-warning">
                        Du måste skapa ett inlägg innan du kan ladda upp bilder.
                        <a href="dashboard.php?page=posts" class="alert-link">Skapa ett inlägg här</a>.
                    </div>
                <?php else: ?>
                    <form method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="post_id" class="form-label">Välj inlägg</label>
                            <select class="form-select" id="post_id" name="post_id" required>
                                <option value="">Välj ett inlägg...</option>
                                <?php foreach ($posts as $post): ?>
                                    <option value="<?php echo $post['id']; ?>">
                                        <?php echo htmlspecialchars($post['title']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="image" class="form-label">Välj bild</label>
                            <input type="file" 
                                   class="form-control" 
                                   id="image" 
                                   name="image" 
                                   accept="image/*" 
                                   required>
                            <small class="text-muted">Tillåtna format: JPG, PNG, GIF</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Beskrivning</label>
                            <textarea class="form-control" 
                                      id="description" 
                                      name="description" 
                                      rows="3"></textarea>
                        </div>
                        
                        <button type="submit" name="upload_image" class="btn btn-primary">
                            <i class="bi bi-cloud-upload"></i> Ladda upp
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div> 