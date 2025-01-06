<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

add_action('wp_enqueue_scripts', function () {
    // Check if this is the cart page
    if (is_cart()) {
        // Prepare data for dataLayer
        $cart_items = [];
        foreach (WC()->cart->get_cart() as $cart_item) {
            $product = $cart_item['data'];
            if ($product) {
                $cart_items[] = [
                    'item_id' => $cart_item['product_id'],
                    'item_name' => $product->get_name(),
                    'price' => floatval($product->get_price()),
                    'quantity' => intval($cart_item['quantity']),
                    'item_category' => CustomDatalayerWooHelperFunctions::get_main_product_category($cart_item['product_id']),
                    'item_category2' => CustomDatalayerWooHelperFunctions::get_secondary_product_category($cart_item['product_id']),
                    'item_category3' => CustomDatalayerWooHelperFunctions::get_tertiary_product_category($cart_item['product_id']),
                ];
            }
        }

        // Prepare customer and traffic source data
        $customer_info = wp_json_encode(CustomDatalayerWooHelperFunctions::get_customer_info(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
        $traffic_source = wp_json_encode(CustomDatalayerWooHelperFunctions::get_traffic_source_info(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);

        // Prepare inline script for dataLayer
        $inline_script = "
            window.dataLayer = window.dataLayer || [];
            dataLayer.push({
                'event': 'view_cart',
                'ecommerce': {
                    'currency': 'USD',
                    'value': " . floatval(WC()->cart->get_total('raw')) . ",
                    'items': " . wp_json_encode($cart_items) . "
                },
                'customer': $customer_info,
                'traffic_source': $traffic_source
            });
        ";

        // Register a placeholder script for inline JavaScript
        wp_register_script('custom_datalayer_woo_cart_js', false, [], null, true);
        wp_enqueue_script('custom_datalayer_woo_cart_js');
        wp_add_inline_script('custom_datalayer_woo_cart_js', $inline_script);
    }
});
