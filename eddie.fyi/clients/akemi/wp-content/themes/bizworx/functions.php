<?php
/**
 * Bizworx functions and definitions
 *
 * @package Bizworx
 */
if ( ! function_exists( 'bizworx_setup' ) ) :

function bizworx_setup() {
	
	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );
	add_image_size('large-thumb', 830);
	add_image_size('medium-thumb', 550, 400, true);
	add_image_size('small-thumb', 230);
	
	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' 	=> __( 'Primary Menu', 'bizworx' )
	) );
	
	// Content width
	global $content_width;
	if ( ! isset( $content_width ) ) {
		$content_width = 1170;
	}
	
	/*
	 * Enable support for Post Formats.
	 * See http://codex.wordpress.org/Post_Formats
	 */
	add_theme_support( 'post-formats', array(
		'aside',
		'image',
		'video',
		'quote',
		'link',
	) );
	
	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );
	
	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );
	
	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'bizworx_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );
	
	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );
	
}
endif;
add_action( 'after_setup_theme', 'bizworx_setup' );

function bizworx_preloader() {
	?>
	<div class="preloader">
	    <div class="spinner">
	        <div class="pre-bounce1"></div>
	        <div class="pre-bounce2"></div>
	    </div>
	</div>
	<?php
}
add_action('bizworx_before_site', 'bizworx_preloader');

/**
 * Blog layout
 */
function bizworx_blog_layout() {
	$layout = get_theme_mod('blog_layout','classic');
	return $layout;
}

if ( ! function_exists( 'bizworx_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function bizworx_posted_on() {
	$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
		$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
	}

	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_attr( get_the_modified_date( 'c' ) ),
		esc_html( get_the_modified_date() )
	);

	$posted_on = sprintf(
		_x( '%s', 'post date', 'bizworx' ),
		'<i class="fa fa-calendar"></i><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
	);

	$byline = sprintf(
		_x( '%s', 'post author', 'bizworx' ),
		'<i class="fa fa-user"></i><span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
	);
	
	$tags = get_the_tags();
	
	if( $tags) :
		
		foreach( $tags as $tag ){
			
			$singletag .= '<a href="' . get_tag_link( $tag->term_id ) . '">'. $tag->name .'</a>';
		}
		
	endif;
	
	if( $tags) :
	$tagsline = '<i class="fa fa-tag"></i><span class="blog-category-url">'.$singletag.'</span>';
	endif;

	echo '<span class="posted-on">' . $posted_on . '</span><span class="byline"> ' . $byline . '</span><span class="tags">'.$tagsline.'</span>';
	
}
endif;

// Changing excerpt more
function bizworx_excerpt_more($more) {
	global $post;
	return '<div class="blog-grid-button"><a class href="'. get_permalink($post->ID) . '">' . 'Read More ' . '<i class="fa fa-arrow-right"></i></a></div>';
}
add_filter('excerpt_more', 'bizworx_excerpt_more');

/**
 * Enqueue scripts and styles.
 */
function bizworx_scripts() {
	
	wp_enqueue_style( 'bizworx-fonts', esc_url( bizworx_google_fonts() ), array(), null );

	wp_enqueue_style( 'bizworx-style', get_stylesheet_uri() );
	
	wp_enqueue_script( 'bizworx-main', get_template_directory_uri() . '/js/main.js', array('jquery'), '20180213', true );
	
	wp_enqueue_script( 'owl.carousel.min', get_template_directory_uri() . '/js/owl.carousel.min.js', array('jquery'), '20180213', true );
	
	wp_enqueue_style( 'bizworx-font-awesome', get_template_directory_uri() . '/fonts/font-awesome.min.css' );

	wp_enqueue_style( 'owl.carousel', get_template_directory_uri() . '/css/owl.carousel.css' );
	
	wp_enqueue_style( 'owl.theme', get_template_directory_uri() . '/css/owl.theme.css' );
	
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'bizworx_scripts' );

/**
 * Fonts
 */
if ( !function_exists('bizworx_google_fonts') ) :
function bizworx_google_fonts() {
	$body_font 		= get_theme_mod('body_font_name', 'Poppins:400,600');
	$headings_font 	= get_theme_mod('headings_font_name', 'Ubuntu:400,400i,500,500i');

	$fonts     		= array();
	$fonts[] 		= esc_attr( str_replace( '+', ' ', $body_font ) );
	$fonts[] 		= esc_attr( str_replace( '+', ' ', $headings_font ) );

	if ( $fonts ) {
		$fonts_url = add_query_arg( array(
			'family' => urlencode( implode( '|', $fonts ) )
		), 'https://fonts.googleapis.com/css' );
	}

	return $fonts_url;	
}
endif;

