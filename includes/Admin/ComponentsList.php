<?php

namespace DOT\Core\Admin;

class ComponentsList {
    public function __construct() {
        add_action('current_screen', array($this, 'current_screen'));
    }

    public function current_screen() {
        if (!dot_is_component_screen())
            return;

        add_filter('admin_url', array($this, 'duplicate_component_url'), 10, 3);
        add_action('load-edit.php', array($this, 'load_components_list'));
    }

    public function pre_get_posts(\WP_Query $query) {
        // Get flexible layouts only
        $query->set(
            's', 's:13:"dot_is_component";i:1'
        );
    }

    /**
     * Filter posts query to get only layouts
     *
     * @return void
     */
    public function load_components_list() {
        add_action('pre_get_posts', array($this, 'pre_get_posts'));
    }

    /**
     * Add argument to URL to stay on layouts list when duplicate a layout
     *
     * @param $url
     * @param $path
     * @param $blog_id
     *
     * @return mixed|string
     */
    public function duplicate_component_url($url, $path, $blog_id) {

        // If not "acfduplicatecomplete" action, return
        if (!strstr($url, 'acfduplicatecomplete')) {
            return $url;
        }

        // Parse URL arguments
        $url_args = wp_parse_url($url, PHP_URL_QUERY);
        parse_str($url_args, $url_query);

        // If layout, add argument
        if (dot_is_layout($url_query['acfduplicatecomplete'])) {
            $url .= '&components=1';
        }

        // Return URL
        return $url;
    }
}