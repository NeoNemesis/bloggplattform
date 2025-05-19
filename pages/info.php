<?php
require_once __DIR__ . '/../db.php';

try {
    $pdo = get_database_connection();
    
    // Hämta användarinformation
    $stmt = $pdo->prepare("SELECT * FROM user WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    // Hantera profiluppdatering
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
        $title = trim($_POST['title']);
        $presentation = trim($_POST['presentation']);
        
        $stmt = $pdo->prepare("UPDATE user SET title = ?, presentation = ? WHERE id = ?");
        if ($stmt->execute([$title, $presentation, $_SESSION['user_id']])) {
            echo '<div class="alert alert-success">Din profil har uppdaterats!</div>';
            // Uppdatera användarinformationen
            $stmt = $pdo->prepare("SELECT * FROM user WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();
        } else {
            echo '<div class="alert alert-danger">Kunde inte uppdatera profilen.</div>';
        }
    }
    
} catch (PDOException $e) {
    echo '<div class="alert alert-danger">Ett fel uppstod: ' . htmlspecialchars($e->getMessage()) . '</div>';
    exit();
}
?>

<h2>Min Profil</h2>

<div class="card">
    <div class="card-body">
        <form method="POST" action="">
            <div class="mb-3">
                <label for="username" class="form-label">Användarnamn</label>
                <input type="text" class="form-control" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
            </div>
            
            <div class="mb-3">
                <label for="title" class="form-label">Titel</label>
                <input type="text" class="form-control" id="title" name="title" 
                       value="<?php echo htmlspecialchars($user['title'] ?? ''); ?>">
            </div>
            
            <div class="mb-3">
                <label for="presentation" class="form-label">Presentation</label>
                <textarea class="form-control" id="presentation" name="presentation" 
                          rows="4"><?php echo htmlspecialchars($user['presentation'] ?? ''); ?></textarea>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Medlem sedan</label>
                <p class="form-control-static">
                    <?php echo date('Y-m-d', strtotime($user['created'])); ?>
                </p>
            </div>
            
            <button type="submit" name="update_profile" class="btn btn-primary">
                Uppdatera Profil
            </button>
        </form>
    </div>
</div> 