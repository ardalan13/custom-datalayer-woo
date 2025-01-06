<?php
// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

add_action('wp_enqueue_scripts', function () {
    // Check if this is the order received page
    if (is_order_received_page()) {
        // Get the order object
        $order = wc_get_order(get_query_var('order-received'));
        if ($order) {
            // Prepare purchase data for dataLayer
            $order_items = [];
            foreach ($order->get_items() as $item_id => $item) {
                $product = $item->get_product();
                if ($product) {
                    $order_items[] = [
                        'item_id' => $item->get_product_id(),
                        'item_name' => $item->get_name(),
                        'sku' => CustomDatalayerWooHelperFunctions::get_product_sku($product),
                        'price' => floatval($item->get_total()),
                        'quantity' => intval($item->get_quantity()),
                        'item_category' => CustomDatalayerWooHelperFunctions::get_main_product_category($item->get_product_id()),
                        'item_category2' => CustomDatalayerWooHelperFunctions::get_secondary_product_category($item->get_product_id()),
                        'item_category3' => CustomDatalayerWooHelperFunctions::get_tertiary_product_category($item->get_product_id()),
                    ];
                }
            }

            // Prepare customer and traffic source data
            $customer_info = wp_json_encode(CustomDatalayerWooHelperFunctions::get_customer_info(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
            $traffic_source = wp_json_encode(CustomDatalayerWooHelperFunctions::get_traffic_source_info(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);

            // Prepare inline script for dataLayer
            $inline_script = "
                window.dataLayer = window.dataLayer || [];
                if (!sessionStorage.getItem('orderSent_' + '" . esc_js($order->get_id()) . "')) {
                    dataLayer.push({
                        'event': 'purchase',
                        'ecommerce': {
                            'transaction_id': '" . esc_js($order->get_id()) . "',
                            'value': " . floatval($order->get_total()) . ",
                            'currency': 'USD',
                            'tax': " . floatval($order->get_total_tax()) . ",
                            'shipping': " . floatval($order->get_shipping_total()) . ",
                            'coupon': '" . (!empty($order->get_coupon_codes()) ? esc_js(implode(", ", $order->get_coupon_codes())) : 'none') . "',
                            'items': " . wp_json_encode($order_items, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) . "
                        },
                        'customer': $customer_info,
                        'traffic_source': $traffic_source
                    });
                    sessionStorage.setItem('orderSent_' + '" . esc_js($order->get_id()) . "', 'true');
                }
            ";

            // Register and enqueue the inline script
            wp_register_script('custom_datalayer_woo_purchase_js', false, [], null, true);
            wp_enqueue_script('custom_datalayer_woo_purchase_js');
            wp_add_inline_script('custom_datalayer_woo_purchase_js', $inline_script);
        }
    }
});
