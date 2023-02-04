<?php
	/**
	 * Plugin Name: HTML Мinify
	 * Plugin URI: https://clearfy.pro/html-minify/
	 * Description: Ever look at the HTML markup of your website and notice how sloppy and amateurish it looks? The HTML Мinify options cleans up sloppy looking markup and minifies, which also speeds up download.
	 * Author: Webcraftic <wordpress.webraftic@gmail.com>
	 * Version: 1.0.1
	 * Text Domain: html-minify
	 * Domain Path: /languages/
	 * Author URI: https://clearfy.pro
	 */

	/*
	 * #### CREDITS ####
	 * This plugin is based on the plugin Autoptimize by the author Frank Goossens, we have finalized this code for our project and our goals.
	 * Many thanks to Frank Goossens for the quality solution for optimizing scripts in Wordpress.
	 *
	 * Public License is a GPLv2 compatible license allowing you to change and use this version of the plugin for free.
	 */

	// Exit if accessed directly
	if( !defined('ABSPATH') ) {
		exit;
	}

	/**
	 * Troubleshoot old versions of PHP on the client server
	 */
	if( version_compare(PHP_VERSION, '5.4.0', '<') ) {
		function wbcr_htm_admin_notice_php_error()
		{
			?>
			<div class="notice notice-error">
				<p><?php _e('The job of the component "Html minify" component has been suspended! You are using the old version of PHP. Please update the PHP version to 5.4 or later to continue to use this component!', 'html-minify'); ?></p>
			</div>
		<?php
		}

		add_action('admin_notices', 'wbcr_htm_admin_notice_php_error');
		return;
	}

	/**
	 * Уведомление о том, что этот плагин используется уже в составе плагина Clearfy, как его компонент.
	 * Мы блокируем работу этого плагина, чтобы не вызывать конфликт.
	 */
	if( defined('WHTM_PLUGIN_ACTIVE') || (defined('WHTM_PLUGIN_ACTIVE') && !defined('LOADING_HTML_MINIFY_AS_ADDON')) ) {
		function wbcr_htm_admin_notice_error()
		{
			?>
			<div class="notice notice-error">
				<p><?php _e('We found that you have the "Clearfy - wordpress optimization plugin" plugin installed, this plugin already has Html minify functions, so you can deactivate plugin "Html minify"!', 'html-minify'); ?></p>
			</div>
		<?php
		}

		add_action('admin_notices', 'wbcr_htm_admin_notice_error');

		return;
	} else {

		// Устанавливаем контстанту, что плагин уже используется
		define('WHTM_PLUGIN_ACTIVE', true);

		// Директория плагина
		define('WHTM_PLUGIN_DIR', dirname(__FILE__));

		// Относительный путь к плагину
		define('WHTM_PLUGIN_BASE', plugin_basename(__FILE__));

		// Ссылка к директории плагина
		define('WHTM_PLUGIN_URL', plugins_url(null, __FILE__));

		

		// Этот плагин может быть аддоном плагина Clearfy, если он загружен, как аддон, то мы не подключаем фреймворк,
		// а наследуем функции фреймворка от плагина Clearfy. Если плагин скомпилирован, как отдельный плагин, то он использует собственный фреймворк для работы.
		// Константа LOADING_HTML_MINIFY_AS_ADDON утсанавливается в классе libs/factory/core/includes/Wbcr_Factory406_Plugin

		if( !defined('LOADING_HTML_MINIFY_AS_ADDON') ) {
			// Фреймворк - отвечает за интерфейс, содержит общие функции для серии плагинов и готовые шаблоны для быстрого развертывания плагина.
			require_once(WHTM_PLUGIN_DIR . '/libs/factory/core/boot.php');
		}

		// Основной класс плагина
		require_once(WHTM_PLUGIN_DIR . '/includes/class.plugin.php');

		// Класс WHTM_Plugin создается только, если этот плагин работает, как самостоятельный плагин.
		// Если плагин работает, как аддон, то класс создается родительским плагином.

		if( !defined('LOADING_HTML_MINIFY_AS_ADDON') ) {
			new WHTM_Plugin(__FILE__, array(
				'prefix' => 'wbcr_htm_', // префикс для базы данных и полей формы
				'plugin_name' => 'wbcr_html_minify', // имя плагина, как уникальный идентификатор
				'plugin_title' => __('Webcraftic HTML Minify', 'html-minify'), // заголовок плагина
				'plugin_version' => '1.0.1', // текущая версия плагина
				'required_php_version' => '5.2', // минимальная версия php для работы плагина
				'required_wp_version' => '4.2', // минимальная версия wp для работы плагина
				'plugin_build' => 'free', // сборка плагина
				//'updates' => WHTM_PLUGIN_DIR . '/updates/' в этой папке хранятся миграции для разных версий плагина
			));
		}
	}