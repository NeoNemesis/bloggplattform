<?php
require_once('db_credentials.php');

// Aktivera felrapportering för utvecklingsmiljön
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * Skapar en PDO-databasanslutning med förbättrad felhantering
 * 
 * @return PDO Databasanslutningsobjekt
 * @throws PDOException Om anslutningen misslyckas
 */
function get_database_connection() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            )
        );
        return $pdo;
    } catch (PDOException $e) {
        error_log("PDO-anslutningsfel: " . $e->getMessage());
        die("Anslutning misslyckades: " . $e->getMessage());
    }
}

// Äldre mysqli-anslutning för bakåtkompatibilitet
$connection = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

// Kontrollera anslutningen
if (!$connection) {
    error_log("Databasanslutning misslyckades: " . mysqli_connect_error());
    die("Anslutning misslyckades: " . mysqli_connect_error());
} else {
    error_log("Databasanslutning lyckades");
}

/**
 * Lägger till en ny användare i databasen
 * 
 * @param string $username Användarnamn för den nya användaren
 * @param string $password Lösenord för den nya användaren
 * @return bool True om användaren skapades, false om det misslyckades
 */
function add_user($username, $password) {
    global $connection;
    error_log("Försöker lägga till användare: $username");

    // Kontrollera först om användaren redan finns
    $check_sql = 'SELECT id FROM user WHERE username = ?';
    $check_stmt = mysqli_prepare($connection, $check_sql);
    
    if (!$check_stmt) {
        error_log("Prepare misslyckades vid kontroll: " . mysqli_error($connection));
        return false;
    }
    
    mysqli_stmt_bind_param($check_stmt, "s", $username);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);
    
    if (mysqli_stmt_num_rows($check_stmt) > 0) {
        error_log("Användaren existerar redan: $username");
        mysqli_stmt_close($check_stmt);
        return false;
    }
    mysqli_stmt_close($check_stmt);

    // Om användaren inte finns, skapa den
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql = 'INSERT INTO user (username, password) VALUES (?, ?)';
    $statement = mysqli_prepare($connection, $sql);

    if (!$statement) {
        error_log("Prepare misslyckades: " . mysqli_error($connection));
        return false;
    }

    mysqli_stmt_bind_param($statement, "ss", $username, $hashed_password);
    $result = mysqli_stmt_execute($statement);

    if (!$result) {
        error_log("Execute misslyckades: " . mysqli_stmt_error($statement));
    } else {
        error_log("Användare tillagd framgångsrikt");
    }

    mysqli_stmt_close($statement);
    return $result;
}

/**
 * Hämtar resultatet från en databasfråga och returnerar som en array
 * 
 * @param mysqli_stmt $statement Prepared statement som har körts
 * @return array Array med resultatrader
 */
function get_result($statement) {
    $rows = array();
    $result = mysqli_stmt_get_result($statement);
    if($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
    }
    return $rows;
}

/**
 * Hämtar alla användare från databasen
 * 
 * @return array Lista med alla användare
 */
function get_users() {
    global $connection;
    $sql = 'SELECT * FROM user';
    $statement = mysqli_prepare($connection, $sql);
    
    if (!$statement) {
        error_log("Prepare misslyckades i get_users: " . mysqli_error($connection));
        return array();
    }
    
    if (!mysqli_stmt_execute($statement)) {
        error_log("Execute misslyckades i get_users: " . mysqli_stmt_error($statement));
        mysqli_stmt_close($statement);
        return array();
    }
    
    $result = get_result($statement);
    mysqli_stmt_close($statement);
    return $result;
}

function get_user($username) {
    global $connection;
    $sql = 'SELECT * FROM user WHERE username=?';
    $statement = mysqli_prepare($connection, $sql);
    
    if (!$statement) {
        error_log("Prepare misslyckades i get_user: " . mysqli_error($connection));
        return array();
    }
    
    mysqli_stmt_bind_param($statement, "s", $username);
    
    if (!mysqli_stmt_execute($statement)) {
        error_log("Execute misslyckades i get_user: " . mysqli_stmt_error($statement));
        mysqli_stmt_close($statement);
        return array();
    }
    
    $result = get_result($statement);
    mysqli_stmt_close($statement);
    return $result;
}

