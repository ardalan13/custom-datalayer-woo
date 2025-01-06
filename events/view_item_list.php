<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Enqueue the script and add inline JavaScript for product category tracking
add_action('wp_enqueue_scripts', function () {
    // Check if this is a product category page
    if (is_product_category()) {
        // Register a placeholder script for inline JavaScript
        wp_register_script('custom_datalayer_woo_category_js', false, [], null, true);

        // Get the current category ID and name
        $category_id = esc_js(get_queried_object_id());
        $category_name = esc_js(single_term_title('', false));

        // Fetch products in the current category
        $products = wc_get_products(['category' => [$category_id]]);
        $items = [];

        // Prepare product data
        foreach ($products as $product) {
            if ($product) {
                $items[] = [
                    'item_id' => esc_js($product->get_id()),
                    'item_name' => esc_js($product->get_name()),
                    'price' => floatval($product->get_price()),
                    'item_category' => esc_js(CustomDatalayerWooHelperFunctions::get_main_product_category($product->get_id())),
                    'item_category2' => esc_js(CustomDatalayerWooHelperFunctions::get_secondary_product_category($product->get_id())),
                    'item_category3' => esc_js(CustomDatalayerWooHelperFunctions::get_tertiary_product_category($product->get_id())),
                ];
            }
        }

        // Encode the data to JSON format for inline JavaScript
        $items_json = wp_json_encode($items, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
        $customer_info = wp_json_encode(CustomDatalayerWooHelperFunctions::get_customer_info(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
        $traffic_source = wp_json_encode(CustomDatalayerWooHelperFunctions::get_traffic_source_info(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);

        // Inline JavaScript for the dataLayer
        $inline_script = "
            window.dataLayer = window.dataLayer || [];
            dataLayer.push({
                'event': 'view_item_list',
                'ecommerce': {
                    'item_list_name': '" . $category_name . "',
                    'item_list_id': '" . $category_id . "',
                    'items': $items_json
                },
                'customer': $customer_info,
                'traffic_source': $traffic_source
            });
        ";

        // Enqueue the script and add the inline code
        wp_enqueue_script('custom_datalayer_woo_category_js');
        wp_add_inline_script('custom_datalayer_woo_category_js', $inline_script);
    }
});
