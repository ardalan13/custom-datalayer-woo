<?php
add_action('wp_footer', function () {
    // Check if this is a product page
    if (is_product()) {
        global $product;

        // Ensure the product object is available
        if ($product) {
            // Safely retrieve product data using helper functions
            $product_id = esc_js($product->get_id());
            $product_name = esc_js($product->get_name());
            $product_price = esc_js($product->get_price());

            // Retrieve product categories using helper functions
            $main_category = esc_js(CustomDatalayerHelperFunctions::get_main_product_category($product->get_id()));
            $secondary_category = esc_js(CustomDatalayerHelperFunctions::get_secondary_product_category($product->get_id()));
            $tertiary_category = esc_js(CustomDatalayerHelperFunctions::get_tertiary_product_category($product->get_id()));

            // Retrieve product variant (e.g., attributes)
            $attributes = $product->get_attributes();
            $item_variant = '';
            if (!empty($attributes)) {
                foreach ($attributes as $attribute) {
                    // Safely concatenate attribute names and options
                    $item_variant .= esc_js($attribute->get_name()) . ': ' . esc_js(implode(', ', $attribute->get_options())) . '; ';
                }
                $item_variant = rtrim($item_variant, '; ');
            }
            ?>
            <script>
                // Initialize dataLayer if not already initialized
                window.dataLayer = window.dataLayer || [];

                // Push product view data to dataLayer
                dataLayer.push({
                    'event': 'view_item',
                    'ecommerce': {
                        'currency': 'USD', // Set the currency
                        'value': parseFloat('<?php echo $product_price; ?>'), // Numeric value for price
                        'items': [{
                            'item_id': '<?php echo $product_id; ?>', // Escaped product ID
                            'item_name': '<?php echo $product_name; ?>', // Escaped product name
                            'price': parseFloat('<?php echo $product_price; ?>'), // Product price
                            'item_category': '<?php echo $main_category; ?>', // Main category
                            'item_category2': '<?php echo $secondary_category; ?>', // Secondary category
                            'item_category3': '<?php echo $tertiary_category; ?>', // Tertiary category
                            'item_variant': '<?php echo esc_js($item_variant); ?>' // Product variant
                        }]
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
