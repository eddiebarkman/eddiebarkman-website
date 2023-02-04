<?php

	/**
	 * The page Settings.
	 *
	 * @since 1.0.0
	 */

	// Exit if accessed directly
	if( !defined('ABSPATH') ) {
		exit;
	}

	class WGA_CachePage extends Wbcr_FactoryPages407_ImpressiveThemplate {

		/**
		 * The id of the page in the admin menu.
		 *
		 * Mainly used to navigate between pages.
		 * @see FactoryPages407_AdminPage
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $id = "ga_cache";
		public $page_menu_dashicon = 'dashicons-testimonial';

		/**
		 * @param Wbcr_Factory406_Plugin $plugin
		 */
		public function __construct(Wbcr_Factory406_Plugin $plugin)
		{
			$this->menu_title = __('Local Google Analytics', 'simple-google-analytics');

			if( !defined('LOADING_GA_CACHE_AS_ADDON') ) {
				$this->internal = false;
				$this->menu_target = 'options-general.php';
				$this->add_link_to_plugin_actions = true;
			}

			parent::__construct($plugin);

			$this->plugin = $plugin;
		}

		public function getMenuTitle()
		{
			return defined('LOADING_GA_CACHE_AS_ADDON')
				? __('Google Analytics Cache', 'simple-google-analytics')
				: __('General', 'simple-google-analytics');
		}

		/**
		 * Requests assets (js and css) for the page.
		 *
		 * @see Wbcr_FactoryPages407_AdminPage
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function assets($scripts, $styles)
		{
			parent::assets($scripts, $styles);

			// Add Clearfy styles for HMWP pages
			if( defined('WBCR_CLEARFY_PLUGIN_ACTIVE') ) {
				$this->styles->add(WCL_PLUGIN_URL . '/admin/assets/css/general.css');
			}
		}


		/**
		 * Permalinks options.
		 *
		 * @since 1.0.0
		 * @return mixed[]
		 */
		public function getOptions()
		{
			$options = wbcr_ga_get_plugin_options();

			$formOptions = array();

			$formOptions[] = array(
				'type' => 'form-group',
				'items' => $options,
				//'cssClass' => 'postbox'
			);

			return apply_filters('wbcr_ga_notices_form_options', $formOptions, $this);
		}
	}