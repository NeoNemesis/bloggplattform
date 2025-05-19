<?php
require_once __DIR__ . '/../db.php';

// Kontrollera om användaren är inloggad
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

try {
    $pdo = get_database_connection();
    
    // Hämta användarinformation
    $stmt = $pdo->prepare("SELECT * FROM user WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
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

<div class="container mt-4">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Min Profil</h5>
                    <p class="card-text">
                        <strong>Användarnamn:</strong> <?php echo htmlspecialchars($user['username']); ?><br>
                        <strong>Medlem sedan:</strong> <?php echo date('Y-m-d', strtotime($user['created'])); ?>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <h2>Mina Inlägg</h2>
            <?php if (empty($posts)): ?>
                <div class="alert alert-info">Du har inte skapat några inlägg än.</div>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h5>
                            <p class="card-text"><?php echo nl2br(htmlspecialchars(substr($post['content'], 0, 200))); ?>...</p>
                            <p class="card-text">
                                <small class="text-muted">Skapad: <?php echo date('Y-m-d H:i', strtotime($post['created'])); ?></small>
                            </p>
                            <a href="blogg.php?post=<?php echo $post['id']; ?>" class="btn btn-primary">Läs mer</a>
                            <a href="blogg.php?post=<?php echo $post['id']; ?>&edit=true" class="btn btn-secondary">Redigera</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div> 