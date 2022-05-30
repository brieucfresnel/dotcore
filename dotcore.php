<?php

/**
 * @wordpress-plugin
 * Plugin Name:       DOT Core
 * Description:       Core plugin for DOT Studio websites
 * Version:           1.0.0
 * Author:            DOT Studio
 * Author URI:        https://studio-dot.fr
 * License:           GPL-2.0
 * License URI:       https://opensource.org/licenses/GPL-2.0
 * Text Domain:       dotstarter
 * Domain Path:       languages
 */

if (!defined('ABSPATH')) {
    exit;
}

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require(__DIR__ . '/vendor/autoload.php');
}

if (!class_exists('DotCore')) {
    class DotCore {
        private static DotCore $_instance;

        public string $version = '0.0.1';

        private bool $acf = false;

        public function __construct() {
            define('DOT_VERSION', $this->version);
            define('DOT_FILE', __FILE__);
            define('DOT_CORE_PATH', plugin_dir_path(__FILE__));
            define('DOT_BASENAME', plugin_basename(__FILE__));
            define('DOT_THEME_PATH', get_stylesheet_directory_uri());
            define('DOT_THEME_INCLUDES_PATH', get_template_directory() . '/includes/');
            define('DOT_THEME_LAYOUTS_PATH', get_stylesheet_directory() . '/templates/layouts/');
            define('DOT_THEME_LAYOUTS_URL', get_stylesheet_directory_uri() . '/templates/layouts/');
            define('DOT_THEME_ASSETS_PATH', get_stylesheet_directory() . '/assets/');
            define('DOT_THEME_ASSETS_URL', get_stylesheet_directory_uri() . '/assets/');
            define('DOT_THEME_STYLE_FILENAME', 'styles');
            define('DOT_THEME_STYLE_ADMIN_FILENAME', 'styles-admin');

            include_once(DOT_CORE_PATH . 'init.php');

            if (!$this->has_acf()) {
                return;
            }

            $this->init();

            add_action('acf/init', array($this, 'load'));
        }

        public function init() {
//            add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
//            add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
            add_filter('script_loader_tag', array($this, 'set_scripts_type_module_attribute'), 10, 3);
            add_action('wp_head', array($this, 'setup_GTM'));
        }

        public function load() {
            require_once(DOT_CORE_PATH . 'helpers.php');
            require_once(DOT_CORE_PATH . 'includes/options-pages.php');
            require_once(DOT_CORE_PATH . 'includes/main-flexible.php');
            require_once(DOT_CORE_PATH . 'includes/flexible-layout-settings.php');
        }

        /**
         * @return DotCore
         */
        public static function instance(): DotCore {
            if (!isset(self::$_instance)) {
                self::$_instance = new DotCore();
            }

            return self::$_instance;
        }

        public function enqueue_styles() {
        }

        public function enqueue_scripts() {
        }

        public function set_scripts_type_module_attribute($tag, $handle, $src) {
            // if not your script, do nothing and return original $tag
            if ('dotstarter-frontend' !== $handle) {
                return $tag;
            }
            // change the script tag by adding type="module" and return it.
            $tag = '<script type="module" src="' . esc_url($src) . '"></script>';

            return $tag;
        }

        public function setup_GTM() {
            $GTM_ID = get_field('google_tag_manager_id', 'option');

            // Check for GTM ID validity then launch GTM
            if (!preg_match('/^GTM-[A-Z0-9]{1,7}$/', $GTM_ID)) return;

            $script = `
                <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
              new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
                })(window,document,'script','dataLayer',` . $GTM_ID . `);</script>
                `;

            echo $script;
        }

        /**
         * Define constants
         *
         * @param      $name
         * @param bool $value
         */
        public function define($name, $value = true) {
            if (!defined($name)) {
                define($name, $value);
            }
        }

        /**
         * Check if ACF Pro is activated
         *
         * @return bool
         */
        public function has_acf() {

            // If ACF already available, return
            if ($this->acf) {
                return true;
            }

            // Check if ACF Pro is activated
            $this->acf = class_exists('ACF') && defined('ACF_PRO') && defined('ACF_VERSION') && version_compare(ACF_VERSION, '5.8', '>=') && class_exists('ACFE');

            return $this->acf;

        }
    }

    new DotCore();
}