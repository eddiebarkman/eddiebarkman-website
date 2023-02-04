<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package Bizworx
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
  <head>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
	
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
	
	<?php if ( get_theme_mod('site_favicon') ) : ?>
		<link rel="shortcut icon" href="<?php echo esc_url(get_theme_mod('site_favicon')); ?>" />
	<?php endif; ?>

	<?php wp_head(); ?>
  </head>

  <body <?php body_class(); ?>>
  
  <?php do_action('bizworx_before_site'); ?>
	
    <header id="masthead"  class="site-header" role="banner">
		<div class="head-wrap banner-background">
			<div class="container">
				<div class="row">
					<div class="col-md-6 col-sm-6 col-xs-12">
						<?php if ( get_theme_mod('site_logo') ) : ?>
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php bloginfo('name'); ?>"><img class="site-logo" src="<?php echo esc_url(get_theme_mod('site_logo')); ?>" alt="<?php bloginfo('name'); ?>" /></a>
						<?php else : ?>
							<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
							<h2 class="site-description"><?php bloginfo( 'description' ); ?></h2>	        
						<?php endif; ?>
					</div>
					<div class="col-md-6 col-sm-6 col-xs-12">
						<div class="btn-menu"></div>
						<nav id="site-navigation" class="site-navigation" role="navigation">
							<?php wp_nav_menu( array( 'theme_location' => 'primary' ) ); ?>
						</nav><!-- #site-navigation -->
					</div>
				</div>
			</div>
		</div>
    </header>
	
	<div class="bizworx-banner-area">
		<?php bizworx_header_background(); ?>
		<?php bizworx_header_video(); ?>
	</div>
	
	<div id="content" class="page-wrap">
		<div class="content-wrapper container">
		<div class="row">