function bizworx_header_background() {
	$title = get_theme_mod('header_title_1', 'Welcome to Bizworx');
	$subtitle = get_theme_mod('header_subtitle_1', 'Get some exciting features in our pro theme');
	$button_text = get_theme_mod('banner_button', 'Go to pro');
	$button_url = get_theme_mod('banner_button_url', 'https://popularfx.com/themes/wordpress/corporate/bizworx_pro'); 
	$front_header_type = get_theme_mod('front_header_type', 'image');
	$site_header_type = get_theme_mod('site_header_type', 'image');
	
	if ( ( $front_header_type == 'image' && is_front_page() || $site_header_type == 'image' && !is_front_page() ) ) :
	?>
	<div class="header-background">
		<div class="header-content">
			<h2 class="bg-maintitle maintitle"><?php echo esc_html( $title ); ?></h2>
			<p class="bg-subtitle subtitle"><?php echo esc_html( $subtitle ); ?></p>
			<a href="<?php echo esc_html( $button_url ); ?>" class="bg-banner-button banner-button"><?php echo esc_html( $button_text ); ?></a>
		</div>
	</div> 
	<?php
	endif;
}

/**
 * Header video
 */
function bizworx_header_video() {

	if ( !function_exists('the_custom_header_markup') ) {
		return;
	}

	$front_header_type 	= get_theme_mod( 'front_header_type' );
	$site_header_type 	= get_theme_mod( 'site_header_type' );

	if ( ( get_theme_mod('front_header_type') == 'core-video' && is_front_page() || get_theme_mod('site_header_type') == 'core-video' && !is_front_page() ) ) {
		the_custom_header_markup();
	}
}

/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function bizworx_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'bizworx' ),
		'id'            => 'sidebar-1',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	//Footer widget areas
	$widget_areas = get_theme_mod('footer_widget_areas', '3');
	for ($i=1; $i<=$widget_areas; $i++) {
		register_sidebar( array(
			'name'          => __( 'Footer ', 'bizworx' ) . $i,
			'id'            => 'footer-' . $i,
			'description'   => '',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		) );
	}

}
add_action( 'widgets_init', 'bizworx_widgets_init' );

/**
 * Enqueue Bootstrap
 */
function bizworx_enqueue_bootstrap() {
	wp_enqueue_style( 'bizworx-bootstrap', get_template_directory_uri() . '/css/bootstrap/bootstrap.min.css', array(), true );
}
add_action( 'wp_enqueue_scripts', 'bizworx_enqueue_bootstrap', 9 );

/**
 * Custom-Header
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Styles
 */
require get_template_directory() . '/inc/styles.php';

/**
 * Upsell
 */
require get_template_directory() . '/upsell/class-customize.php';

/**
 * Carousel slider
 */
require get_template_directory() . '/recommend/class-tgm-plugin-activation.php';

function bizworx_recommend_plugin() {
 
	if ( !defined( 'SITEORIGIN_PANELS_VERSION' ) ) {
	    $plugins[] = array(
	            'name'               => 'Elementor',
	            'slug'               => 'elementor',
	            'required'           => false,
	    );
	}

    $plugins[] = array(
            'name'               => 'Contact Form 7',
            'slug'               => 'contact-form-7',
            'required'           => false,
    );

    tgmpa( $plugins);
 
}
add_action( 'tgmpa_register', 'bizworx_recommend_plugin' );

/**
 * Admin notice
 */
require get_template_directory() . '/notices/persist-admin-notices-dismissal.php';

function bizworx_admin_notice() {
	if ( ! PAnD::is_admin_notice_active( 'bizworx-pro-notice' ) ) {
		return;
	}
	
	 echo '<div data-dismissible="bizworx-pro-notice" class="notice notice-info is-dismissible">
          <p>You have activated the <b>Bizworx</b> theme. For some new exciting features you can take a look of our <b><a href="https://popularfx.com/themes/wordpress/corporate/bizworx_pro">Bizworx pro</a></b> theme.</p>
         </div>';
	
}
add_action( 'admin_init', array( 'PAnD', 'init' ) );
add_action( 'admin_notices', 'bizworx_admin_notice' );