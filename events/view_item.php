<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

add_action('wp_enqueue_scripts', function () {
    // Check if this is a product page
    if (is_product()) {
        // Get the global product object
        global $product;

        // Ensure the product object is valid
        if (!isset($product) || !($product instanceof WC_Product)) {
            // Try to retrieve the product object again
            $product_id = get_the_ID(); // Get the current post ID
            $product = wc_get_product($product_id); // Retrieve product object
        }

        // Ensure we now have a valid product object
        if ($product && $product instanceof WC_Product) {
            // Safely retrieve product data
            $product_id = esc_js($product->get_id());
            $product_name = esc_js($product->get_name());
            $product_price = floatval($product->get_price());

            // Retrieve product categories using helper functions
            $main_category = esc_js(CustomDatalayerWooHelperFunctions::get_main_product_category($product->get_id()));
            $secondary_category = esc_js(CustomDatalayerWooHelperFunctions::get_secondary_product_category($product->get_id()));
            $tertiary_category = esc_js(CustomDatalayerWooHelperFunctions::get_tertiary_product_category($product->get_id()));

            // Retrieve product variant (e.g., attributes)
            $attributes = $product->get_attributes();
            $item_variant = '';
            if (!empty($attributes)) {
                foreach ($attributes as $attribute) {
                    // Check if attribute object is valid
                    if (is_object($attribute)) {
                        $item_variant .= esc_js($attribute->get_name()) . ': ' . esc_js(implode(', ', $attribute->get_options())) . '; ';
                    }
                }
                $item_variant = rtrim($item_variant, '; ');
            }

            // Retrieve customer info and traffic source
            $customer_info = wp_json_encode(CustomDatalayerWooHelperFunctions::get_customer_info(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
            $traffic_source = wp_json_encode(CustomDatalayerWooHelperFunctions::get_traffic_source_info(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);

            // Inline JavaScript for dataLayer
            $inline_script = "
                window.dataLayer = window.dataLayer || [];
                dataLayer.push({
                    'event': 'view_item',
                    'ecommerce': {
                        'currency': 'USD',
                        'value': {$product_price},
                        'items': [{
                            'item_id': '{$product_id}',
                            'item_name': '{$product_name}',
                            'price': {$product_price},
                            'item_category': '{$main_category}',
                            'item_category2': '{$secondary_category}',
                            'item_category3': '{$tertiary_category}',
                            'item_variant': '{$item_variant}'
                        }]
                    },
                    'customer': $customer_info,
                    'traffic_source': $traffic_source
                });
            ";

            // Register a placeholder script for inline JavaScript
            wp_register_script('custom_datalayer_woo_product_js', false, [], null, true);
            wp_enqueue_script('custom_datalayer_woo_product_js');
            wp_add_inline_script('custom_datalayer_woo_product_js', $inline_script);
        }
    }
});
