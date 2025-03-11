<?php
/**
 * Plugin Name: Custom Image Sizes Manager
 * Description: Thêm hoặc loại bỏ kích thước ảnh tùy chỉnh trong WordPress với tùy chọn bật/tắt trong cài đặt Media.
 * Plugin URI: https://nevothemes.com/
 * Version: 1.0.0
 * Author: NevoThemes
 * Author URI: https://nevothemes.com/
 */

// Ngăn truy cập trực tiếp
if (!defined('ABSPATH')) {
    exit;
}

// Thêm tùy chọn vào Media Settings
function nevo_custom_image_sizes_settings() {
    add_settings_section(
        'custom_image_sizes_section',
        'Custom Image Sizes',
        '__return_false',
        'media'
    );

    add_settings_field(
        'enable_custom_image_sizes',
        'Bật tính năng add_image_size()',
        'custom_image_sizes_checkbox',
        'media',
        'custom_image_sizes_section',
        ['label_for' => 'enable_custom_image_sizes']
    );

    add_settings_field(
        'disable_default_sizes',
        'Vô hiệu hóa kích thước ảnh mặc định',
        'custom_image_sizes_checkbox',
        'media',
        'custom_image_sizes_section',
        ['label_for' => 'disable_default_sizes']
    );

    register_setting('media', 'enable_custom_image_sizes');
    register_setting('media', 'disable_default_sizes');
}
add_action('admin_init', 'nevo_custom_image_sizes_settings');

// Hàm hiển thị checkbox
function nevo_custom_image_sizes_checkbox($args) {
    $option = get_option($args['label_for'], false);
    echo '<input type="checkbox" name="' . esc_attr($args['label_for']) . '" id="' . esc_attr($args['label_for']) . '" value="1" ' . checked(1, $option, false) . '>';
}

// Thêm kích thước ảnh nếu được bật
function nevo_add_custom_image_sizes() {
    if (get_option('enable_custom_image_sizes')) {
        $medium_width  = get_option('medium_size_w');
        $medium_height = get_option('medium_size_h');

        $large_width  = get_option('large_size_w');
        $large_height = get_option('large_size_h');

        add_image_size('custom-medium', $medium_width, $medium_height, true);
        add_image_size('custom-large', $large_width, $large_height, true);
    }
}
add_action('after_setup_theme', 'nevo_add_custom_image_sizes');

// Xóa kích thước mặc định nếu tùy chọn được bật
function nevo_disable_default_image_sizes($sizes) {
    if (get_option('disable_default_sizes')) {
        $sizes_to_remove = ['medium', 'medium_large', 'large', '1536x1536', '2048x2048'];
        return array_diff($sizes, $sizes_to_remove);
    }
    return $sizes;
}
add_filter('intermediate_image_sizes', 'nevo_disable_default_image_sizes');

function nevo_disable_advanced_image_sizes($sizes) {
    if (get_option('disable_default_sizes')) {
        unset($sizes['medium_large']);
        unset($sizes['large']);
        unset($sizes['1536x1536']);
        unset($sizes['2048x2048']);
    }
    return $sizes;
}
add_filter('intermediate_image_sizes_advanced', 'nevo_disable_advanced_image_sizes');
