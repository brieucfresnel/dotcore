<?php

namespace DOT\Core;

class FieldGroup {

    public function __construct() {
        add_action('load-edit.php', array($this, 'load_layouts_list'));
    }

    public function load_layouts_list() {
        global $typenow;
        // If not admin, not main query and not layouts screen, return
        if (!is_admin() || $typenow !== 'acf-field-group' || !acf_maybe_get_GET('layouts')) {
            return;
        }

        // Get admin field groups class
        $acf_field_groups = acf_get_instance('ACF_Admin_Field_Groups');

        add_action('pre_get_posts', array($this, 'pre_get_posts'));
    }

    public function pre_get_posts(\WP_Query $query) {
        if ( !is_admin() || !$query->is_main_query() || is_post_type_archive( 'acf-field-group' ) ) {
            return;
        }

        // Get flexible layouts only
        $query->set(
            's', 's:13:"dot_is_layout";i:1'
        );

//        dot_print_r($query);
    }
}

?>