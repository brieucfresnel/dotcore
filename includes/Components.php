<?php

namespace DOT\Core;

class Elements {

    /**
     * Post type slug
     *
     * @var string
     */
    public static string $post_type = 'dot-element';

    public function __construct() {
        // WP hooks
        add_action( 'init', array( $this, 'register_element' ) );

        // ACF hooks
        add_filter( 'acf/location/rule_values/post_type', array( $this, 'remove_element_from_post_types' ) );
        add_filter( 'acf/location/rule_values/post', array( $this, 'remove_element_from_posts' ) );
        add_filter( 'acf/get_post_types', array( $this, 'remove_element_from_acf_post_types' ), 10, 2 );

        // ACF hooks - Element location rule
        add_filter( 'acf/location/rule_types', array( $this, 'location_types' ) );
        add_filter( 'acf/location/rule_match/' . self::$post_type, array( $this, 'location_match' ), 10, 3 );
        add_filter( 'acf/location/rule_values/' .self::$post_type, array( $this, 'location_values' ), 10, 2 );
    }

    /**
     * Remove Element from post types list
     *
     * @param $choices
     *
     * @return mixed
     */
    public function remove_element_from_post_types( $choices ) {

        unset( $choices[ self::$post_type ] );

        return $choices;
    }

    /**
     * Remove elements from posts list
     *
     * @param $choices
     *
     * @return mixed
     */
    public function remove_element_from_posts( $choices ) {

        // Get post type labels
        $post_type = get_post_type_labels( get_post_type_object( self::$post_type ) );

        // Remove elements
        unset( $choices[ $post_type->singular_name ] );

        return $choices;
    }

    /**
     * Remove Elements from acf_get_post_types()
     *
     * @param $post_types
     * @param $args
     *
     * @return mixed
     */
    public function remove_component_from_acf_post_types( $post_types, $args ) {

        $key = array_search( self::$post_type, $post_types, true );

        // If element key found, unset it
        if ( $key ) {
            unset( $post_types[ $key ] );
        }

        return $post_types;
    }

    /**
     * Add element rule
     *
     * @param $choices
     *
     * @return mixed
     */
    public function location_types( $choices ) {

        // Get post type labels
        $post_type = get_post_type_labels( get_post_type_object( self::$post_type ) );

        // Add element option
        $choices["DOT"][ self::$post_type ] = $post_type->singular_name;

        return $choices;
    }

    /**
     * element rule values
     *
     * @param $values
     * @param $rule
     *
     * @return array
     */
    public function location_values( $values, $rule ) {

        // Get posts grouped by
        $posts = get_posts(
            array(
                'post_type'      => self::$post_type,
                'posts_per_page' => - 1,
            )
        );

        // Add "all" option
        $values = array(
            'all' => __( 'All', 'acf' ),
        );

        // Build choices array
        if ( !empty( $posts ) ) {
            // Add posts
            foreach ( $posts as $post ) {
                $values[ $post->ID ] = $post->post_title;
            }
        }

        return $values;
    }

    /**
     * element rule matches
     *
     * @param $result
     * @param $rule
     * @param $screen
     *
     * @return bool
     */
    public function location_match( $result, $rule, $screen ) {
        global $current_screen;

        // Get post ID
        $post_id = acf_maybe_get( $screen, 'post_id' );

        // If no post, return
        if ( !$post_id ) {
            return false;
        }

        if ( $rule['value'] === 'all' ) {
            // Allow "all" to match any value.
            $match = $current_screen->post_type === self::$post_type;

        } else {
            // Compare all other values.
            $match = (int) $post_id === (int) $rule['value'];
        }

        // Allow for "!=" operator.
        if ( $rule['operator'] === '!=' ) {
            $match = !$match;
        }

        // Contains operator
        if ( $rule['operator'] === 'contains' ) {

            $post_name = get_post_field( 'post_title', $post_id );
            if ( !$post_name ) {
                return false;
            }

            // Compare
            return ( stripos( $post_name, $rule['value'] ) !== false );
        }

        return $match;
    }

    public function register_component() {
        $labels = array(
            'name'                  => _x( 'Elements', 'Post type general name', 'dotcore' ),
            'singular_name'         => _x( 'Element', 'Post type singular name', 'dotcore' ),
            'menu_name'             => _x( 'Elements', 'Admin Menu text', 'dotcore' ),
            'name_admin_bar'        => _x( 'Element', 'Add New on Toolbar', 'dotcore' ),
            'add_new'               => __( 'Add New', 'dotcore' ),
            'add_new_item'          => __( 'Add New Element', 'dotcore' ),
            'new_item'              => __( 'New Element', 'dotcore' ),
            'edit_item'             => __( 'Edit Element', 'dotcore' ),
            'view_item'             => __( 'View Element', 'dotcore' ),
            'all_items'             => __( 'All Elements', 'dotcore' ),
            'search_items'          => __( 'Search Elements', 'dotcore' ),
            'parent_item_colon'     => __( 'Parent Elements:', 'dotcore' ),
            'not_found'             => __( 'No elements found.', 'dotcore' ),
            'not_found_in_trash'    => __( 'No elements found in Trash.', 'dotcore' ),
            'featured_image'        => _x( 'Element Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'dotcore' ),
            'set_featured_image'    => _x( 'Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'dotcore' ),
            'remove_featured_image' => _x( 'Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'dotcore' ),
            'use_featured_image'    => _x( 'Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'dotcore' ),
            'archives'              => _x( 'Element archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'dotcore' ),
            'insert_into_item'      => _x( 'Insert into Element', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'dotcore' ),
            'uploaded_to_this_item' => _x( 'Uploaded to this Element', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'dotcore' ),
            'filter_items_list'     => _x( 'Filter Elements list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'dotcore' ),
            'items_list_navigation' => _x( 'Elements list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'dotcore' ),
            'items_list'            => _x( 'Elements list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'dotcore' ),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_in_menu'       => false,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => self::$post_type ),
            'capability_type'    => 'post',
            'hierarchical'       => false,
            'supports'           => array( 'title' ),
        );

        register_post_type( self::$post_type, $args );
    }
}