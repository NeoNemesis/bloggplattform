<?php
/**
 * CSRF Protection Functions
 */

/**
 * Genererar en ny CSRF-token och lagrar den i session
 */
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validerar CSRF-token
 */
function verify_csrf_token($token) {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    
    $result = hash_equals($_SESSION['csrf_token'], $token);
    
    // Förstör token efter användning för att förhindra replay-attacker
    unset($_SESSION['csrf_token']);
    
    return $result;
} 