<?php

/**
 * @wordpress-plugin
 * Plugin Name:       DOT Core
 * Description:       Core plugin for DOT Studio websites
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Studio DOT
 * Author URI:        https://studio-dot.fr
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       dotcore
 * Domain Path:       /languages
 */

namespace DOT\Core;

if (!defined('ABSPATH')) {
    exit;
}

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require(__DIR__ . '/vendor/autoload.php');
}


class DOT_Core {
    /**
     * @var string
     */
    public string $version = '0.0.1';

    /**
     * @var bool
     */
    private bool $acf = false;

    /**
     * The DOT_Core single instance
     *
     * @var DOT_Core|null
     */
    private static ?DOT_Core $instance = null;

    public function __construct() {
        define('DOT_VERSION', $this->version);
        define('DOT_FILE', __FILE__);
        define('DOT_CORE_PATH', plugin_dir_path(__FILE__));
        define('DOT_CORE_URL', plugin_dir_url(__FILE__));
        define('DOT_BASENAME', plugin_basename(__FILE__));
        define('DOT_THEME_PATH', get_stylesheet_directory());
        define('DOT_THEME_URI', get_stylesheet_directory_uri());
        define('DOT_THEME_INCLUDES_PATH', get_template_directory() . '/includes/');
        define('DOT_THEME_LAYOUTS_PATH', get_stylesheet_directory() . '/dotstarter/layouts/');
        define('DOT_THEME_LAYOUTS_URI', get_stylesheet_directory_uri() . '/dotstarter/layouts/');
        define('DOT_THEME_LAYOUT_PARTS_PATH', get_stylesheet_directory() . '/dotstarter/layout-parts/');
        define('DOT_THEME_LAYOUT_PARTS_URI', get_stylesheet_directory_uri() . '/dotstarter/layouts-parts/');
        define('DOT_THEME_COMPONENTS_PATH', get_stylesheet_directory() . '/dotstarter/components/');
        define('DOT_THEME_COMPONENTS_URI', get_stylesheet_directory_uri() . '/dotstarter/components/');
        define('DOT_THEME_ASSETS_PATH', get_stylesheet_directory() . '/assets/');
        define('DOT_THEME_ASSETS_URL', get_stylesheet_directory_uri() . '/assets/');

        include_once(DOT_CORE_PATH . 'init.php');

        if (!$this->has_acf()) {
            return;
        }

        $this->init();

        add_action('acf/init', array($this, 'load'));
    }

    public static function getInstance(): ?DOT_Core {
        if (self::$instance === null) {
            self::$instance = new DOT_Core();
        }

        return self::$instance;
    }

    public function load() {
        require_once(DOT_CORE_PATH . 'includes/helpers.php');

        // Main
        acf_get_instance('\DOT\Core\Main\MainFlexible');
        acf_get_instance('\DOT\Core\Main\LayoutSettings');
        acf_get_instance('\DOT\Core\Main\Layouts');
        acf_get_instance('\DOT\Core\Main\LayoutParts');
        acf_get_instance('\DOT\Core\Main\Components');

        // Admin
        acf_get_instance('\DOT\Core\Admin\Admin');
        acf_get_instance('\DOT\Core\Admin\Menus');

        acf_get_instance('\DOT\Core\Admin\Layouts\LayoutsList');
        acf_get_instance('\DOT\Core\Admin\Layouts\LayoutsSingle');

        acf_get_instance('\DOT\Core\Admin\LayoutParts\LayoutPartsList');
        acf_get_instance('\DOT\Core\Admin\LayoutParts\LayoutPartsSingle');

        acf_get_instance('\DOT\Core\Admin\Components\ComponentsSingle');

        // Register custom field types
        acf_register_field_type('DOT\Core\Fields\FieldLayoutPart');
        acf_register_field_type('DOT\Core\Fields\FieldComponent');
    }

    public function init() {
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_styles'));
        add_filter('script_loader_tag', array($this, 'set_scripts_type_module_attribute'), 10, 3);
        add_action('wp_head', array($this, 'setup_GTM'));
    }

    public function enqueue_styles() {
        wp_enqueue_style('dot-admin', DOT_CORE_URL . '/dist/css/admin.css', array(), false);
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
        $GTM_ID = get_field('key_gtm', 'option');

        // Check for GTM ID validity then launch GTM
        if (!preg_match('/^GTM-[A-Z0-9]{1,7}$/', $GTM_ID)) return;

        ?>
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer', <?= $GTM_ID ?>;</script>
        <?php
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

DOT_Core::getInstance();
