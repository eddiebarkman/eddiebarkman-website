<?php
/**
 * VW Photography Theme Customizer
 *
 * @package VW Photography
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function vw_photography_customize_register( $wp_customize ) {

	//add home page setting pannel
	$wp_customize->add_panel( 'vw_photography_panel_id', array(
	    'priority' => 10,
	    'capability' => 'edit_theme_options',
	    'theme_supports' => '',
	    'title' => __( 'VW Settings', 'vw-photography' ),
	    'description' => __( 'Description of what this panel does.', 'vw-photography' ),
	) );

	$wp_customize->add_section( 'vw_photography_left_right', array(
    	'title'      => __( 'General Settings', 'vw-photography' ),
		'priority'   => 30,
		'panel' => 'vw_photography_panel_id'
	) );

	// Add Settings and Controls for Layout
	$wp_customize->add_setting('vw_photography_theme_options',array(
        'default' => __('Right Sidebar','vw-photography'),
        'sanitize_callback' => 'vw_photography_sanitize_choices'	        
	));
	$wp_customize->add_control('vw_photography_theme_options',array(
        'type' => 'radio',
        'label' => __('Do you want this section','vw-photography'),
        'section' => 'vw_photography_left_right',
        'choices' => array(
            'Left Sidebar' => __('Left Sidebar','vw-photography'),
            'Right Sidebar' => __('Right Sidebar','vw-photography'),
            'One Column' => __('One Column','vw-photography'),
            'Three Columns' => __('Three Columns','vw-photography'),
            'Four Columns' => __('Four Columns','vw-photography'),
            'Grid Layout' => __('Grid Layout','vw-photography')
        ),
	) );

	//Slider
	$wp_customize->add_section( 'vw_photography_slidersettings' , array(
    	'title'      => __( 'Slider Settings', 'vw-photography' ),
		'priority'   => null,
		'panel' => 'vw_photography_panel_id'
	) );

	$wp_customize->add_setting('vw_photography_slider_arrows',array(
       'default' => 'false',
       'sanitize_callback'	=> 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_photography_slider_arrows',array(
       'type' => 'checkbox',
       'label' => __('Show / Hide slider','vw-photography'),
       'section' => 'vw_photography_slidersettings',
    ));

	for ( $count = 1; $count <= 4; $count++ ) {

		// Add color scheme setting and control.
		$wp_customize->add_setting( 'vw_photography_slider_page' . $count, array(
			'default'           => '',
			'sanitize_callback' => 'vw_photography_sanitize_dropdown_pages'
		) );
		$wp_customize->add_control( 'vw_photography_slider_page' . $count, array(
			'label'    => __( 'Select Slide Image Page', 'vw-photography' ),
			'description' => __('Slider image size (1500 x 600)','vw-photography'),
			'section'  => 'vw_photography_slidersettings',
			'type'     => 'dropdown-pages'
		) );
	}

	//Photogarphy Category
	$wp_customize->add_section( 'vw_photography_category_section' , array(
    	'title'      => __( 'Photogarphy Category', 'vw-photography' ),
		'priority'   => null,
		'panel' => 'vw_photography_panel_id'
	) );

	// Add color scheme setting and control.
	$wp_customize->add_setting( 'vw_photography_category_page', array(
		'default'           => '',
		'sanitize_callback' => 'vw_photography_sanitize_dropdown_pages'
	) );
	$wp_customize->add_control( 'vw_photography_category_page', array(
		'label'    => __( 'Category Page', 'vw-photography' ),
		'section'  => 'vw_photography_category_section',
		'type'     => 'dropdown-pages'
	) );

	$post_list = get_posts();
	$i = 0;
	$pst[]='Select';
	foreach($post_list as $post){
		$pst[$post->post_title] = $post->post_title;
	}

	for ( $m = 1; $m <= 3; $m++ ) {

		$wp_customize->add_setting('vw_photography_post_category' .$m,array(
			'sanitize_callback' => 'vw_photography_sanitize_choices',
		));
		$wp_customize->add_control('vw_photography_post_category' .$m,array(
			'type'    => 'select',
			'choices' => $pst,
			'label' => __('Select post','vw-photography'),
			'description' => __('Image Size (290 x 440)','vw-photography'),
			'section' => 'vw_photography_category_section',
		));
	}
	
	//Footer Text
	$wp_customize->add_section('vw_photography_footer',array(
		'title'	=> __('Footer','vw-photography'),
		'description'=> __('This section will appear in the footer','vw-photography'),
		'panel' => 'vw_photography_panel_id',
	));	
	
	$wp_customize->add_setting('vw_photography_footer_text',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));	
	$wp_customize->add_control('vw_photography_footer_text',array(
		'label'	=> __('Copyright Text','vw-photography'),
		'section'=> 'vw_photography_footer',
		'setting'=> 'vw_photography_footer_text',
		'type'=> 'text'
	));	
}

add_action( 'customize_register', 'vw_photography_customize_register' );

/**
 * Singleton class for handling the theme's customizer integration.
 *
 * @since  1.0.0
 * @access public
 */
final class VW_Photography_Customize {

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {

		static $instance = null;

		if ( is_null( $instance ) ) {
			$instance = new self;
			$instance->setup_actions();
		}

		return $instance;
	}

	/**
	 * Constructor method.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function __construct() {}

	/**
	 * Sets up initial actions.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function setup_actions() {

		// Register panels, sections, settings, controls, and partials.
		add_action( 'customize_register', array( $this, 'sections' ) );

		// Register scripts and styles for the controls.
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_control_scripts' ), 0 );
	}

	/**
	 * Sets up the customizer sections.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  object  $manager
	 * @return void
	 */
	public function sections( $manager ) {

		// Load custom sections.
		load_template( trailingslashit( get_template_directory() ) . '/inc/section-pro.php' );

		// Register custom section types.
		$manager->register_section_type( 'VW_Photography_Customize_Section_Pro' );

		// Register sections.
		$manager->add_section(
			new VW_Photography_Customize_Section_Pro(
				$manager,
				'example_1',
				array(
					'priority'   => 9,
					'title'    => esc_html__( 'Photography Pro', 'vw-photography' ),
					'pro_text' => esc_html__( 'Upgrade Pro', 'vw-photography' ),
					'pro_url'  => esc_url('https://www.vwthemes.com/themes/wordpress-photography-themes/'),
				)
			)
		);
	}

	/**
	 * Loads theme customizer CSS.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function enqueue_control_scripts() {

		wp_enqueue_script( 'vw-photography-customize-controls', trailingslashit( get_template_directory_uri() ) . '/js/customize-controls.js', array( 'customize-controls' ) );

		wp_enqueue_style( 'vw-photography-customize-controls', trailingslashit( get_template_directory_uri() ) . '/css/customize-controls.css' );
	}
}

// Doing this customizer thang!
VW_Photography_Customize::get_instance();