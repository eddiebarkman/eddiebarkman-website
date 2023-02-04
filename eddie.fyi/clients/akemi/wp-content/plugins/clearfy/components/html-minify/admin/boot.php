<?php
	/**
	 * Admin boot
	 * @author Webcraftic <wordpress.webraftic@gmail.com>
	 * @copyright Webcraftic 25.05.2017
	 * @version 1.0
	 */

	// Exit if accessed directly
	if( !defined('ABSPATH') ) {
		exit;
	}

	/**
	 * Печатает ошибки совместимости с похожими плагинами
	 */
	add_action('wbcr_factory_notices_405_list', function ($notices, $plugin_name) {
		if( $plugin_name != WHTM_Plugin::app()->getPluginName() ) {
			return $notices;
		}

		if( is_plugin_active('autoptimize/autoptimize.php') ) {
			$notice_text = __('Clearfy: Html minify component is not compatible with the Autoptimize plugin, please do not use them together to avoid conflicts. Please disable the Html minify component', 'html-minify');

			if( class_exists('WCL_Plugin') ) {
				$component_button = WCL_Plugin::app()->getInstallComponentsButton('internal', 'html_minify');
				$notice_text .= ' ' . $component_button->getLink();
			}

			$notices[] = array(
				'id' => 'mac_plugin_compatibility',
				'type' => 'error',
				'classes' => array('wbcr-hide-after-action'),
				'dismissible' => false,
				'dismiss_expires' => 0,
				'text' => '<p>' . $notice_text . '</p>'
			);
		}

		return $notices;
	}, 10, 2);

	add_filter("wbcr_clearfy_group_options", function ($options) {
		$options[] = array(
			'name' => 'html_optimize',
			'title' => __('Optimize HTML Code?', 'html-minify'),
			'tags' => array('optimize_html', 'optimize_code', 'hide_my_wp'),
			'values' => array()
		);
		$options[] = array(
			'name' => 'html_keepcomments',
			'title' => __('Keep HTML comments?', 'html-minify'),
			'tags' => array(),
			'values' => array()
		);

		return $options;
	});

	/**
	 * Adds a new mode to the Quick Setup page
	 *
	 * @param array $mods
	 * @return mixed
	 */

	add_filter("wbcr_clearfy_allow_quick_mods", function ($mods) {
		if( !defined('WMAC_PLUGIN_ACTIVE') ) {
			$title = __('One click optimize html code', 'html-minify');
		} else {
			$title = __('One click optimize html code and scripts', 'html-minify');
		}

		$mod['optimize_code'] = array(
			'title' => $title,
			'icon' => 'dashicons-performance'
		);

		return $mod + $mods;
	});


