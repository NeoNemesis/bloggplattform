<?php
session_start();
require_once 'db.php';
require_once 'includes/image_handler.php';

// Kontrollera om användaren är inloggad
$is_logged_in = isset($_SESSION['user_id']);

// Hämta bloggare ID från URL eller använd inloggad användares ID
$blogger_id = isset($_GET['blogger']) ? intval($_GET['blogger']) : ($is_logged_in ? $_SESSION['user_id'] : null);

// Hämta post ID från URL om det finns
$post_id = isset($_GET['post']) ? intval($_GET['post']) : null;

// Hantera formulär för att skapa/uppdatera inlägg
$message = '';
if ($is_logged_in && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);
        $image_filename = null;
        
        if (!empty($title) && !empty($content)) {
            try {
                $pdo = get_database_connection();
                
                // Hantera bilduppladdning om en fil har skickats
                if (isset($_FILES['post_image']) && $_FILES['post_image']['error'] === UPLOAD_ERR_OK) {
                    try {
                        $image_filename = handle_image_upload($_FILES['post_image'], $post_id ?? null);
                    } catch (Exception $e) {
                        $message = "Bilduppladdningsfel: " . $e->getMessage();
                    }
                }
                
                if ($_POST['action'] === 'create_post') {
                    $stmt = $pdo->prepare("INSERT INTO post (title, content, image_filename, userId) VALUES (?, ?, ?, ?)");
                    if ($stmt->execute([$title, $content, $image_filename, $_SESSION['user_id']])) {
                        $message = "Inlägget har skapats!";
                        $new_post_id = $pdo->lastInsertId();
                        header("Location: blogg.php?post=" . $new_post_id);
                        exit();
                    }
                } 
                elseif ($_POST['action'] === 'update_post' && isset($_POST['post_id'])) {
                    $post_id = intval($_POST['post_id']);
                    
                    // Hämta befintlig bild om den finns
                    $stmt = $pdo->prepare("SELECT image_filename FROM post WHERE id = ? AND userId = ?");
                    $stmt->execute([$post_id, $_SESSION['user_id']]);
                    $old_image = $stmt->fetchColumn();
                    
                    // Uppdatera inlägget
                    $stmt = $pdo->prepare("UPDATE post SET title = ?, content = ?, image_filename = ? WHERE id = ? AND userId = ?");
                    if ($stmt->execute([$title, $content, $image_filename ?? $old_image, $post_id, $_SESSION['user_id']])) {
                        // Ta bort gammal bild om ny bild laddades upp
                        if ($image_filename && $old_image) {
                            delete_post_image($old_image);
                        }
                        $message = "Inlägget har uppdaterats!";
                        header("Location: blogg.php?post=" . $post_id);
                        exit();
                    }
                }
            } catch (PDOException $e) {
                $message = "Ett fel uppstod: " . $e->getMessage();
            }
        } else {
            $message = "Både titel och innehåll måste fyllas i.";
        }
    }
}