function get_password($id) {
    global $connection;
    $sql = 'SELECT password FROM user WHERE id=?';
    $statement = mysqli_prepare($connection, $sql);
    
    if (!$statement) {
        error_log("Prepare misslyckades i get_password: " . mysqli_error($connection));
        return array();
    }
    
    mysqli_stmt_bind_param($statement, "i", $id);
    
    if (!mysqli_stmt_execute($statement)) {
        error_log("Execute misslyckades i get_password: " . mysqli_stmt_error($statement));
        mysqli_stmt_close($statement);
        return array();
    }
    
    $result = get_result($statement);
    mysqli_stmt_close($statement);
    return $result;
}

function get_images($id) {
    global $connection;
    $sql = 'SELECT image.filename, image.description FROM image JOIN post ON image.postId=post.id WHERE post.userId=?';
    $statement = mysqli_prepare($connection, $sql);
    
    if (!$statement) {
        error_log("Prepare misslyckades i get_images: " . mysqli_error($connection));
        return array();
    }
    
    mysqli_stmt_bind_param($statement, "i", $id);
    
    if (!mysqli_stmt_execute($statement)) {
        error_log("Execute misslyckades i get_images: " . mysqli_stmt_error($statement));
        mysqli_stmt_close($statement);
        return array();
    }
    
    $result = get_result($statement);
    mysqli_stmt_close($statement);
    return $result;
}

function change_avatar($filename, $id) {
    global $connection;
    $sql = 'UPDATE user SET image=? WHERE id=?';
    $statement = mysqli_prepare($connection, $sql);
    
    if (!$statement) {
        error_log("Prepare misslyckades i change_avatar: " . mysqli_error($connection));
        return false;
    }
    
    mysqli_stmt_bind_param($statement, "si", $filename, $id);
    $result = mysqli_stmt_execute($statement);
    
    if (!$result) {
        error_log("Execute misslyckades i change_avatar: " . mysqli_stmt_error($statement));
    }
    
    mysqli_stmt_close($statement);
    return $result;
}

function delete_post($id, $userId) {
    global $connection;
    // Kontrollera att användaren äger inlägget
    $sql = 'DELETE FROM post WHERE id=? AND userId=?';
    $statement = mysqli_prepare($connection, $sql);
    
    if (!$statement) {
        error_log("Prepare misslyckades i delete_post: " . mysqli_error($connection));
        return false;
    }
    
    mysqli_stmt_bind_param($statement, "ii", $id, $userId);
    $result = mysqli_stmt_execute($statement);
    
    if (!$result) {
        error_log("Execute misslyckades i delete_post: " . mysqli_stmt_error($statement));
    }
    
    mysqli_stmt_close($statement);
    return $result;
}

function add_post($title, $content, $userId) {
    global $connection;
    $sql = 'INSERT INTO post (title, content, userId) VALUES (?, ?, ?)';
    $statement = mysqli_prepare($connection, $sql);
    
    if (!$statement) {
        error_log("Prepare misslyckades i add_post: " . mysqli_error($connection));
        return false;
    }
    
    mysqli_stmt_bind_param($statement, "ssi", $title, $content, $userId);
    $result = mysqli_stmt_execute($statement);
    
    if (!$result) {
        error_log("Execute misslyckades i add_post: " . mysqli_stmt_error($statement));
    }
    
    mysqli_stmt_close($statement);
    return $result;
}

function get_user_posts($userId) {
    global $connection;
    $sql = 'SELECT * FROM post WHERE userId=? ORDER BY created DESC';
    $statement = mysqli_prepare($connection, $sql);
    
    if (!$statement) {
        error_log("Prepare misslyckades i get_user_posts: " . mysqli_error($connection));
        return array();
    }
    
    mysqli_stmt_bind_param($statement, "i", $userId);
    
    if (!mysqli_stmt_execute($statement)) {
        error_log("Execute misslyckades i get_user_posts: " . mysqli_stmt_error($statement));
        mysqli_stmt_close($statement);
        return array();
    }
    
    $result = get_result($statement);
    mysqli_stmt_close($statement);
    return $result;
}

