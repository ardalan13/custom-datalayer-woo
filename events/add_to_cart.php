<?php
// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Add action to enqueue scripts and inline DataLayer logic
add_action('wp_enqueue_scripts', function () {
    // Only enqueue the script if WooCommerce is active
    if (is_product() || is_shop() || is_cart() || is_checkout()) {
        // Retrieve customer and traffic source information
        $customer_info = wp_json_encode(CustomDatalayerWooHelperFunctions::get_customer_info(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
        $traffic_source = wp_json_encode(CustomDatalayerWooHelperFunctions::get_traffic_source_info(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);

        // Prepare the inline script
        $inline_script = "
            jQuery(function($) {
                var customerInfo = $customer_info;
                var trafficSource = $traffic_source;

                // Listen for the 'added_to_cart' event
                $(document.body).on('added_to_cart', function(event, fragments, cart_hash, button) {
                    var product = $(button).closest('.product');
                    if (product.length > 0) {
                        // Get product details
                        var product_id = product.data('id') || 'unknown';
                        var product_name = product.find('.product_title').text().trim() || 'unknown';
                        var product_price = parseFloat(product.find('.woocommerce-Price-amount').first().text().replace(/[^\d.-]/g, '').trim()) || 0;

                        // Retrieve product categories using HTML5 data attributes or fallback
                        var main_category = product.data('main-category') || 'Uncategorized';
                        var secondary_category = product.data('secondary-category') || '';
                        var tertiary_category = product.data('tertiary-category') || '';

                        // Push data to the dataLayer
                        window.dataLayer = window.dataLayer || [];
                        dataLayer.push({
                            'event': 'add_to_cart',
                            'ecommerce': {
                                'currency': 'USD',
                                'value': product_price,
                                'items': [{
                                    'item_id': product_id,
                                    'item_name': product_name,
                                    'price': product_price,
                                    'item_category': main_category,
                                    'item_category2': secondary_category,
                                    'item_category3': tertiary_category
                                }]
                            },
                            'customer': customerInfo,
                            'traffic_source': trafficSource
                        });
                    }
                });
            });
        ";

        // Register and enqueue the inline script
        wp_register_script('custom_datalayer_woo_add_to_cart_js', false, [], null, true);
        wp_enqueue_script('custom_datalayer_woo_add_to_cart_js');
        wp_add_inline_script('custom_datalayer_woo_add_to_cart_js', $inline_script);
    }
});
