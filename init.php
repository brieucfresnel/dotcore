<?php

if (!class_exists('DOT_Init')) {

    class DOT_Init {
        public function __construct() {
            register_activation_hook(DOT_FILE, array($this, 'dot_activation'));
            register_deactivation_hook(DOT_FILE, array($this, 'dot_deactivation'));
            add_action('init', array($this, 'load_translations'));
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
    }

    new DOT_Init();
}

