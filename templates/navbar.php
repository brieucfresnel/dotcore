<div class="acf-admin-toolbar">
    <h2>DotStarter</h2>
    <?php
    $submenus = acf_get_instance('\DOT\Core\Admin\Menus')->get_submenus();

    if (!empty($submenus)):
        // Sort menu items by position
        usort($submenus, function ($a, $b) {
            return $a['position'] <=> $b['position'];
        });

        foreach ($submenus as $menu_item):
            if ($menu_item['type'] === 'options'):
                $menu_item['menu_slug'] = 'admin.php?page=' . $menu_item['menu_slug'];
            endif; ?>
            <a href="<?= $menu_item['menu_slug'] ?>"
               class="acf-tab <?= strstr($menu_item['menu_slug'], $_SERVER['QUERY_STRING']) ? ' is-active' : '' ?>">
                <?= $menu_item['menu_title'] ?>
            </a>
        <?php endforeach;
    endif; ?>
</div>
