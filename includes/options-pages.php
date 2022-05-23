<?php

namespace Dot\DotCore;

if (!class_exists('OptionsPages')) {
    class OptionsPages {
        public function __construct() {
            $this->generate_options_pages();
        }

        public function generate_options_pages() {
            acf_add_options_page( array(
                'page_title'      => __( 'Réglages du thème', 'dotcore' ),
                'menu_title'      => __( 'Réglages du thème', 'dotcore' ),
                'menu_slug'       => 'theme-settings',
                'capability'      => 'edit_posts',
                'position'        => '',
                'parent_slug'     => '',
                'icon_url'        => '',
                'redirect'        => false,
                'post_id'         => 'options',
                'autoload'        => false,
                'update_button'   => __( 'Mettre à jour', 'dotcore' ),
                'updated_message' => __( 'Réglages mis à jour', 'dotcore' ),
            ) );
        }
    }

    new OptionsPages();
}