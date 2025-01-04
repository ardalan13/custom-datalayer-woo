<?php
add_action('wp_footer', function () {
    // Check if this is the order received page
    if (is_order_received_page()) {
        // Get the order object
        $order = wc_get_order(get_query_var('order-received'));
        if ($order) {
            ?>
            <script>
                // Initialize dataLayer if not already initialized
                window.dataLayer = window.dataLayer || [];

                // Push purchase data to the dataLayer
                dataLayer.push({
                    'event': 'purchase', // Event name for purchase
                    'ecommerce': {
                        'transaction_id': '<?php echo esc_js($order->get_order_number()); ?>', // Order transaction ID
                        'value': parseFloat('<?php echo esc_js($order->get_total()); ?>'), // Total order value
                        'currency': 'USD', // Currency
                        'tax': parseFloat('<?php echo esc_js($order->get_total_tax()); ?>'), // Tax amount
                        'shipping': parseFloat('<?php echo esc_js($order->get_shipping_total()); ?>'), // Shipping cost
                        'coupon': '<?php echo esc_js(implode(", ", $order->get_coupon_codes()) ?: 'none'); ?>', // Applied coupons or 'none'
                        'items': [
                            <?php
                            // Loop through order items and prepare product data
                            foreach ($order->get_items() as $item_id => $item) {
                                $product = $item->get_product();
                                if ($product) {
                                    // Retrieve product details using helper functions
                                    $product_id = esc_js($item->get_product_id());
                                    $product_name = esc_js($item->get_name());
                                    $product_sku = esc_js(CustomDatalayerHelperFunctions::get_product_sku($product));
                                    $product_price = esc_js($item->get_total());
                                    $product_quantity = intval($item->get_quantity());
                                    $product_category = esc_js(CustomDatalayerHelperFunctions::get_main_product_category($item->get_product_id()));
                                    $product_category2 = esc_js(CustomDatalayerHelperFunctions::get_secondary_product_category($item->get_product_id()));
                                    $product_category3 = esc_js(CustomDatalayerHelperFunctions::get_tertiary_product_category($item->get_product_id()));
                                    ?>
                                    {
                                        'item_id': '<?php echo $product_id; ?>', // Product ID
                                        'item_name': '<?php echo $product_name; ?>', // Product name
                                        'sku': '<?php echo $product_sku; ?>', // Product SKU
                                        'price': parseFloat('<?php echo $product_price; ?>'), // Product price
                                        'quantity': <?php echo $product_quantity; ?>, // Product quantity
                                        'item_category': '<?php echo $product_category; ?>', // Main category
                                        'item_category2': '<?php echo $product_category2; ?>', // Secondary category
                                        'item_category3': '<?php echo $product_category3; ?>' // Tertiary category
                                    },
                                    <?php
                                }
                            } ?>
                        ]
                    },
                    'customer': <?php echo wp_json_encode(CustomDatalayerHelperFunctions::get_customer_info(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>, // Secure JSON encoding for customer info
                    'traffic_source': <?php echo wp_json_encode(CustomDatalayerHelperFunctions::get_traffic_source_info(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?> // Secure JSON encoding for traffic source info
                });
            </script>
            <?php
        }
    }
});
?>
