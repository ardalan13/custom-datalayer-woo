<?php
add_action('wp_footer', function () {
    ?>
    <script>
        jQuery(function($) {
            // Fetch customer and traffic source information using PHP helper functions
            var customerInfo = <?php echo wp_json_encode(CustomDatalayerHelperFunctions::get_customer_info(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
            var trafficSource = <?php echo wp_json_encode(CustomDatalayerHelperFunctions::get_traffic_source_info(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;

            // Listen for the "added_to_cart" event
            $(document.body).on('added_to_cart', function(event, fragments, cart_hash, $button) {
                var $product = $button.closest('.product');
                if ($product.length > 0) {
                    // Get product details
                    var product_id = $product.data('id') || 'unknown';
                    var product_name = $product.find('.product_title').text().trim() || 'unknown';
                    var product_price = parseFloat($product.find('.woocommerce-Price-amount').first().text().replace(/[^\d.-]/g, '').trim()) || 0;

                    // Retrieve product categories using helper functions
                    var main_category = $product.data('main-category') || 'Uncategorized';
                    var secondary_category = $product.data('secondary-category') || '';
                    var tertiary_category = $product.data('tertiary-category') || '';

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
                        'customer': customerInfo, // Add customer information
                        'traffic_source': trafficSource // Add traffic source information
                    });
                }
            });
        });
    </script>
    <?php
});