function get_latest_posts($limit = 5) {
    global $connection;
    $sql = 'SELECT post.*, user.username FROM post JOIN user ON post.userId = user.id ORDER BY post.created DESC LIMIT ?';
    $statement = mysqli_prepare($connection, $sql);
    
    if (!$statement) {
        error_log("Prepare misslyckades i get_latest_posts: " . mysqli_error($connection));
        return array();
    }
    
    mysqli_stmt_bind_param($statement, "i", $limit);
    
    if (!mysqli_stmt_execute($statement)) {
        error_log("Execute misslyckades i get_latest_posts: " . mysqli_stmt_error($statement));
        mysqli_stmt_close($statement);
        return array();
    }
    
    $result = get_result($statement);
    mysqli_stmt_close($statement);
    return $result;
}

/**
 * OBS! Kan ta bort alla tabeller ut databasen om så önskas
 *
 * Importerar databastabeller och innehåll i databasen från en .sql-fil
 * Använd MyPhpAdmin för att exportera din lokala databas till en .sql-fil
 *
 * @param $filename
 * @param $dropOldTables - skicka in TRUE om alla tabeller som finns ska tas bort
 */
function import($filename, $dropOldTables=FALSE) {
    global $connection;
    if ($dropOldTables) {
        $query = 'SHOW TABLES';
        $result = mysqli_query($connection, $query);
        if ($result) {
            while ($row = mysqli_fetch_row($result)) {
                $query = 'DROP TABLE ' . $row[0];
                if (mysqli_query($connection, $query))
                    echo 'Tabellen <strong>' . $row[0] . '</strong> togs bort<br>';
            }
        }
    }
    
    $query = '';
    $lines = file($filename);

    foreach ($lines as $line) {
        if (substr($line, 0, 2) == '--' || $line == '')
            continue;

        $query .= $line;

        if (substr(trim($line), -1, 1) == ';') {
            if (!mysqli_query($connection, $query))
                echo "<br>Fel i frågan: <strong>$query</strong><br><br>";
            $query = '';
        }
    }
    echo 'Importeringen är klar!<br>';
}

/**
 * Verifierar en användares inloggningsuppgifter
 * 
 * @param string $username Användarnamn
 * @param string $password Lösenord
 * @return int|false Användar-ID om verifieringen lyckas, false om den misslyckas
 */
function verify_user($username, $password) {
    global $connection;
    $sql = 'SELECT id, password FROM user WHERE username=?';
    $statement = mysqli_prepare($connection, $sql);
    
    if (!$statement) {
        error_log("Prepare misslyckades i verify_user: " . mysqli_error($connection));
        return false;
    }
    
    mysqli_stmt_bind_param($statement, "s", $username);
    
    if (!mysqli_stmt_execute($statement)) {
        error_log("Execute misslyckades i verify_user: " . mysqli_stmt_error($statement));
        mysqli_stmt_close($statement);
        return false;
    }
    
    $result = mysqli_stmt_get_result($statement);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($statement);
    
    if ($user && password_verify($password, $user['password'])) {
        return $user['id'];
    }
    return false;
}

/**
 * Uppdaterar en användares profilinformation
 * 
 * @param int $userId Användar-ID
 * @param string $title Bloggtitel
 * @param string $presentation Presentation/beskrivning
 * @return bool True om uppdateringen lyckades, false om den misslyckades
 */
function update_user_profile($userId, $title, $presentation) {
    global $connection;
    $sql = 'UPDATE user SET title=?, presentation=? WHERE id=?';
    $statement = mysqli_prepare($connection, $sql);
    
    if (!$statement) {
        error_log("Prepare misslyckades i update_user_profile: " . mysqli_error($connection));
        return false;
    }
    
    mysqli_stmt_bind_param($statement, "ssi", $title, $presentation, $userId);
    $result = mysqli_stmt_execute($statement);
    
    if (!$result) {
        error_log("Execute misslyckades i update_user_profile: " . mysqli_stmt_error($statement));
    }
    
    mysqli_stmt_close($statement);
    return $result;
}

