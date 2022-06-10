<?php

namespace DOT\Core\Admin;

use DOT\Core\Main\Components;

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
            "callback" => array($this, 'dashboard'),
            "icon" => DOT_THEME_ASSETS_URL . '/icons/logo-dot.svg',
            "position" => '80'
        );

        add_menu_page(
            $main_page["page_title"], $main_page["menu_title"], $main_page["capability"], $main_page["menu_slug"], $main_page["callback"], $main_page["icon"], $main_page["position"]
        );

        $submenus = array(
            array(
                'parent_slug' => $main_page_slug,
                'page_title' => __('Layouts', 'dotcore'),
                'menu_title' => __('Layouts', 'dotcore'),
                'capability' => 'edit_posts',
                'menu_slug' => 'edit.php?post_type=acf-field-group&layouts=1',
                'callback' => null,
                'position' => 20,
            ),
            array(
                'parent_slug' => $main_page_slug,
                'page_title' => __('Layout Parts', 'dotcore'),
                'menu_title' => __('Layout Parts', 'dotcore'),
                'capability' => 'edit_posts',
                'menu_slug' => 'edit.php?post_type=acf-field-group&layout_parts=1',
                'callback' => null,
                'position' => 20,
            ),
            array(
                'parent_slug' => $main_page_slug,
                'page_title' => __('Components', 'dotcore'),
                'menu_title' => __('Components', 'dotcore'),
                'capability' => 'edit_posts',
                'menu_slug' => 'edit.php?post_type=' . Components::$post_type,
                'callback' => null,
                'position' => 20,
            )
        );

        foreach ($submenus as $submenu) {
            add_submenu_page($submenu['parent_slug'], $submenu['page_title'], $submenu['menu_title'], $submenu['capability'], $submenu['menu_slug'], $submenu['callback'], $submenu['position']);
        }
    }

    public function dashboard() {
        ob_start();
        ?>
        <div class="card">
            <h1> <?php _e('DotStarter', 'dotcore') ?></h1>
            <p>Dashboard development is still ongoing. Please come back later.</p>
        </div>

        <?php ob_end_flush();
    }
}

