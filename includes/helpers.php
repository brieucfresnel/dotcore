<?php

/**
 * @return Array
 */
function dot_get_layouts(): array {
	return acf_get_instance( '\DOT\Core\Main\Layouts' )->get_layouts();
}

/**
 * @return void
 */
function the_dot_layouts() {
	if ( has_flexible( acf_get_instance( '\DOT\Core\Main\MainFlexible' )->get_field_key() ) ):
		the_flexible( acf_get_instance( '\DOT\Core\Main\MainFlexible' )->get_field_key() );
	endif;
}

/**
 * @param string $slug
 * @param string|false $selector
 *
 * @return void
 */
function the_dot_layout( string $slug, $selector = false ) {
	acf_get_instance( '\DOT\Core\Main\Layouts' )->the_layout( $slug, $selector );
}


/**
 * @param $field_group
 *
 * @return mixed
 */
function dot_is_layout( $field_group ) {
	return acf_get_instance( '\DOT\Core\Main\Layouts' )->is_layout( $field_group );
}


/**
 * @return mixed
 */
function dot_is_layout_screen() {
	return acf_get_instance( '\DOT\Core\Main\Layouts' )->is_layout_screen();
}


/**
 * @param string $type
 * @param string $selector
 *
 * @return void
 */
function the_layout_part( string $type, string $selector ) {
	return acf_get_instance( '\DOT\Core\Main\LayoutParts' )->the_layout_part( $type, $selector );
}


/**
 * @return Array
 */
function dot_get_layout_parts(): array {
	return acf_get_instance( '\DOT\Core\Main\LayoutParts' )->get_layout_parts();
}


/**
 * @return array
 */
function dot_get_components(): array {
	return acf_get_instance( '\DOT\Core\Main\Components' )->get_components();
}


/**
 * @param int $slug
 *
 * @return void
 */
function dot_the_component( string $slug ): void {
	acf_get_instance( '\DOT\Core\Main\Components' )->the_component( $slug );
}

/**
 * @param string[] $location_rule
 *
 * @return array
 */
function get_field_group_from_location_rule( array $location_rule ): array {
	// need to create cache or transient for this data?
	$result           = array();
	$acf_field_groups = acf_get_field_groups();

	foreach ( $acf_field_groups as $acf_field_group ) {
		foreach ( $acf_field_group['location'] as $group_locations ) {
			foreach ( $group_locations as $rule ) {
				if ( $rule === $location_rule ) {
					$result[] = acf_get_fields( $acf_field_group );
				}
			}
		}
	}

	return $result;
}

/**
 * @return mixed
 */
function dot_is_component_screen() {
	$screen = get_current_screen();

	return is_admin() && $screen->post_type === \DOT\Core\Main\Components::$post_type;
}

/**
 * @param $field_group
 *
 * @return mixed
 */
function dot_is_layout_part( $field_group ) {
	return acf_get_instance( '\DOT\Core\Main\LayoutParts' )->is_layout_part( $field_group );
}


/**
 * @return mixed
 */
function dot_is_layout_part_screen() {
	return acf_get_instance( '\DOT\Core\Main\LayoutParts' )->is_layout_part_screen();
}

/**
 * Get field group attached to a component
 *
 * @param int $component_id
 *
 * @return array|false
 */
function get_component_field_group( int $component_id ) {
	return acf_get_instance( '\DOT\Core\Main\Components' )->get_field_group( $component_id );
}


/**
 * Récupérer l'URL du custom logo avec un fallback s'il n'est pas défini
 * @return string
 */
function dot_get_logo_url(): string {
	$logo_url = wp_get_attachment_url( get_theme_mod( 'custom_logo' ) );

	return $logo_url ? esc_url( $logo_url ) : get_template_directory_uri() . '/assets/img/logo.png';
}

/**
 * print_r with <pre> tag before and after
 *
 * @param $array
 *
 * @return void
 */
function dot_print_r( $array ) {
	echo '<pre>';
	var_dump( $array );
	echo '</pre>';
}

/**
 * @param $email
 * @param $encode
 * @param $reverse
 * @param $before
 * @param $after
 *
 * @return mixed|string
 */
function dot_obfuscate_string( $string, $encode = 1, $reverse = 0, $before = '', $after = '' ) {
	$output = '';
	if ( $reverse ) {
		$string = strrev( $string );
		$output = $before;
	}
	if ( $encode ) {
		for ( $i = 0; $i < ( strlen( $string ) ); $i ++ ) {
			$output .= '&#' . ord( $string[ $i ] ) . ';';
		}
	} else {
		$output .= $string;
	}
	if ( $reverse ) {
		$output .= $after;
	}

	return $output;
}
