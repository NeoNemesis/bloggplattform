<?php
// Definiera projektets rotsökväg
define('PROJECT_ROOT', dirname(__DIR__) . '/');

// Bestäm bassökväg baserat på aktuell mapp
function get_base_path() {
    $script_path = $_SERVER['PHP_SELF'];
    $base_path = '';
    
    // Justera sökvägen baserat på var filen ligger
    if (strpos($script_path, '/admin/') !== false) {
        $base_path = '../';
    } elseif (strpos($script_path, '/auth/') !== false) {
        $base_path = '../';
    } elseif (strpos($script_path, '/pages/') !== false) {
        $base_path = '../';
    }
    
    return $base_path;
}

// Konstanter för vanliga mappar
define('UPLOADS_DIR', PROJECT_ROOT . 'uploads/');
define('IMAGES_DIR', PROJECT_ROOT . 'images/');
define('CSS_DIR', PROJECT_ROOT . 'css/');
define('JS_DIR', PROJECT_ROOT . 'javascript/');
define('INCLUDES_DIR', PROJECT_ROOT . 'includes/');

// Funktion för att få korrekt URL till en uppladdad bild
function get_upload_url($filename) {
    return get_base_path() . 'uploads/' . $filename;
}

// Funktion för att få korrekt URL till en webbplatsbild
function get_image_url($filename) {
    return get_base_path() . 'images/' . $filename;
}

// Funktion för att få korrekt URL till en CSS-fil
function get_css_url($filename) {
    return get_base_path() . 'css/' . $filename;
}

// Funktion för att få korrekt URL till en JavaScript-fil
function get_js_url($filename) {
    return get_base_path() . 'javascript/' . $filename;
}

// Funktion för att få korrekt URL till en PHP-sida
function get_page_url($page) {
    return get_base_path() . $page;
} 