<?php

namespace DOT\Core\Admin;

class LayoutsList {

    public function __construct() {
        add_filter( 'admin_url', array( $this, 'duplicate_layout_url' ), 10, 3 );
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
    public function duplicate_layout_url( $url, $path, $blog_id ) {

        // If not "acfduplicatecomplete" action, return
        if ( !strstr( $url, 'acfduplicatecomplete' ) ) {
            return $url;
        }

        // Parse URL arguments
        $url_args = wp_parse_url( $url, PHP_URL_QUERY );
        parse_str( $url_args, $url_query );

        // If layout, add argument
        if ( dot_is_layout( $url_query['acfduplicatecomplete'] ) ) {
            $url .= '&layouts=1';
        }

        // Return URL
        return $url;
    }
}