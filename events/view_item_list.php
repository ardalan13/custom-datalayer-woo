<?php
add_action('wp_footer', function () {
    // Check if this is a product category page
    if (is_product_category()) {
        // Get the current category ID and name safely
        $category_id = esc_js(get_queried_object_id());
        $category_name = esc_js(single_term_title('', false));

        // Fetch products in the current category
        $products = wc_get_products(['category' => [$category_id]]);
        ?>
        <script>
            // Ensure dataLayer is initialized
            window.dataLayer = window.dataLayer || [];

            // Prepare product list data
            dataLayer.push({
                'event': 'view_item_list', // The event name for viewing a product list
                'ecommerce': {
                    'item_list_name': '<?php echo $category_name; ?>', // Escaped name of the product list (category name)
                    'item_list_id': '<?php echo $category_id; ?>', // Escaped ID of the product list (category ID)
                    'items': [
                        <?php
                        // Loop through products in the category and prepare data
                        foreach ($products as $product) {
                            if ($product) {
                                // Retrieve product details safely using helper functions
                                $product_id = esc_js($product->get_id());
                                $product_name = esc_js($product->get_name());
                                $product_price = esc_js($product->get_price());

                                // Use helper functions for category retrieval
                                $main_category = esc_js(CustomDatalayerHelperFunctions::get_main_product_category($product->get_id()));
                                $secondary_category = esc_js(CustomDatalayerHelperFunctions::get_secondary_product_category($product->get_id()));
                                $tertiary_category = esc_js(CustomDatalayerHelperFunctions::get_tertiary_product_category($product->get_id()));
                                ?>
                                {
                                    'item_id': '<?php echo $product_id; ?>', // Escaped Product ID
                                    'item_name': '<?php echo $product_name; ?>', // Escaped Product name
                                    'price': parseFloat('<?php echo $product_price; ?>'), // Escaped Product price (numeric)
                                    'item_category': '<?php echo $main_category; ?>', // Escaped Main category
                                    'item_category2': '<?php echo $secondary_category; ?>', // Escaped Secondary category
                                    'item_category3': '<?php echo $tertiary_category; ?>' // Escaped Tertiary category
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
