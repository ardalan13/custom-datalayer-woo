<?php

require_once plugin_dir_path(__FILE__) . 'helper-functions.php';


if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
// Load all event files
$event_files = glob(plugin_dir_path( __FILE__ ) . 'events/*.php');
foreach ($event_files as $file) {
    require_once $file;
}
?>
