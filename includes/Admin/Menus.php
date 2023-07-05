<?php

namespace DOT\Core\Admin;

use DOT\Core\Main\Components;

class Menus {

    private array $submenus;

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
            "icon" => DOT_CORE_ASSETS_URL . '/icons/logo-dot.svg',
            "position" => '80'
        );

        add_menu_page(
            $main_page["page_title"],
            $main_page["menu_title"],
            $main_page["capability"],
            $main_page["menu_slug"],
            $main_page["callback"],
            $main_page["icon"],
            $main_page["position"]
        );

        $submenus = array(
            array(
                'type' => 'menu',
                'parent_slug' => $main_page_slug,
                'page_title' => __('Layouts', 'dotcore'),
                'menu_title' => __('Layouts', 'dotcore'),
                'capability' => 'edit_posts',
                'menu_slug' => 'edit.php?post_type=acf-field-group&layouts=1',
                'callback' => null,
                'position' => 20,
            ),
            array(
                'type' => 'menu',
                'parent_slug' => $main_page_slug,
                'page_title' => __('Layout Parts', 'dotcore'),
                'menu_title' => __('Layout Parts', 'dotcore'),
                'capability' => 'edit_posts',
                'menu_slug' => 'edit.php?post_type=acf-field-group&layout_parts=1',
                'callback' => null,
                'position' => 20,
            ),
            array(
                'type' => 'menu',
                'parent_slug' => $main_page_slug,
                'page_title' => __('Components', 'dotcore'),
                'menu_title' => __('Components', 'dotcore'),
                'capability' => 'edit_posts',
                'menu_slug' => 'edit.php?post_type=' . Components::$post_type,
                'callback' => null,
                'position' => 20,
            ),
            array(
                'type' => 'options',
                'page_title' => __('Réglages du site'),
                'menu_title' => __('Réglages du site'),
                'parent_slug' => $main_page_slug,
                'menu_slug' => 'theme-settings',
                'position' => 1,
            )
        );

        foreach ($submenus as $submenu) {
            if ($submenu['type'] === 'menu') {
                add_submenu_page($submenu['parent_slug'], $submenu['page_title'], $submenu['menu_title'], $submenu['capability'], $submenu['menu_slug'], $submenu['callback'], $submenu['position']);
                continue;
            }

            if ($submenu['type'] === 'options') {
                acf_add_options_sub_page(array(
                    'page_title' => $submenu['page_title'],
                    'menu_title' => $submenu['menu_title'],
                    'parent_slug' => $submenu['parent_slug'],
                    'menu_slug' => $submenu['menu_slug'],
                    'position' => $submenu['position'],
                ));
            }
        }

        $this->submenus = $submenus;
    }

    public function dashboard() {
        ob_start();
?>
        <div class="card">
            <h1> <?php _e('DotStarter', 'dotcore') ?></h1>
            <p><?php _e('Dashboard development is still ongoing. Please come back later.', 'dotcore') ?></p>
        </div>

<?php ob_end_flush();
    }

    public function get_submenus() {
        return $this->submenus;
    }
}
