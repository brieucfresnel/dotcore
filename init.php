<?php

if (!class_exists('DOT_Init')) {

    class DOT_Init {
        public function __construct() {
            register_activation_hook(DOT_FILE, array($this, 'dot_activation'));
            register_deactivation_hook(DOT_FILE, array($this, 'dot_deactivation'));
            add_action('init', array($this, 'load_translations'));

            require_once(DOT_CORE_PATH . 'vendor/tgmpa/tgm-plugin-activation/class-tgm-plugin-activation.php');
            add_action('tgmpa_register', array($this, 'register_required_plugins'));
        }

        public function dot_activation() {

        }

        public function dot_deactivation() {

        }

        /**
         * Init hook
         * Load translations
         */
        public function load_translations(): bool {

            $domain = 'dotcore';

            $locale = apply_filters('plugin_locale', get_locale(), $domain);
            $mo_file = $domain . '-' . $locale . '.mo';

            // Try to load from the languages directory first.
            if (load_textdomain($domain, WP_LANG_DIR . '/plugins/' . $mo_file)) {
                return true;
            }

            // Load from plugin lang folder.
            return load_textdomain($domain, DOT_CORE_PATH . 'lang/' . $mo_file);

        }

        /**
         * @return void
         * @throws Exception
         */
        function register_required_plugins() {
            $plugins = array(

                // Bundled plug-ins
                array(
                    'name' => 'Advanced Custom Fields Pro',
                    'slug' => 'advanced-custom-fields-pro',
                    'required' => true,
                ),
                array(
                    'name' => 'WP Migrate DB Pro',
                    'slug' => 'wp-migrate-db-pro',
                    'required' => true,
                ),

                // Plug-ins from WordPress repository
                array(
                    'name' => 'Classic Editor',
                    'slug' => 'classic-editor',
                    'required' => true,
                ),
                array(
                    'name' => 'Advanced Custom Fields : Extended',
                    'slug' => 'acf-extended',
                    'required' => true,
                ),
                array(
                    'name' => 'GDPR Cookie Compliance (CCPA ready)',
                    'slug' => 'gdpr-cookie-compliance',
                    'required' => true,
                ),
                array(
                    'name' => 'Yoast SEO',
                    'slug' => 'wordpress-seo',
                    'required' => true,
                ),
                array(
                    'name' => 'Better Search Replace',
                    'slug' => 'better-search-replace',
                    'required' => false,
                ),
                array(
                    'name' => 'What The File',
                    'slug' => 'what-the-file',
                    'required' => false,
                ),
            );

            $config = array(
                'id' => 'dotcore',
                // Unique ID for hashing notices for multiple instances of TGMPA.
                'default_path' => DOT_CORE_PATH . 'plugins/',
                // Default absolute path to bundled plugins.
                'menu' => 'tgmpa-install-plugins',
                // Menu slug.
                'parent_slug' => 'themes.php',
                // Parent menu slug.
                'capability' => 'edit_theme_options',
                // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
                'has_notices' => true,
                // Show admin notices or not.
                'dismissable' => true,
                // If false, a user cannot dismiss the nag message.
                'dismiss_msg' => '',
                // If 'dismissable' is false, this message will be output at top of nag.
                'is_automatic' => false,
                // Automatically activate plugins after installation or not.
                'message' => '',
                // Message to output right before the plugins table.
            );

            try {
                tgmpa($plugins, $config);
            } catch (Exception $e) {
                throw new Exception('This plugin requires composer and npm configuration. Please follow the instructions in readme.md');
            }
        }
    }

    new DOT_Init();
}

