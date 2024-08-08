<?php
/*
Plugin Name: Blog Post Submission
Description: A plugin to allow users to submit blog posts from the frontend.
Version: 1.0
Author: CPM
Text Domain: blog_post_submission
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('BPS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('BPS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('BPS_PLUGIN_VERSION', '1.0');

// Include the public form handler
require_once BPS_PLUGIN_DIR . 'public/blog-post-submission-form.php';

// Activation hook
function bps_plugin_activate()
{
    // Activation code here...
}
register_activation_hook(__FILE__, 'bps_plugin_activate');

// Deactivation hook
function bps_plugin_deactivate()
{
    // Deactivation code here...
}
register_deactivation_hook(__FILE__, 'bps_plugin_deactivate');

// Enqueue styles and scripts
function bps_enqueue_scripts()
{
    wp_enqueue_style('bps-styles', BPS_PLUGIN_URL . 'public/css/styles.css', array(), BPS_PLUGIN_VERSION);
    wp_enqueue_script('bps-scripts', BPS_PLUGIN_URL . 'public/js/scripts.js', array('jquery'), BPS_PLUGIN_VERSION, true);
}
add_action('wp_enqueue_scripts', 'bps_enqueue_scripts');
