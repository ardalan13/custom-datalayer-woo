<?php

// Prevent direct access
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Load helper functions
$helper_functions_file = plugin_dir_path(__FILE__) . 'helper-functions.php';
if (file_exists($helper_functions_file) && is_readable($helper_functions_file)) {
    require_once $helper_functions_file;
}

// Load all event files
$event_files = glob(plugin_dir_path(__FILE__) . 'events/*.php');
if ($event_files) {
    foreach ($event_files as $file) {
        if (file_exists($file) && is_readable($file)) {
            require_once $file;
        }
    }
}