// Funktion för att hämta blogginlägg
function getContent($post_id, $blogger_id) {
    try {
        $pdo = get_database_connection();
        
        if ($post_id) {
            // Visa ett specifikt inlägg
            $stmt = $pdo->prepare("
                SELECT p.*, u.username 
                FROM post p 
                JOIN user u ON p.userId = u.id 
                WHERE p.id = ?
            ");
            $stmt->execute([$post_id]);
            $post = $stmt->fetch();
            
            if ($post) {
                $html = '<article class="blog-post">';
                $html .= '<h1>' . htmlspecialchars($post['title']) . '</h1>';
                $html .= '<p class="meta">Skrivet av: ' . htmlspecialchars($post['username']) . '</p>';
                
                // Visa bild om den finns
                if (!empty($post['image_filename'])) {
                    $html .= '<div class="blog-image mb-3">';
                    $html .= '<img src="' . get_image_path($post['image_filename']) . '" 
                                  alt="Bild till inlägget" 
                                  class="img-fluid rounded">';
                    $html .= '</div>';
                }
                
                $html .= '<div class="content">' . nl2br(htmlspecialchars($post['content'])) . '</div>';
                
                // Visa redigera/ta bort knappar om användaren äger inlägget
                if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['userId']) {
                    $html .= '<div class="actions mt-3">';
                    $html .= '<a href="blogg.php?post=' . $post['id'] . '&edit=true" class="btn btn-primary me-2">Redigera</a>';
                    $html .= '<form method="POST" style="display: inline;">';
                    $html .= '<input type="hidden" name="action" value="delete_post">';
                    $html .= '<input type="hidden" name="post_id" value="' . $post['id'] . '">';
                    $html .= '<button type="submit" class="btn btn-danger" onclick="return confirm(\'Är du säker på att du vill ta bort detta inlägg?\')">Ta bort</button>';
                    $html .= '</form>';
                    $html .= '</div>';
                }
                
                $html .= '</article>';
                return $html;
            }
            return '<div class="alert alert-warning">Inlägget kunde inte hittas.</div>';
        } else {
            // Visa alla inlägg eller inlägg från en specifik bloggare
            $sql = "
                SELECT p.*, u.username 
                FROM post p 
                JOIN user u ON p.userId = u.id
            ";
            $params = [];
            
            if ($blogger_id) {
                $sql .= " WHERE p.userId = ?";
                $params[] = $blogger_id;
            }
            
            $sql .= " ORDER BY p.created DESC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $posts = $stmt->fetchAll();
            
            if ($posts) {
                $html = '';
                foreach ($posts as $post) {
                    $html .= '<article class="card mb-4">';
                    $html .= '<div class="card-body">';
                    $html .= '<h2 class="card-title"><a href="blogg.php?post=' . $post['id'] . '" class="text-decoration-none">' . htmlspecialchars($post['title']) . '</a></h2>';
                    $html .= '<p class="card-subtitle mb-2 text-muted">Skrivet av: ' . htmlspecialchars($post['username']) . '</p>';
                    $html .= '<div class="card-text">' . nl2br(htmlspecialchars(substr($post['content'], 0, 200))) . '...</div>';
                    $html .= '<a href="blogg.php?post=' . $post['id'] . '" class="btn btn-primary mt-3">Läs mer</a>';
                    $html .= '</div>';
                    $html .= '</article>';
                }
                return $html;
            }
            return '<div class="alert alert-info">Inga inlägg hittades.</div>';
        }
    } catch (PDOException $e) {
        return '<div class="alert alert-danger">Ett fel uppstod: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
}

// Funktion för att hämta bloggarinformation
function getBloggerInfo($blogger_id) {
    if (!$blogger_id) {
        return '<h3>Alla bloggare</h3>';
    }
    
    try {
        $pdo = get_database_connection();
        $stmt = $pdo->prepare("SELECT username, created FROM user WHERE id = ?");
        $stmt->execute([$blogger_id]);
        $blogger = $stmt->fetch();
        
        if ($blogger) {
            $html = '<h3>' . htmlspecialchars($blogger['username']) . 's blogg</h3>';
            $html .= '<p class="text-muted">Medlem sedan: ' . date('Y-m-d', strtotime($blogger['created'])) . '</p>';
            return $html;
        }
        return '<div class="alert alert-warning">Bloggaren kunde inte hittas.</div>';
    } catch (PDOException $e) {
        return '<div class="alert alert-danger">Ett fel uppstod: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
}

// Inkludera header efter all databaslogik
require_once 'templates/header.php';
?>

<main class="container mt-4">
    <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-8">
            <?php if ($is_logged_in && (!$post_id || (isset($_GET['edit']) && $_GET['edit'] == 'true'))): ?>
                <div class="card mb-4">
                    <div class="card-body">
                        <h2><?php echo $post_id ? 'Redigera inlägg' : 'Skapa nytt inlägg'; ?></h2>
                        <form method="POST" action="" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="<?php echo $post_id ? 'update_post' : 'create_post'; ?>">
                            <?php if ($post_id): ?>
                                <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                            <?php endif; ?>
                            
                            <div class="mb-3">
                                <label for="title" class="form-label">Titel:</label>
                                <input type="text" class="form-control" id="title" name="title" required 
                                       value="<?php echo isset($post['title']) ? htmlspecialchars($post['title']) : ''; ?>">
                            </div>

                            <div class="mb-3">
                                <label for="content" class="form-label">Innehåll:</label>
                                <textarea class="form-control" id="content" name="content" rows="5" required><?php 
                                    echo isset($post['content']) ? htmlspecialchars($post['content']) : ''; 
                                ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="post_image" class="form-label">Bild:</label>
                                <input type="file" class="form-control" id="post_image" name="post_image" accept="image/*">
                                <?php if (isset($post['image_filename']) && $post['image_filename']): ?>
                                    <div class="mt-2">
                                        <img src="<?php echo get_image_path($post['image_filename']); ?>" 
                                             alt="Nuvarande bild" class="img-thumbnail" style="max-width: 200px;">
                                    </div>
                                <?php endif; ?>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <?php echo $post_id ? 'Uppdatera inlägg' : 'Skapa inlägg'; ?>
                            </button>
                            <?php if ($post_id): ?>
                                <a href="blogg.php?post=<?php echo $post_id; ?>" class="btn btn-secondary">Avbryt</a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <?php echo getContent($post_id, $blogger_id); ?>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <?php echo getBloggerInfo($blogger_id); ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once 'templates/footer.php'; ?>