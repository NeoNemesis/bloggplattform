<?php
// Aktivera felrapportering för utvecklingsmiljön
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Databasanslutningsuppgifter för swplayground
define('DB_SERVER', 'localhost');
define('DB_USER', 'vilvic3');     // Ditt användarnamn för databasen
define('DB_PASS', 'rgsms6dzxwcbMitr');    // Ditt lösenord för databasen
define('DB_NAME', 'd0019e_vt25_vilvic3');  // Namnet på din databas

// Ställ in teckenkodning för databasen
define('DB_CHARSET', 'utf8mb4');  // Stödjer alla Unicode-tecken inklusive emojis
?> 