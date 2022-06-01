<?php

namespace DOT\Core;

class PostTypes {
    public function __construct() {
            add_action('init', array($this, 'register_post_types'));
    }

    public function register_post_types() {
//        $labels = array(
//            'name'                  => _x( 'Components', 'Post type general name', 'dotcore' ),
//            'singular_name'         => _x( 'Component', 'Post type singular name', 'dotcore' ),
//            'menu_name'             => _x( 'Components', 'Admin Menu text', 'dotcore' ),
//            'name_admin_bar'        => _x( 'Component', 'Add New on Toolbar', 'dotcore' ),
//            'add_new'               => __( 'Add New', 'dotcore' ),
//            'add_new_item'          => __( 'Add New Component', 'dotcore' ),
//            'new_item'              => __( 'New Component', 'dotcore' ),
//            'edit_item'             => __( 'Edit Component', 'dotcore' ),
//            'view_item'             => __( 'View Component', 'dotcore' ),
//            'all_items'             => __( 'All Components', 'dotcore' ),
//            'search_items'          => __( 'Search Components', 'dotcore' ),
//            'parent_item_colon'     => __( 'Parent Components:', 'dotcore' ),
//            'not_found'             => __( 'No components found.', 'dotcore' ),
//            'not_found_in_trash'    => __( 'No components found in Trash.', 'dotcore' ),
//            'featured_image'        => _x( 'Component Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'dotcore' ),
//            'set_featured_image'    => _x( 'Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'dotcore' ),
//            'remove_featured_image' => _x( 'Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'dotcore' ),
//            'use_featured_image'    => _x( 'Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'dotcore' ),
//            'archives'              => _x( 'Component archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'dotcore' ),
//            'insert_into_item'      => _x( 'Insert into Component', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'dotcore' ),
//            'uploaded_to_this_item' => _x( 'Uploaded to this Component', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'dotcore' ),
//            'filter_items_list'     => _x( 'Filter Components list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'dotcore' ),
//            'items_list_navigation' => _x( 'Components list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'dotcore' ),
//            'items_list'            => _x( 'Components list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'dotcore' ),
//        );
//
//        $args = array(
//            'labels'             => $labels,
//            'public'             => true,
//            'publicly_queryable' => true,
//            'show_in_menu'       => false,
//            'query_var'          => true,
//            'rewrite'            => array( 'slug' => 'component' ),
//            'capability_type'    => 'post',
//            'hierarchical'       => false,
//            'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments' ),
//        );
//
//        register_post_type( 'component', $args );
    }
}