function get_newest_bloggers($limit = 5) {
    global $connection;
    $sql = 'SELECT id, username, title, presentation, image, created FROM user ORDER BY created DESC LIMIT ?';
    $statement = mysqli_prepare($connection, $sql);
    
    if (!$statement) {
        error_log("Prepare misslyckades i get_newest_bloggers: " . mysqli_error($connection));
        return array();
    }
    
    mysqli_stmt_bind_param($statement, "i", $limit);
    
    if (!mysqli_stmt_execute($statement)) {
        error_log("Execute misslyckades i get_newest_bloggers: " . mysqli_stmt_error($statement));
        mysqli_stmt_close($statement);
        return array();
    }
    
    $result = get_result($statement);
    mysqli_stmt_close($statement);
    return $result;
}

function get_post($post_id, $userId = null) {
    try {
        $pdo = get_database_connection();
        $sql = $userId 
            ? "SELECT * FROM post WHERE id = ? AND userId = ?"
            : "SELECT p.*, u.username, u.image as user_image 
               FROM post p 
               JOIN users u ON p.userId = u.id 
               WHERE p.id = ?";
        
        $stmt = $pdo->prepare($sql);
        
        if ($userId) {
            $stmt->execute([$post_id, $userId]);
        } else {
            $stmt->execute([$post_id]);
        }
        
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Fel vid hämtning av inlägg: " . $e->getMessage());
        return null;
    }
}

function update_post($postId, $userId, $title, $content) {
    global $connection;
    $sql = 'UPDATE post SET title=?, content=? WHERE id=? AND userId=?';
    $statement = mysqli_prepare($connection, $sql);
    
    if (!$statement) {
        error_log("Prepare misslyckades i update_post: " . mysqli_error($connection));
        return false;
    }
    
    mysqli_stmt_bind_param($statement, "ssii", $title, $content, $postId, $userId);
    $result = mysqli_stmt_execute($statement);
    
    if (!$result) {
        error_log("Execute misslyckades i update_post: " . mysqli_stmt_error($statement));
    }
    
    mysqli_stmt_close($statement);
    return $result;
}

function add_post_image($filename, $description, $postId) {
    global $connection;
    $sql = 'INSERT INTO image (filename, description, postId) VALUES (?, ?, ?)';
    $statement = mysqli_prepare($connection, $sql);
    
    if (!$statement) {
        error_log("Prepare misslyckades i add_post_image: " . mysqli_error($connection));
        return false;
    }
    
    mysqli_stmt_bind_param($statement, "ssi", $filename, $description, $postId);
    $result = mysqli_stmt_execute($statement);
    
    if (!$result) {
        error_log("Execute misslyckades i add_post_image: " . mysqli_stmt_error($statement));
    }
    
    mysqli_stmt_close($statement);
    return $result;
}

function get_post_images($postId) {
    global $connection;
    $sql = 'SELECT * FROM image WHERE postId=? ORDER BY created DESC';
    $statement = mysqli_prepare($connection, $sql);
    
    if (!$statement) {
        error_log("Prepare misslyckades i get_post_images: " . mysqli_error($connection));
        return array();
    }
    
    mysqli_stmt_bind_param($statement, "i", $postId);
    
    if (!mysqli_stmt_execute($statement)) {
        error_log("Execute misslyckades i get_post_images: " . mysqli_stmt_error($statement));
        mysqli_stmt_close($statement);
        return array();
    }
    
    $result = get_result($statement);
    mysqli_stmt_close($statement);
    return $result;
}

