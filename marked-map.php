<?php
/**
 * Plugin Name: WP Marked Map
 * Description: Display Google Maps with coordinate-based markers using a simple shortcode.
 * Version: 1.0.6
 * Author: vladyslav10111
 * Text Domain: marked-map
 */
if (!defined('ABSPATH')) {
    exit;
}

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
} else {
    add_action('admin_notices', function () {
        echo '<div class="notice notice-error"><p><strong>WP Marked Map:</strong> Autoload file is missing. Please run <code>composer install</code>.</p></div>';
    });
    return;
}

use Vladyslav10111\MarkedMap\MarkedMapShortcode;
use Vladyslav10111\MarkedMap\MarkedMapAdmin;

add_action('plugins_loaded', function () {
    new MarkedMapAdmin();
    new MarkedMapShortcode();
});
