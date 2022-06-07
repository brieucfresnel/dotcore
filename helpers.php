<?php

/**
 * @return Array
 */
function dot_get_layouts(): array {
    return acf_get_instance('\DOT\Core\Layouts')->get_layouts();
}


/**
 * @return void
 */
function the_dot_layouts() {
    if (has_flexible(acf_get_instance('\DOT\Core\MainFlexible')->get_field_key())):
        the_flexible(acf_get_instance('\DOT\Core\MainFlexible')->get_field_key());
    endif;
}


/**
 * @param $field_group
 * @return mixed
 */
function dot_is_layout($field_group) {
    return acf_get_instance('\DOT\Core\Layouts')->is_layout($field_group);
}


/**
 * @return mixed
 */
function dot_is_layout_screen() {
    return acf_get_instance('\DOT\Core\Layouts')->is_layout_screen();
}


/**
 * @param string $type
 * @param string $selector
 * @return void
 */
function the_layout_part(string $type, string $selector) {
    if(!get_sub_field($selector))
        return;

    $available = false;
    $available_parts = dot_get_layout_parts();

    foreach ($available_parts as $layout_part) {
        var_dump($layout_part['dot_layout_part_slug']);
        if ($layout_part['dot_layout_part_slug'] === $type) {
            $available = true;
        }
    }

    if ($available)
        get_template_part('dotstarter/layout-parts/' . $type . '/' . $type, null, get_sub_field($selector));
}


/**
 * @return Array
 */
function dot_get_layout_parts(): array {
    return acf_get_instance('\DOT\Core\LayoutParts')->get_layout_parts();
}


/**
 * @return array
 */
function dot_get_components(): array {
    return acf_get_instance('\DOT\Core\Components')->get_components();
}


/**
 * @param int $id
 * @return void
 */
function the_component(int $id): void {
    acf_get_instance('\DOT\Core\Components')->the_component($id);
}

function dot_get_component_post_type() {

}

/**
 * @return mixed
 */
function dot_is_component_screen() {
    $screen = get_current_screen();
    return is_admin() && $screen->post_type === \DOT\Core\Components::$post_type;
}

/**
 * @param $field_group
 * @return mixed
 */
function dot_is_layout_part($field_group) {
    return acf_get_instance('\DOT\Core\LayoutParts')->is_layout_part($field_group);
}


/**
 * @return mixed
 */
function dot_is_layout_part_screen() {
    return acf_get_instance('\DOT\Core\LayoutParts')->is_layout_part_screen();
}

function dot_get_field_groups_by_location($param, $value, $operator) {

}

/**
 * Get field group attached to a component
 *
 * @param int $component_id
 * @return array|false
 */
function get_component_field_group(int $component_id) {
    return acf_get_instance('\DOT\Core\Components')->get_field_group($component_id);
}


/**
 * Récupérer l'URL du custom logo avec un fallback s'il n'est pas défini
 * @return string
 */
function dot_get_logo_url(): string {
    $logo_url = wp_get_attachment_url(get_theme_mod('custom_logo'));

    return $logo_url ? esc_url($logo_url) : get_template_directory_uri() . '/assets/img/logo.png';
}

/**
 * print_r with <pre> tag before and after
 *
 * @param $array
 * @return void
 */
function dot_print_r($array) {
    echo '<pre>';
    var_dump($array);
    echo '</pre>';
}

/**
 * @param $email
 * @param $encode
 * @param $reverse
 * @param $before
 * @param $after
 * @return mixed|string
 */
function dot_obfuscate_string($string, $encode = 1, $reverse = 0, $before = '', $after = '') {
    $output = '';
    if ($reverse) {
        $string = strrev($string);
        $output = $before;
    }
    if ($encode) {
        for ($i = 0; $i < (strlen($string)); $i++) {
            $output .= '&#' . ord($string[$i]) . ';';
        }
    } else {
        $output .= $string;
    }
    if ($reverse) {
        $output .= $after;
    }

    return $output;
}
