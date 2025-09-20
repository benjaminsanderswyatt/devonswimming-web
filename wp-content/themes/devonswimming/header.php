<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>



    <div class="hero">
        
        <div class="hero-image-wrapper">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/hero.png" class="hero-img" alt="Devon Hero Image">
        </div>

        <div class="hero-waves" style="background-image: url('<?php echo get_template_directory_uri(); ?>/assets/images/waves.svg');"></div>
        
        <div class="hero-logo">
            <img
            src="<?php echo get_template_directory_uri(); ?>/assets/images/logo-1024x1024.png" 
            alt="Devon A.S.A. Logo"
            width="1024"
            height="1024"
            >
        </div>

    </div>


    <header class="header">

        <div class="burger-wrapper">
            <h1 class="header-title">Devon Swimming A.S.A.</h1>
            
            <!-- Burger icon -->
            <button
                class="menu-toggle"
                aria-controls="menu-panel"
                aria-expanded="false"
                >
                <span class="visually-hidden">Menu</span>
                
                <span class="burger" aria-hidden="true">
                    <span class="bar"></span>
                    <span class="bar"></span>
                    <span class="bar"></span>
                </span>
            </button>
        </div>

        <nav class="main-nav">
            <div class="menus-panel" id="menus-panel" aria-hidden="true">
                <?php
                    // Primary menu
                    wp_nav_menu([
                        'theme_location' => 'primary',
                        'menu_class' => 'nav-menu',
                        'container' => false,
                        'menu_id' => 'primary-menu',
                    ]);

                    // Links menu
                    if (has_nav_menu('links')) {
                    wp_nav_menu([
                        'theme_location' => 'links',
                        'menu_class'     => 'nav-social',
                        'container'      => false,
                        'depth'          => 1,
                        'walker'         => new Devon_Walker_Social_Icons(),
                    ]);
                }
                ?>
            </div>
        </nav>


    </header>