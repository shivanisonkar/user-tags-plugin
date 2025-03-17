<?php
/*
Plugin Name: User Tags for WordPress Users
Description: A plugin to categorize users using custom taxonomies.
Version: 1.0
Author: Your Name
*/

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Include required files
require_once plugin_dir_path(__FILE__) . 'user-tags-functions.php';

// Enqueue scripts & styles
function user_tags_enqueue_scripts() {
    wp_enqueue_script('select2', 'https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js', array('jquery'), null, true);
    wp_enqueue_style('select2-style', 'https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css');
    wp_enqueue_script('select2-init', plugin_dir_url(__FILE__) . 'assets/js/select2-init.js', array('jquery', 'select2'), null, true);
}
add_action('admin_enqueue_scripts', 'user_tags_enqueue_scripts');
?>
