<?php

namespace DOT\Core\Admin;

if (!class_exists('\Dot\Core\Admin\Admin')) {
    class Admin {

        public function __construct() {
            add_filter('parent_file', array($this, 'menu_parent_file'));
            add_filter('submenu_file', array($this, 'menu_submenu_file'));
            add_filter('admin_url', array($this, 'change_admin_url'), 10, 2);
            add_action( 'in_admin_header', array( $this, 'add_navbar' ) );
        }

        public function add_navbar() {
            if ( $this->is_dot_admin_page() ) {
                $this->display_navbar();
            }
        }

        public function display_navbar() {
            add_filter( 'acf/admin/toolbar', '__return_false' );
        }

        public function is_dot_admin_page() {

            $is_dot_admin = false;

            $layouts = acf_get_instance('\DOT\Core\Layouts');

            if (
                acf_maybe_get_GET( 'layouts' ) === '1' ||
                acf_maybe_get_GET( 'layout' ) === '1' ||
                $layouts->is_layout( get_post( acf_maybe_get_GET( 'post' ) ) )
            ) {
                $is_dot_admin = true;
            }

            return $is_dot_admin;
        }

        /**
         * Change highlighted parent menu
         *
         * @param $parent_file
         *
         * @return string
         */
        public function menu_parent_file($parent_file) {

            if (dot_is_layout_screen()) {
                global $pagenow, $plugin_page;

                $pagenow = 'dotstarter';
                $plugin_page = 'dotstarter';
            }

            return $parent_file;
        }

        /**
         * Change highlighted subpage menu
         *
         * @param $submenu_file
         *
         * @return string
         */
        public function menu_submenu_file($submenu_file) {

            global $current_screen;

            $layouts = acf_get_instance('\DOT\Core\Layouts');

            // Define submenu for Layouts menu
            $is_layout = $layouts->is_layout(acf_maybe_get_GET('post'));
            if (acf_maybe_get_GET('layouts') === '1' || $is_layout || acf_maybe_get_GET('layout') === '1') {
                $submenu_file = 'edit.php?post_type=acf-field-group&layouts=1';
            }

            return $submenu_file;
        }

        /**
         * Change "Add new" link on layouts page
         *
         * @param $url
         * @param $path
         *
         * @return string
         */

        public function change_admin_url($url, $path) {

            // Modify "Add new" link on layouts page
            if ($path === 'post-new.php?post_type=acf-field-group' && acf_maybe_get_GET('layouts') === '1') {
                // Add argument
                $url = $url . '&layout=1';
            }

            $layouts = acf_get_instance('\DOT\Core\Layouts');

            // Modify "Add new" link on layout single page
            $is_layout = $layouts->is_layout(acf_maybe_get_GET('post'));
            if ($path === 'post-new.php?post_type=acf-field-group' && $is_layout) {
                $url = $url . '&layout=1';
            }

            return $url;
        }
    }
}