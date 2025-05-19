<?php
require_once 'db.php';

try {
    $pdo = get_database_connection();
    
    // Hämta användarinformation
    $stmt = $pdo->prepare("
        SELECT u.*, 
               COUNT(DISTINCT p.id) as post_count,
               COUNT(DISTINCT i.id) as image_count
        FROM user u
        LEFT JOIN post p ON u.id = p.userId
        LEFT JOIN image i ON p.id = i.postId
        WHERE u.id = ?
        GROUP BY u.id
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    if (!$user) {
        throw new Exception("Kunde inte hitta användarinformation.");
    }
    
} catch (PDOException $e) {
    $error = "Ett fel uppstod: " . $e->getMessage();
}

// Hantera profiluppdatering
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    try {
        $title = trim($_POST['title'] ?? '');
        $presentation = trim($_POST['presentation'] ?? '');
        $email = trim($_POST['email'] ?? '');
        
        // Validera e-post
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Ogiltig e-postadress.");
        }
        
        // Uppdatera profilen
        $stmt = $pdo->prepare("
            UPDATE user 
            SET title = ?, presentation = ?, email = ?
            WHERE id = ?
        ");
        $stmt->execute([$title, $presentation, $email, $_SESSION['user_id']]);
        
        // Hantera profilbild om en ny laddats upp
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['profile_image'];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            
            if (!in_array($file['type'], $allowedTypes)) {
                throw new Exception("Endast JPG, PNG och GIF-bilder är tillåtna.");
            }
            
            // Skapa uploads-mappen om den inte finns
            if (!file_exists('uploads')) {
                mkdir('uploads', 0777, true);
            }
            
            // Generera unikt filnamn
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid('profile_') . '.' . $extension;
            $filepath = 'uploads/' . $filename;
            
            // Ta bort gammal profilbild om den finns
            if (!empty($user['image'])) {
                $oldFile = 'uploads/' . $user['image'];
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }
            
            // Flytta den nya filen
            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                $stmt = $pdo->prepare("UPDATE user SET image = ? WHERE id = ?");
                $stmt->execute([$filename, $_SESSION['user_id']]);
            } else {
                throw new Exception("Kunde inte spara profilbilden.");
            }
        }
        
        $success = "Din profil har uppdaterats.";
        
        // Uppdatera användarinformationen
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        
    } catch (Exception $e) {
        $error = "Ett fel uppstod: " . $e->getMessage();
    }
}
?>

<div class="container-fluid">
    <h2 class="mb-4">Min profil</h2>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <?php if (!empty($user['image'])): ?>
                        <img src="uploads/<?php echo htmlspecialchars($user['image']); ?>" 
                             class="rounded-circle mb-3" 
                             style="width: 150px; height: 150px; object-fit: cover;"
                             alt="Profilbild">
                    <?php else: ?>
                        <div class="rounded-circle mb-3 bg-secondary d-flex align-items-center justify-content-center" 
                             style="width: 150px; height: 150px; margin: 0 auto;">
                            <i class="bi bi-person-fill text-white" style="font-size: 4rem;"></i>
                        </div>
                    <?php endif; ?>
                    
                    <h4><?php echo htmlspecialchars($user['username']); ?></h4>
                    <?php if (!empty($user['title'])): ?>
                        <p class="text-muted"><?php echo htmlspecialchars($user['title']); ?></p>
                    <?php endif; ?>
                    
                    <div class="d-flex justify-content-around mt-3">
                        <div>
                            <h5><?php echo $user['post_count']; ?></h5>
                            <small class="text-muted">Inlägg</small>
                        </div>
                        <div>
                            <h5><?php echo $user['image_count']; ?></h5>
                            <small class="text-muted">Bilder</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="title" class="form-label">Titel</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="title" 
                                   name="title" 
                                   value="<?php echo htmlspecialchars($user['title'] ?? ''); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">E-post</label>
                            <input type="email" 
                                   class="form-control" 
                                   id="email" 
                                   name="email" 
                                   value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="presentation" class="form-label">Presentation</label>
                            <textarea class="form-control" 
                                      id="presentation" 
                                      name="presentation" 
                                      rows="4"><?php echo htmlspecialchars($user['presentation'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="profile_image" class="form-label">Profilbild</label>
                            <input type="file" 
                                   class="form-control" 
                                   id="profile_image" 
                                   name="profile_image" 
                                   accept="image/*">
                            <small class="text-muted">Ladda upp en ny bild för att ändra din profilbild.</small>
                        </div>
                        
                        <button type="submit" name="update_profile" class="btn btn-primary">
                            <i class="bi bi-save"></i> Spara ändringar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> 