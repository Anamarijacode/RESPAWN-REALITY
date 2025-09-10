<?php
function simpleWP_theme_setup()
{

    // Podrška za istaknute slike
    add_theme_support('post-thumbnails');

    // Registracija bočnih traka
    register_sidebar([
        'name' => 'glavni-sidebar',
        'id' => 'glavni-sidebar',
        'description' => 'Prikaz zadnjih 5 objava na naslovnici',
        'before_widget' => '<div class="widget">',
        'after_widget' => '</div>',
    ]);

    for ($i = 1; $i <= 4; $i++) {
        register_sidebar([
            'name' => "Footer Sidebar $i",
            'id' => "footer-sidebar$i",
            'before_widget' => '<div class="footer-widget">',
            'after_widget' => '</div>',
        ]);
    }

    // Registracija menija
    function register_my_menus()
    {
        register_nav_menus(array(
            'main-menu' => 'main-menu'
        ));
    }
    add_action('init', 'register_my_menus');



    // povezivanje s css-om
    function simpleWP_enqueue_scripts()
    {
        wp_enqueue_style('main-styles', get_stylesheet_uri());
        wp_enqueue_style('styles.css',get_template_directory_uri().'/css/styles.css');


    // Učitajte JavaScript datoteke predloška
        wp_enqueue_script('bootstrap', get_template_directory_uri() . '/js/bootstrap.min.js', ['jquery'], null, true);
        wp_enqueue_script('main-script', get_template_directory_uri() . '/js/scripts.js', ['jquery'], null, true);
    }
    add_action('wp_enqueue_scripts', 'simpleWP_enqueue_scripts');

}
add_action('after_setup_theme', 'simpleWP_theme_setup');

