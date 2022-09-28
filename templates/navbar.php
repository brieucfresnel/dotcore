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
<?php


global $post_type, $post_type_object, $acf_page_title;
$post_new_file = "post-new.php?post_type=$post_type";

$page_title = false;
if ( isset( $acf_page_title ) ) {
    $page_title = $acf_page_title;
} elseif ( is_object( $post_type_object ) ) {
    $page_title = $post_type_object->labels->name;
}

if($_GET['layouts'] === '1') {
    $page_title = __('Sections', 'dotcore');
}
if($_GET['layout_parts'] === '1') {
    $page_title = __('Parties de sections', 'dotcore');
}
if($_GET['components'] === '1') {
    $page_title = __('Composants', 'dotcore');
}
if ( $page_title && get_current_screen()->base === 'edit' ) {
    ?>
    <div class="acf-headerbar">

        <h1 class="acf-page-title">
            <?php
            echo esc_html( $page_title );
            ?>
        </h1>

        <?php
        if ( ! empty( $post_type_object ) && current_user_can( $post_type_object->cap->create_posts ) ) {
            echo ' <a href="' . esc_url( admin_url( $post_new_file ) ) . '" class="acf-btn acf-btn-sm"><i class="acf-icon acf-icon-plus"></i>' . esc_html( $post_type_object->labels->add_new ) . '</a>';
        }
        ?>

    </div>
<?php } ?>

