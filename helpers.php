<?php

/**
 * @return Array
 */
function dot_get_layouts() : array {
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
 * Récupérer l'URL du custom logo avec un fallback s'il n'est pas défini
 * @return string
 */
function dot_get_logo_url(): string {
    $logo_url = wp_get_attachment_url(get_theme_mod('custom_logo'));

    return $logo_url ? esc_url($logo_url) : get_template_directory_uri() . '/assets/img/logo.png';
}

function dot_print_r($array) {
    echo '<pre>';
    var_dump($array);
    echo '</pre>';
}

function dot_obfuscate_string($email, $encode = 1, $reverse = 0, $before = '<span class="email">', $after = '</span>') {
    $output = '';
    if ($reverse) {
        $email = strrev($email);
        $output = $before;
    }
    if ($encode) {
        for ($i = 0; $i < (strlen($email)); $i++) {
            $output .= '&#' . ord($email[$i]) . ';';
        }
    } else {
        $output .= $email;
    }
    if ($reverse) {
        $output .= $after;
    }

    return $output;
}