<?php
/**
 * Hanterar bilduppladdningar för blogginlägg
 */

function handle_image_upload($file, $post_id) {
    $upload_dir = '../uploads/images/';
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_size = 5 * 1024 * 1024; // 5MB

    // Validera filtyp
    if (!in_array($file['type'], $allowed_types)) {
        throw new Exception('Endast JPG, PNG och GIF-bilder är tillåtna.');
    }

    // Validera filstorlek
    if ($file['size'] > $max_size) {
        throw new Exception('Bilden får inte vara större än 5MB.');
    }

    // Skapa ett unikt filnamn
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $unique_filename = $post_id . '_' . uniqid() . '.' . $file_extension;
    $upload_path = $upload_dir . $unique_filename;

    // Flytta filen till uppladdningsmappen
    if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
        throw new Exception('Det gick inte att ladda upp bilden. Försök igen.');
    }

    return $unique_filename;
}

function delete_post_image($image_filename) {
    $file_path = '../uploads/images/' . $image_filename;
    if (file_exists($file_path)) {
        unlink($file_path);
    }
}

function get_image_path($image_filename) {
    return '/uploads/images/' . $image_filename;
}
?> 