function get_blogger_posts($blogger_id) {
    try {
        $pdo = get_database_connection();
        $stmt = $pdo->prepare("
            SELECT * FROM post 
            WHERE userId = ? 
            ORDER BY created DESC
        ");
        $stmt->execute([$blogger_id]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Fel vid hämtning av bloggarens inlägg: " . $e->getMessage());
        return [];
    }
}

function get_latest_post($blogger_id = null) {
    try {
        $pdo = get_database_connection();
        $sql = "
            SELECT p.*, u.username 
            FROM post p 
            JOIN users u ON p.userId = u.id 
        ";
        $params = [];
        
        if ($blogger_id) {
            $sql .= " WHERE p.userId = ?";
            $params[] = $blogger_id;
        }
        
        $sql .= " ORDER BY p.created DESC LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Fel vid hämtning av senaste inlägg: " . $e->getMessage());
        return null;
    }
}

function get_blogger_info($blogger_id) {
    try {
        $pdo = get_database_connection();
        $stmt = $pdo->prepare("
            SELECT u.*, COUNT(p.id) as post_count 
            FROM users u 
            LEFT JOIN post p ON u.id = p.userId 
            WHERE u.id = ? 
            GROUP BY u.id
        ");
        $stmt->execute([$blogger_id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Fel vid hämtning av bloggarinfo: " . $e->getMessage());
        return null;
    }
}

function create_post($title, $content, $userId, $image = null) {
    global $connection;
    $sql = 'INSERT INTO post (title, content, userId, image_filename) VALUES (?, ?, ?, ?)';
    $statement = mysqli_prepare($connection, $sql);
    
    if (!$statement) {
        error_log("Prepare misslyckades i create_post: " . mysqli_error($connection));
        return false;
    }
    
    mysqli_stmt_bind_param($statement, "ssis", $title, $content, $userId, $image);
    $result = mysqli_stmt_execute($statement);
    
    if (!$result) {
        error_log("Execute misslyckades i create_post: " . mysqli_stmt_error($statement));
        mysqli_stmt_close($statement);
        return false;
    }
    
    $post_id = mysqli_insert_id($connection);
    mysqli_stmt_close($statement);
    return $post_id;
}

function verify_post_owner($postId, $userId) {
    global $connection;
    $sql = 'SELECT 1 FROM post WHERE id = ? AND userId = ?';
    $statement = mysqli_prepare($connection, $sql);
    
    if (!$statement) {
        error_log("Prepare misslyckades i verify_post_owner: " . mysqli_error($connection));
        return false;
    }
    
    mysqli_stmt_bind_param($statement, "ii", $postId, $userId);
    mysqli_stmt_execute($statement);
    mysqli_stmt_store_result($statement);
    $exists = mysqli_stmt_num_rows($statement) > 0;
    mysqli_stmt_close($statement);
    
    return $exists;
}

function upload_image($file, $userId) {
    $upload_dir = 'uploads/images/';
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    // Validera filtyp
    if (!in_array($file['type'], $allowed_types)) {
        return ['error' => 'Endast JPG, PNG och GIF-bilder är tillåtna.'];
    }
    
    // Validera filstorlek
    if ($file['size'] > $max_size) {
        return ['error' => 'Bilden får inte vara större än 5MB.'];
    }
    
    // Skapa unikt filnamn
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $unique_filename = uniqid($userId . '_') . '.' . $file_extension;
    $upload_path = $upload_dir . $unique_filename;
    
    // Flytta filen
    if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
        return ['error' => 'Kunde inte ladda upp filen.'];
    }
    
    return ['filename' => $unique_filename];
}

function delete_image($imageId, $userId) {
    global $connection;
    
    // Först verifiera att bilden tillhör användarens inlägg
    $sql = 'SELECT i.filename FROM image i 
            JOIN post p ON i.postId = p.id 
            WHERE i.id = ? AND p.userId = ?';
    $statement = mysqli_prepare($connection, $sql);
    
    if (!$statement) {
        error_log("Prepare misslyckades i delete_image: " . mysqli_error($connection));
        return false;
    }
    
    mysqli_stmt_bind_param($statement, "ii", $imageId, $userId);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    $image = mysqli_fetch_assoc($result);
    mysqli_stmt_close($statement);
    
    if (!$image) {
        return false;
    }
    
    // Ta bort filen från filsystemet
    $file_path = 'uploads/images/' . $image['filename'];
    if (file_exists($file_path)) {
        unlink($file_path);
    }
    
    // Ta bort från databasen
    $sql = 'DELETE FROM image WHERE id = ?';
    $statement = mysqli_prepare($connection, $sql);
    
    if (!$statement) {
        error_log("Prepare misslyckades vid borttagning av bild: " . mysqli_error($connection));
        return false;
    }
    
    mysqli_stmt_bind_param($statement, "i", $imageId);
    $result = mysqli_stmt_execute($statement);
    mysqli_stmt_close($statement);
    
    return $result;
}

function sanitize_input($input) {
    if (is_array($input)) {
        return array_map('sanitize_input', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}
