<?php
require_once('db_config.php');

function getMenu($blogger_id = null) {
    $connection = get_db_connection();

    if ($blogger_id === null) {
        // Om ingen bloggare är vald, visa alla inlägg
        $sql = "SELECT p.id, p.title, u.username 
                FROM post p 
                JOIN users u ON p.userId = u.id 
                ORDER BY p.created_at DESC 
                LIMIT 10";
        $statement = mysqli_prepare($connection, $sql);
    } else {
        // Visa inlägg för specifik bloggare
        $sql = "SELECT p.id, p.title 
                FROM post p 
                WHERE p.userId = ? 
                ORDER BY p.created_at DESC";
        $statement = mysqli_prepare($connection, $sql);
        mysqli_stmt_bind_param($statement, "i", $blogger_id);
    }

    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);

    $menu = "<nav class='blog-menu'>";
    $menu .= "<h3>" . ($blogger_id ? "Bloggarens inlägg" : "Senaste inläggen") . "</h3>";
    $menu .= "<ul>";
    
    while ($row = mysqli_fetch_assoc($result)) {
        $menu .= "<li>";
        $menu .= "<a href='blogg.php?post=" . $row['id'] . "'>";
        $menu .= htmlspecialchars($row['title']);
        if (!$blogger_id && isset($row['username'])) {
            $menu .= " <span class='author'>av " . htmlspecialchars($row['username']) . "</span>";
        }
        $menu .= "</a></li>";
    }
    
    $menu .= "</ul>";
    $menu .= "</nav>";

    mysqli_stmt_close($statement);
    mysqli_close($connection);
    return $menu;
}
?>