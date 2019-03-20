<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta charset="<?php bloginfo( 'charset' ); ?>" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="profile" href="http://gmpg.org/xfn/11" />
        <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
        <?php wp_head(); ?>
    </head>
<body <?php body_class(''); ?> itemtype="http://schema.org/WebPage" itemscope="itemscope">

<!-- #ht-site-container -->
<div id="ht-site-container" class="clearfix ht-layout-<?php echo get_theme_mod('ht_setting_widthmode', 'fullwidth') ?>">

    <!-- #header -->
    <header id="site-header" class="clearfix" role="banner" itemtype="http://schema.org/WPHeader" itemscope="itemscope">
    <div class="ht-container clearfix">

    <!-- #logo -->
    <div id="logo">
        <a title="<?php bloginfo( 'name' ); ?>" href="<?php echo home_url(); ?>">
            <img alt="<?php bloginfo( 'name' ); ?>" src="<?php echo ht_theme_logo(); ?>" />
                <?php if ( is_front_page() ) { ?>
                    <h1 class="site-title" itemprop="headline"><?php bloginfo( 'name' ); ?></h1>
                <?php } ?>
        </a>
    </div>
    <!-- /#logo -->

    <?php if ( has_nav_menu( 'primary-nav' ) ) { ?>
        <!-- #primary-nav -->
        <nav id="nav-primary" role="navigation" itemtype="http://schema.org/SiteNavigationElement" itemscope="itemscope">
            <button id="ht-nav-toggle"><span><?php _e('Menu','helpguru') ?></span></button>
            <?php wp_nav_menu( array('theme_location' => 'primary-nav', 'menu_id' => false, 'menu_class' => false, 'container_id' => 'nav-primary-menu', 'container_class' => '' )); ?>
        </nav>
        <!-- /#primary-nav -->
    <?php } ?>

    </div>
    </header>
    <!-- /#header -->