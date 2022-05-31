<?php

namespace DOT\Core\Admin;

class Menus {

    public function __construct() {
        add_action('admin_menu', array($this, 'add_menu_pages'));
    }

    public function add_menu_pages() {
        $main_page_slug = 'dotstarter';

        $main_page = array(
            "page_title" => __('DotStarter', 'dotcore'),
            "menu_title" => __('DotStarter', 'dotcore'),
            "capability" => 'edit_posts',
            "menu_slug" => $main_page_slug,
            "callback" => array(acf_get_instance('DOT\Core\FieldGroup'), 'layouts_list'),
            "icon" => DOT_THEME_ASSETS_URL . '/icons/logo-dot.svg',
            "position" => '82'
        );

        add_menu_page(
            $main_page["page_title"], $main_page["menu_title"], $main_page["capability"], $main_page["menu_slug"], $main_page["callback"], $main_page["icon"], $main_page["position"]
        );

        add_submenu_page(
            $main_page_slug,
            __('Dispositions', 'dotcore'),
            __('Dispositions', 'dotcore'),
            'edit_posts',
            'edit.php?post_type=acf-field-group&layouts=1',
            null,
            20,
        );
    }
}

//            acf_add_options_page(array(
//                'page_title' => ,
//                'menu_title' => __('Réglages du site', 'dotcore'),
//                'menu_slug' => ,
//                'capability' => 'edit_posts',
//                'position' => '',
//                'parent_slug' => '',
//                'icon_url' => ,
//                'redirect' => false,
//                'post_id' => 'options',
//                'autoload' => false,
//                'update_button' => __('Mettre à jour', 'dotcore'),
//                'updated_message' => __('Réglages mis à jour', 'dotcore'),
//            ));