<?php

namespace DOT\Core\Main;

class Layouts {
	use HasTemplateFiles;

	/**
	 * Get the registered flexible layouts
	 *
	 * @return array
	 */
	public function get_layouts(): array {
		$field_groups = acf_get_field_groups();
		$layouts      = [];

		foreach ( $field_groups as $field_group ) {
			if ( ! $this->is_layout( $field_group ) ) {
				continue;
			}

			$layouts[] = $field_group;
		}

		return $layouts;
	}

	/**
	 * @param string|integer $id
	 *
	 * @return array | false
	 */
	public function get_layout( $id ) {
		// Get from ID
		if ( is_numeric( $id ) ) {
			return acf_get_field_group( $id );
		}

		// Get from slug
		if ( is_string( $id ) ) {
			$field_groups = $this->get_layouts();

			foreach ( $field_groups as $field_group ) {
				if ( $field_group['dot_layout_slug'] === $id ) {
					return $field_group;
				}
			}
		}

		return false;
	}

	/**
	 * Render layout template
	 *
	 * @param string $type
	 * @param bool|string $selector
	 *
	 * @return void
	 */
	public function the_layout( string $slug ): void {

		// Enqueue styles and script
		$this->enqueue( $slug, 'layout' );

		// Render template
		$this->render( $slug, 'layout' );
	}

	/**
	 * Check whether a field group is a flexible layout
	 *
	 * @param $field_group
	 *
	 * @return mixed|null
	 */
	public function is_layout( $field_group ) {

		// If $field_group is an ID
		if ( ! is_array( $field_group ) ) {
			$field_group = acf_get_field_group( $field_group );
		}

		return acf_maybe_get( $field_group, 'dot_is_layout' );
	}

	/**
	 * Check whether current screen is a layout admin screen
	 *
	 * @return bool
	 */
	public function is_layout_screen(): bool {
		global $typenow;

		// If not field groups page, return
		if ( $typenow !== 'acf-field-group' ) {
			return false;
		}

		// Get screens
		$is_layout_list   = acf_is_screen( 'edit-acf-field-group' ) && acf_maybe_get_GET( 'layouts' ) === '1';
		$is_layout_single = acf_is_screen( 'acf-field-group' );

		if ( $is_layout_list ) {

			// Layout list
			return true;

		} elseif ( $is_layout_single ) {

			// Check if layout single page
			$is_layout_single_new  = acf_maybe_get_GET( 'layout' ) === '1';
			$is_layout_single_edit = $this->is_layout( acf_maybe_get_GET( 'post' ) );
			$is_layout_single_save = isset( $_REQUEST['acf_field_group']['dot_is_layout'] );

			// Layout single
			if ( $is_layout_single_new || $is_layout_single_edit || $is_layout_single_save ) {
				return true;
			}
		}

		return false;
	}
}