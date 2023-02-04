<?php
	/**
	 * Основной класс плагина
	 * @author Webcraftic <wordpress.webraftic@gmail.com>
	 * @copyright (c) 19.02.2018, Webcraftic
	 * @version 1.0
	 */

	// Exit if accessed directly
	if( !defined('ABSPATH') ) {
		exit;
	}

	if( !class_exists('WHTM_Plugin') ) {
		if( !class_exists('WHTM_PluginFactory') ) {
			// Этот плагин может быть аддоном плагина Clearfy, если он загружен, как аддон, то мы не подключаем фреймворк,
			// а наследуем функции фреймворка от плагина Clearfy. Если плагин скомпилирован, как отдельный плагин, то он использует собственный фреймворк для работы.
			// Константа LOADING_HTML_MINIFY_AS_ADDON утсанавливается в классе libs/factory/core/includes/Wbcr_Factory406_Plugin

			if( defined('LOADING_HTML_MINIFY_AS_ADDON') ) {
				class WHTM_PluginFactory {
					
				}
			} else {
				class WHTM_PluginFactory extends Wbcr_Factory406_Plugin {
					
				}
			}
		}
		
		class WHTM_Plugin extends WHTM_PluginFactory {
			
			/**
			 * @var Wbcr_Factory406_Plugin
			 */
			private static $app;
			
			/**
			 * @var bool
			 */
			private $as_addon;
			
			/**
			 * @param string $plugin_path
			 * @param array $data
			 * @throws Exception
			 */
			public function __construct($plugin_path, $data)
			{
				$this->as_addon = isset($data['as_addon']);
				
				if( $this->as_addon ) {
					$plugin_parent = isset($data['plugin_parent'])
						? $data['plugin_parent']
						: null;
					
					if( !($plugin_parent instanceof Wbcr_Factory406_Plugin) ) {
						throw new Exception('An invalid instance of the class was passed.');
					}

					// Если плагин загружен, как аддон, то мы передаем в app ссылку на класс родителя
					self::$app = $plugin_parent;
				} else {
					// Если плагин самостоятельный, то записываем в app сслыку на текущий класс
					self::$app = $this;

					parent::__construct($plugin_path, $data);
				}

				$this->setTextDomain();
				$this->setModules();
				$this->globalScripts();
				
				if( is_admin() ) {
					$this->adminScripts();
				}

				add_action('plugins_loaded', array($this, 'pluginsLoaded'));
			}
			
			/**
			 * Статический метод для быстрого доступа к информации о плагине, а также часто использумых методах.
			 *
			 * Пример:
			 * WHTM_Plugin::app()->getOption()
			 * WHTM_Plugin::app()->updateOption()
			 * WHTM_Plugin::app()->deleteOption()
			 * WHTM_Plugin::app()->getPluginName()
			 *
			 * @return Wbcr_Factory406_Plugin
			 */
			public static function app()
			{
				return self::$app;
			}

			// todo: перенести этот медот в фреймворк
			protected function setTextDomain()
			{
				// Localization plugin
				//load_plugin_textdomain('html-minify', false, dirname(WCL_PLUGIN_BASE) . '/languages/');

				$domain = 'html-minify';
				$locale = apply_filters('plugin_locale', is_admin()
					? get_user_locale()
					: get_locale(), $domain);
				$mofile = $domain . '-' . $locale . '.mo';

				if( !load_textdomain($domain, WHTM_PLUGIN_DIR . '/languages/' . $mofile) ) {
					load_muplugin_textdomain($domain);
				}
			}

			/**
			 * Подключаем модули фреймворка
			 */
			protected function setModules()
			{
				if( !$this->as_addon ) {
					self::app()->load(array(
						// Модуль отвечает за подключение скриптов и стилей для интерфейса
						array('libs/factory/bootstrap', 'factory_bootstrap_406', 'admin'),
						// Модуль отвечает за создание форм и полей
						array('libs/factory/forms', 'factory_forms_407', 'admin'),
						// Модуль отвечает за создание шаблонов страниц плагина
						array('libs/factory/pages', 'factory_pages_407', 'admin'),
						// Модуль в котором хранится общий функционал плагина Clearfy и его аддонов
						array('libs/factory/clearfy', 'factory_clearfy_203', 'all')
					));
				}
			}

			/**
			 * Регистрируем страницы плагина
			 */
			private function registerPages()
			{

				$admin_path = WHTM_PLUGIN_DIR . '/admin/pages';

				// Пример основной страницы настроек
				self::app()->registerPage('WHTM_SettingsPage', $admin_path . '/settings.php');

				// Пример внутренней страницы настроек
				//self::app()->registerPage('WHTM_StatisticPage', $admin_path . '/statistic.php');
			}

			/**
			 * Подключаем функции бекенда
			 */
			private function adminScripts()
			{
				require(WHTM_PLUGIN_DIR . '/admin/boot.php');
				$this->registerPages();
			}

			/**
			 * Подключаем глобальные функции
			 */
			private function globalScripts()
			{
				//require(WHTM_PLUGIN_DIR . '/includes/classes/class.configurate-comments.php');
				//new WHTM_ConfigComments(self::$app);
			}

			/**
			 * Выполнение действий после загрузки плагина
			 * Подключаем все классы оптимизации и запускаем процесс
			 */
			public function pluginsLoaded()
			{
				require_once(WHTM_PLUGIN_DIR . '/includes/classes/class.mac-base.php');
				require_once(WHTM_PLUGIN_DIR . '/includes/classes/class.mac-html.php');
				require_once(WHTM_PLUGIN_DIR . '/includes/classes/class.mac-main.php');

				require_once(WHTM_PLUGIN_DIR . '/includes/classes/ext/php/minify-html.php');

				$plugin = new WHTM_PluginMain();
				$plugin->start();
			}
		}
	}