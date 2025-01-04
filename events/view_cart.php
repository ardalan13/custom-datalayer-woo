<?php
add_action('wp_footer', function () {
    // Check if this is the cart page
    if (is_cart()) {
        ?>
        <script>
            // Ensure dataLayer is initialized
            window.dataLayer = window.dataLayer || [];

            // Push cart data to the dataLayer
            dataLayer.push({
                'event': 'view_cart', // The event name for viewing the cart
                'ecommerce': {
                    'currency': 'USD', // Set the currency
                    'value': parseFloat('<?php echo esc_js(WC()->cart->get_total('raw')); ?>'), // Total cart value
                    'items': [
                        <?php
                        // Loop through cart items and prepare data
                        foreach (WC()->cart->get_cart() as $cart_item) {
                            $product = $cart_item['data'];
                            if ($product) {
                                // Retrieve product details using helper functions
                                $product_id = esc_js($cart_item['product_id']);
                                $product_name = esc_js($product->get_name());
                                $product_price = esc_js($product->get_price());
                                $product_quantity = intval($cart_item['quantity']);
                                $main_category = esc_js(CustomDatalayerHelperFunctions::get_main_product_category($cart_item['product_id']));
                                $secondary_category = esc_js(CustomDatalayerHelperFunctions::get_secondary_product_category($cart_item['product_id']));
                                $tertiary_category = esc_js(CustomDatalayerHelperFunctions::get_tertiary_product_category($cart_item['product_id']));
                                ?>
                                {
                                    'item_id': '<?php echo $product_id; ?>', // Product ID
                                    'item_name': '<?php echo $product_name; ?>', // Product name
                                    'price': parseFloat('<?php echo $product_price; ?>'), // Product price (numeric)
                                    'quantity': <?php echo $product_quantity; ?>, // Product quantity (integer)
                                    'item_category': '<?php echo $main_category; ?>', // Main category
                                    'item_category2': '<?php echo $secondary_category; ?>', // Secondary category
                                    'item_category3': '<?php echo $tertiary_category; ?>' // Tertiary category
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
});
?>
