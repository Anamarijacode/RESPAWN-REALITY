<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">

    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <nav class="navbar navbar-expand-lg shadow-sm" style="background-color: #2f2f2f !important;">
        <div class="container-fluid">
            <!-- Brand Name -->
            <a class="navbar-brand" href="<?php echo home_url(); ?>" style="font-size: 2rem; font-weight: bold; letter-spacing: 2px; color: #ffffff;">
                <?php bloginfo('name'); ?>
            </a>
            <!-- Navbar Toggler for Mobile -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Nav links -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'container'      => 'ul',
                    'menu_class'     => 'navbar-nav ms-auto',
                    'fallback_cb'    => false,
                    'depth'          => 3, 
                    'walker'         => new WP_Bootstrap_Navwalker(),
                ));
                
                ?>
            </div>
        </div>
    </nav>
