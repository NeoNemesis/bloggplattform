<?php
require_once 'db.php';

// Kontrollera om användaren är admin
if ($_SESSION['username'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

try {
    $pdo = get_database_connection();
    
    // Hämta alla användare och deras inläggsantal
    $stmt = $pdo->query("
        SELECT u.id, u.username, u.created, 
               COUNT(DISTINCT p.id) as post_count,
               COUNT(DISTINCT i.id) as image_count
        FROM user u
        LEFT JOIN post p ON u.id = p.userId
        LEFT JOIN image i ON p.id = i.postId
        GROUP BY u.id
        ORDER BY u.created DESC
    ");
    $users = $stmt->fetchAll();

} catch (PDOException $e) {
    $error = "Ett fel uppstod: " . $e->getMessage();
}

// Hantera borttagning av användare
if (isset($_POST['delete_user']) && isset($_POST['user_id'])) {
    $userId = $_POST['user_id'];
    try {
        // Börja en transaktion
        $pdo->beginTransaction();
        
        // Ta först bort alla bilder kopplade till användarens inlägg
        $stmt = $pdo->prepare("
            DELETE i FROM image i
            INNER JOIN post p ON i.postId = p.id
            WHERE p.userId = ?
        ");
        $stmt->execute([$userId]);
        
        // Ta bort alla användarens inlägg
        $stmt = $pdo->prepare("DELETE FROM post WHERE userId = ?");
        $stmt->execute([$userId]);
        
        // Ta bort användaren
        $stmt = $pdo->prepare("DELETE FROM user WHERE id = ?");
        $stmt->execute([$userId]);
        
        $pdo->commit();
        $success = "Användaren och alla tillhörande data har tagits bort.";
        
        // Uppdatera användarlistan
        header("Location: dashboard.php?page=manage_users");
        exit;
    } catch (PDOException $e) {
        $pdo->rollBack();
        $error = "Ett fel uppstod vid borttagning: " . $e->getMessage();
    }
}
?>

<div class="container-fluid">
    <h2 class="mb-4">Hantera Bloggare</h2>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Användarnamn</th>
                    <th>Registrerad</th>
                    <th>Antal inlägg</th>
                    <th>Antal bilder</th>
                    <th>Åtgärder</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo date('Y-m-d', strtotime($user['created'])); ?></td>
                        <td><?php echo $user['post_count']; ?></td>
                        <td><?php echo $user['image_count']; ?></td>
                        <td>
                            <a href="dashboard.php?page=view_user_posts&user_id=<?php echo $user['id']; ?>" 
                               class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i> Visa inlägg
                            </a>
                            <form method="post" class="d-inline" onsubmit="return confirm('Är du säker på att du vill ta bort denna användare och alla tillhörande inlägg?');">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <button type="submit" name="delete_user" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i> Ta bort
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div> 