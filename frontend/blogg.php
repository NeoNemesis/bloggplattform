<?php
session_start();
require_once '../db.php';

// Hämta post ID och blogger ID från URL
$post_id = isset($_GET['post']) ? intval($_GET['post']) : null;
$blogger_id = isset($_GET['blogger']) ? intval($_GET['blogger']) : null;

// Inkludera header
require_once '../templates/header.php';
?>

<link rel="stylesheet" href="../css/layout.css">

<main class="container mt-4">
    <div class="row">
        <!-- Vänster sidofält - Lista över inlägg -->
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-body">
                    <h3 class="mb-3">Blogginlägg</h3>
                    <?php
                    // Hämta alla inlägg för bloggaren eller alla inlägg om ingen bloggare är vald
                    $posts = $blogger_id ? 
                            get_blogger_posts($blogger_id) : 
                            get_latest_posts(10);
                    
                    if (!empty($posts)): ?>
                        <div class="list-group">
                            <?php foreach ($posts as $post): ?>
                                <a href="?post=<?php echo $post['id']; ?><?php echo $blogger_id ? '&blogger=' . $blogger_id : ''; ?>" 
                                   class="list-group-item list-group-item-action <?php echo ($post_id == $post['id']) ? 'active' : ''; ?>">
                                    <?php echo htmlspecialchars($post['title']); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p>Inga inlägg hittade.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Huvudinnehåll - Valt inlägg -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-body">
                    <?php
                    if ($post_id) {
                        $post = get_post($post_id);
                        if ($post): ?>
                            <article>
                                <h1><?php echo htmlspecialchars($post['title']); ?></h1>
                                <div class="meta text-muted mb-3">
                                    Publicerad: <?php echo date('Y-m-d', strtotime($post['created'])); ?>
                                </div>
                                <?php if ($post['image_filename']): ?>
                                    <img src="<?php echo get_image_path($post['image_filename']); ?>" 
                                         alt="Bild till inlägget" 
                                         class="img-fluid mb-3">
                                <?php endif; ?>
                                <div class="content">
                                    <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                                </div>
                            </article>
                        <?php else: ?>
                            <p>Inlägget kunde inte hittas.</p>
                        <?php endif;
                    } else {
                        // Visa senaste inlägget om inget är valt
                        $latest = get_latest_post($blogger_id);
                        if ($latest): ?>
                            <article>
                                <h1><?php echo htmlspecialchars($latest['title']); ?></h1>
                                <div class="meta text-muted mb-3">
                                    Publicerad: <?php echo date('Y-m-d', strtotime($latest['created'])); ?>
                                </div>
                                <?php if ($latest['image_filename']): ?>
                                    <img src="<?php echo get_image_path($latest['image_filename']); ?>" 
                                         alt="Bild till inlägget" 
                                         class="img-fluid mb-3">
                                <?php endif; ?>
                                <div class="content">
                                    <?php echo nl2br(htmlspecialchars($latest['content'])); ?>
                                </div>
                            </article>
                        <?php else: ?>
                            <p>Inga inlägg tillgängliga.</p>
                        <?php endif;
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- Höger sidofält - Bloggarinfo -->
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-body">
                    <?php
                    if ($blogger_id) {
                        $blogger = get_blogger_info($blogger_id);
                        if ($blogger): ?>
                            <h3><?php echo htmlspecialchars($blogger['username']); ?></h3>
                            <?php if ($blogger['image']): ?>
                                <img src="<?php echo get_image_path($blogger['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($blogger['username']); ?>" 
                                     class="img-fluid rounded-circle mb-3">
                            <?php endif; ?>
                            <?php if ($blogger['title']): ?>
                                <p class="text-muted"><?php echo htmlspecialchars($blogger['title']); ?></p>
                            <?php endif; ?>
                            <?php if ($blogger['presentation']): ?>
                                <p><?php echo nl2br(htmlspecialchars($blogger['presentation'])); ?></p>
                            <?php endif; ?>
                            <p class="text-muted">Medlem sedan: <?php echo date('Y-m-d', strtotime($blogger['created'])); ?></p>
                        <?php else: ?>
                            <p>Bloggaren kunde inte hittas.</p>
                        <?php endif;
                    } else: ?>
                        <h3>Välj en bloggare</h3>
                        <p>Välj ett inlägg från listan till vänster för att se mer information om bloggaren.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once '../templates/footer.php'; ?> 