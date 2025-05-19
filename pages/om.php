<?php
require_once 'templates/header.php';

// Hämta senaste inläggen för sidebaren
try {
    $stmt = executeQuery("SELECT * FROM blog_posts ORDER BY created_at DESC LIMIT 5");
    $latest_posts = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
} catch (Exception $e) {
    $latest_posts = [];
}
?>

<main class="container">
    <div class="row">
        <!-- Left Sidebar -->
        <div class="col-md-3">
            <?php include 'templates/sidebar.php'; ?>
        </div>

        <!-- Main Content -->
        <div class="col-md-6">
            <h1>Om oss</h1>
            <div class="about-content">
                <!-- Innehåll från original om.html -->
                <p>Välkommen till vår bloggplattform! Vi är dedikerade till att skapa en plats där kreativa röster kan höras.</p>
                <p>Vår mission är att erbjuda en användarvänlig och modern plattform för bloggare att dela sina tankar och idéer.</p>
            </div>
        </div>

        <!-- Right Sidebar -->
        <div class="col-md-3">
            <div class="card">
                <h3 class="card-header">Senaste inläggen</h3>
                <div class="card-body">
                    <?php if (!empty($latest_posts)): ?>
                        <ul class="list-unstyled">
                            <?php foreach ($latest_posts as $post): ?>
                                <li class="mb-2">
                                    <a href="<?php echo get_page_url('post.php?id=' . htmlspecialchars($post['id'])); ?>">
                                        <?php echo htmlspecialchars($post['title']); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>Inga inlägg att visa än.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card mt-3">
                <h3 class="card-header">Om Bloggplattformen</h3>
                <div class="card-body">
                    <p>En plats där du kan dela dina tankar och idéer med världen.</p>
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <a href="<?php echo get_page_url('register.php'); ?>" class="btn btn-primary">Kom igång</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
require_once 'templates/footer.php';
?> 