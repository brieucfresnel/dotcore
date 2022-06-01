<?php

namespace DOT\Core;

class Components {
    /**
     * Get the registered components
     *
     * @return array
     */
    public function get_components() : array {
        $field_groups = acf_get_field_groups();
        $components = [];

        foreach ($field_groups as $field_group) {
            if (!$this->is_component($field_group)) {
                continue;
            }

            $components[] = $field_group;
        }

        return $components;
    }

    /**
     * @param $field_group
     * @return mixed|null
     */
    public function is_component($field_group) {

        // If $field_group is an ID
        if(!is_array($field_group)) {
            $field_group = acf_get_field_group($field_group);
        }

        return acf_maybe_get($field_group, 'dot_is_component');
    }

    /**
     * @return bool
     */
    public function is_component_screen(): bool {
        global $typenow;

        // If not field groups page, return
        if ( $typenow !== 'acf-field-group' ) {
            return false;
        }

        // Get screens
        $is_layout_list   = acf_is_screen( 'edit-acf-field-group' ) && acf_maybe_get_GET( 'components' ) === '1';
        $is_layout_single = acf_is_screen( 'acf-field-group' );

        if ( $is_layout_list ) {

            // Layout list
            return true;

        } elseif ( $is_layout_single ) {

            // Check if layout single page
            $is_layout_single_new  = acf_maybe_get_GET( 'component' ) === '1';
            $is_layout_single_edit = $this->is_component( acf_maybe_get_GET( 'post' ) );
            $is_layout_single_save = isset( $_REQUEST['acf_field_group']['dot_is_component'] );

            // Layout single
            if ( $is_layout_single_new || $is_layout_single_edit || $is_layout_single_save ) {
                return true;
            }
        }

        return false;
    }
}