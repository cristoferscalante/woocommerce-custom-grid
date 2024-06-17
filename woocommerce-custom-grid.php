<?php
/*
Plugin Name: WooCommerce Custom Grid
Description: Un plugin personalizado para mostrar productos en una cuadrícula con categorías.
Version: 1.0
Author: Tu Nombre
*/

if (!defined('ABSPATH')) {
    exit; // Salir si se accede directamente.
}

// Registrar el shortcode
add_shortcode('custom_product_grid', 'custom_product_grid_shortcode');

function custom_product_grid_shortcode($atts)
{
    ob_start();

    // Consultar las categorías de productos
    $product_categories = get_terms('product_cat');

    // Cargar el contenido HTML desde el archivo template.html
    $html = file_get_contents(plugin_dir_path(__FILE__) . 'template.html');

    // Insertar los botones de categoría
    $buttons_html = '';
    foreach ($product_categories as $category) {
        $buttons_html .= '<button class="category-button" data-category="' . $category->slug . '">' . $category->name . '</button>';
    }
    $html = str_replace('{{category_buttons}}', $buttons_html, $html);

    echo $html;

    ?>
    <script src="<?php echo plugins_url('scripts.js', __FILE__); ?>"></script>
    <?php

    return ob_get_clean();
}

// Manejar la solicitud AJAX para obtener productos
add_action('wp_ajax_fetch_products', 'fetch_products');
add_action('wp_ajax_nopriv_fetch_products', 'fetch_products');

function fetch_products()
{
    $category = sanitize_text_field($_GET['category']);

    $args = [
        'post_type' => 'product',
        'posts_per_page' => -1,
        'tax_query' => [
            [
                'taxonomy' => 'product_cat',
                'field' => 'slug',
                'terms' => $category,
            ],
        ],
    ];

    $query = new WP_Query($args);
    $products = [];

    while ($query->have_posts()) {
        $query->the_post();
        $product = wc_get_product(get_the_ID());
        $products[] = [
            'id' => $product->get_id(),
            'title' => get_the_title(),
            'description' => get_the_excerpt(),
            'price' => $product->get_price_html(),
            'image' => wp_get_attachment_url($product->get_image_id()),
        ];
    }

    wp_send_json(['products' => $products]);

    wp_die();
}

// Incluir el archivo CSS
add_action('wp_enqueue_scripts', 'custom_product_grid_styles');

function custom_product_grid_styles()
{
    wp_enqueue_style('custom-product-grid-styles', plugins_url('styles.css', __FILE__));
}
