<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php wp_title(); ?></title>
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<!-- Page Header-->
 <header class="masthead" style="background-image: url('assets/img/home-bg.jpg')">
            <div class="overlay"></div>
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 col-md-10 mx-auto">
                        <div class="site-heading">
                            <h1>Custom tema</h1>
                            <span class="subheading">A Blog Theme by Start Bootstrap</span>
                        </div>
                    </div>
                </div>
            </div>

    <nav class="navbar fixed-top" id="mainNav">
        <div class="container">
            <a class="navbar-brand" href="index.php">Simple WP Theme</a>
            <?php
                wp_nav_menu(array(
                    'theme_location' => 'main-menu',
                    'container' => 'div',
                    'container_class' => 'main-menu-container',
                    'menu_class' => 'main-menu',
                ));
            ?>
        </div>
    </nav>
</header>

