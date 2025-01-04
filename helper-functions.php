<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('CustomDatalayerHelperFunctions')) {

    class CustomDatalayerHelperFunctions {

        /**
         * Get the main category of a product
         * @param int $product_id
         * @return string
         */
        public static function get_main_product_category($product_id) {
            $categories = wp_get_post_terms($product_id, 'product_cat');
            if (!empty($categories)) {
                return esc_html($categories[0]->name);
            }
            return 'Uncategorized';
        }

        /**
         * Get the secondary product category
         * @param int $product_id
         * @return string
         */
        public static function get_secondary_product_category($product_id) {
            $categories = wp_get_post_terms($product_id, 'product_cat');
            return isset($categories[1]) ? esc_html($categories[1]->name) : '';
        }

        /**
         * Get the tertiary product category
         * @param int $product_id
         * @return string
         */
        public static function get_tertiary_product_category($product_id) {
            $categories = wp_get_post_terms($product_id, 'product_cat');
            return isset($categories[2]) ? esc_html($categories[2]->name) : '';
        }

        /**
         * Get the SKU of a product
         * @param WC_Product $product
         * @return string
         */
        public static function get_product_sku($product) {
            if ($product) {
                $sku = $product->get_sku();
                return !empty($sku) ? esc_html($sku) : 'N/A';
            }
            return 'N/A';
        }

        /**
         * Get customer information dynamically and hash it
         * @return array
         */
        public static function get_customer_info() {
            if (!is_user_logged_in()) {
                return []; // Return empty array for non-logged-in users
            }

            $user_id = get_current_user_id();

            // Retrieve and hash customer details
            return [
                'id_hash' => hash('sha256', $user_id),
                'billing' => [
                    'first_name_hash' => hash('sha256', sanitize_text_field(get_user_meta($user_id, 'billing_first_name', true))),
                    'last_name_hash' => hash('sha256', sanitize_text_field(get_user_meta($user_id, 'billing_last_name', true))),
                    'company_hash' => hash('sha256', sanitize_text_field(get_user_meta($user_id, 'billing_company', true))),
                    'address_1_hash' => hash('sha256', sanitize_text_field(get_user_meta($user_id, 'billing_address_1', true))),
                    'address_2_hash' => hash('sha256', sanitize_text_field(get_user_meta($user_id, 'billing_address_2', true))),
                    'city_hash' => hash('sha256', sanitize_text_field(get_user_meta($user_id, 'billing_city', true))),
                    'state_hash' => hash('sha256', sanitize_text_field(get_user_meta($user_id, 'billing_state', true))),
                    'postcode_hash' => hash('sha256', sanitize_text_field(get_user_meta($user_id, 'billing_postcode', true))),
                    'country_hash' => hash('sha256', sanitize_text_field(get_user_meta($user_id, 'billing_country', true))),
                    'email_hash' => hash('sha256', sanitize_email(wp_get_current_user()->user_email)),
                    'phone_hash' => hash('sha256', sanitize_text_field(get_user_meta($user_id, 'billing_phone', true))),
                    'vat_number_hash' => hash('sha256', sanitize_text_field(get_user_meta($user_id, 'billing_vat_number', true))), // Example additional field
                ],
            ];
        }

        /**
         * Get traffic source information
         * @return array
         */
        public static function get_traffic_source_info() {
            $utm_source = isset($_GET['utm_source']) ? sanitize_text_field(wp_unslash($_GET['utm_source'])) : 'direct';
            $utm_medium = isset($_GET['utm_medium']) ? sanitize_text_field(wp_unslash($_GET['utm_medium'])) : 'organic';
            $utm_campaign = isset($_GET['utm_campaign']) ? sanitize_text_field(wp_unslash($_GET['utm_campaign'])) : 'none';

            return [
                'utm_source' => $utm_source,
                'utm_medium' => $utm_medium,
                'utm_campaign' => $utm_campaign
            ];
        }
    }
}
