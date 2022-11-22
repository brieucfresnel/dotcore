<?php

namespace DOT\Core\Admin\LayoutParts;

class LayoutPartsList {
    public function __construct() {
        add_action('current_screen', array($this, 'current_screen'));
    }

    public function current_screen() {
        if (!dot_is_layout_part_screen())
            return;

        add_filter('admin_url', array($this, 'duplicate_layout_part_url'), 10, 3);
        add_action('load-edit.php', array($this, 'load_layout_parts_list'));

        // Table columns
        add_filter( 'manage_edit-acf-field-group_columns', array( $this, 'columns' ), 15 );
    }

    public function pre_get_posts(\WP_Query $query) {
        // Get flexible layouts only
        $query->set(
            's', 's:13:"dot_is_lpart";i:1'
        );
    }

    /**
     * Filter posts query to get only layouts
     *
     * @return void
     */
    public function load_layout_parts_list() {
        add_action('pre_get_posts', array($this, 'pre_get_posts'));
    }

    /**
     * @param $columns
     * @return mixed
     */
    public function columns($columns) {
        unset( $columns['acf-field-group-category'] );

        return $columns;
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
    public function duplicate_layout_part_url($url, $path, $blog_id) {

        // If not "acfduplicatecomplete" action, return
        if (!strstr($url, 'acfduplicatecomplete')) {
            return $url;
        }

        // Parse URL arguments
        $url_args = wp_parse_url($url, PHP_URL_QUERY);
        parse_str($url_args, $url_query);

        // If layout, add argument
        if (dot_is_layout($url_query['acfduplicatecomplete'])) {
            $url .= '&layout_parts=1';
        }

        // Return URL
        return $url;
    }
}