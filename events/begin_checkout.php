<?php
// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Add action to wp_enqueue_scripts to handle DataLayer script inclusion
add_action('wp_enqueue_scripts', function () {
    if (is_checkout() && !is_order_received_page()) {
        // Retrieve applied coupons
        $coupons = WC()->cart->get_applied_coupons();

        // Prepare cart items data
        $cart_items = [];
        foreach (WC()->cart->get_cart() as $cart_item) {
            $product = $cart_item['data'];
            if ($product) {
                $cart_items[] = [
                    'item_id' => esc_js($cart_item['product_id']),
                    'item_name' => esc_js($product->get_name()),
                    'price' => floatval($product->get_price()),
                    'quantity' => intval($cart_item['quantity']),
                    'item_category' => esc_js(CustomDatalayerWooHelperFunctions::get_main_product_category($cart_item['product_id'])),
                    'item_category2' => esc_js(CustomDatalayerWooHelperFunctions::get_secondary_product_category($cart_item['product_id'])),
                    'item_category3' => esc_js(CustomDatalayerWooHelperFunctions::get_tertiary_product_category($cart_item['product_id'])),
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
                'event': 'begin_checkout',
                'ecommerce': {
                    'currency': 'USD',
                    'value': " . floatval(WC()->cart->get_total('raw')) . ",
                    'coupon': '" . esc_js(!empty($coupons) ? implode(", ", $coupons) : 'none') . "',
                    'items': " . wp_json_encode($cart_items) . "
                },
                'customer': $customer_info,
                'traffic_source': $traffic_source
            });
        ";

        // Register and enqueue the inline script
        wp_register_script('custom_datalayer_woo_begin_checkout_js', false, [], null, true);
        wp_enqueue_script('custom_datalayer_woo_begin_checkout_js');
        wp_add_inline_script('custom_datalayer_woo_begin_checkout_js', $inline_script);
    }
});
