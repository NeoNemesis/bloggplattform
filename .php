<?php
// Aktivera felrapportering för debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Inkludera databasinställningar
require_once('db_config.php');

echo "<h2>Testar databasanslutning</h2>";

// Försök ansluta till databasen
$connection = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

// Kontrollera anslutningen
if (!$connection) {
    die("Anslutning misslyckades: " . mysqli_connect_error());
}

echo "Anslutning lyckades!<br>";

// Visa databasversion och värdnamn
if ($result = mysqli_query($connection, "SELECT VERSION() as version")) {
    $row = mysqli_fetch_assoc($result);
    echo "Databasversion: " . $row['version'] . "<br>";
    mysqli_free_result($result);
}

echo "Databasvärd: " . mysqli_get_host_info($connection) . "<br>";

// Testa att hämta data från user-tabellen
echo "<h3>Testar att hämta data från user-tabellen:</h3>";

$query = "SELECT * FROM user LIMIT 5";
$result = mysqli_query($connection, $query);

if ($result) {
    if (mysqli_num_rows($result) > 0) {
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Användarnamn</th></tr>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['username']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "Inga användare hittades i databasen.";
    }
    mysqli_free_result($result);
} else {
    echo "Fel vid hämtning av data: " . mysqli_error($connection);
}

// Stäng anslutningen
mysqli_close($connection);
?>