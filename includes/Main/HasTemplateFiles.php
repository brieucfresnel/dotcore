<?php

namespace DOT\Core\Main;

trait HasTemplateFiles {

	/**
	 * Enqueue css and js files
	 *
	 * @param string $slug
	 * @param string $type
	 *
	 * @return void
	 */
	public function enqueue( string $slug, string $type) {
		global $is_preview;

		$handle = $type . '-' . $slug;

		$baseURI = '';
		if ( $type === 'component' ) {
			$baseURI = DOT_THEME_COMPONENTS_URI;
		} else if ( $type === 'layout_part' ) {
			$baseURI = DOT_THEME_LAYOUT_PARTS_URI;
		} else if ( $type === 'layout' ) {
			$baseURI = DOT_THEME_LAYOUTS_URI;
		}

		$style  = $baseURI . $slug . '/' . $slug . '.css';
//		$script = $baseURI . $slug . '/' . $slug . '.js';

		// Check
		if ( ! empty( $style ) ) {

			// URL starting with current domain
			if ( stripos( $style, home_url() ) === 0 ) {

				$style = str_replace( home_url(), '', $style );
				$style = substr(ABSPATH, 0, -1) . $style;
			}

			// Locate
			$style_file = acfe_locate_file_url( $style );

			// Front-end
			if ( ! empty( $style_file ) ) {
				wp_enqueue_style( $handle, $style_file, array(), false, 'all' );
			}

		}

		// Check
//		if ( ! empty( $script ) ) {
//
//			// URL starting with current domain
//			if ( stripos( $script, home_url() ) === 0 ) {
//
//				$script = str_replace( home_url(), '', $script );
//
//			}
//
//			// Locate
//			$script_file = acfe_locate_file_url( $script );
//
//
//			// Front-end
//			if ( ! $is_preview || ( stripos( $script, 'http://' ) === 0 || stripos( $script, 'https://' ) === 0 || stripos( $script, '//' ) === 0 ) ) {
//
//				if ( ! empty( $script_file ) ) {
//
//					wp_enqueue_script( $handle, $script_file, array(), false, true );
//
//				}
//
//			} else {
//
//				$path      = pathinfo( $script );
//				$extension = $path['extension'];
//
//				$script_preview = substr( $script, 0, - strlen( $extension ) - 1 );
//				$script_preview .= '-preview.' . $extension;
//
//				$script_preview = acfe_locate_file_url( $script_preview );
//
//				// Enqueue
//				if ( ! empty( $script_preview ) ) {
//
//					wp_enqueue_script( $handle . '-preview', $script_preview, array(), false, true );
//
//				} elseif ( ! empty( $script_file ) ) {
//
//					wp_enqueue_script( $handle, $script_file, array(), false, true );
//
//				}
//
//			}
//
//		}
	}

	/**
	 * Include template file if it exists
	 *
	 * @param string $slug
	 * @param string $type
	 * @param string|null $selector
	 *
	 * @return void
	 */
	public function render( string $slug, string $type, string $selector = null ) {

		$basePath = '';

		if ( $type === 'component' ) {
			$basePath = DOT_THEME_COMPONENTS_PATH;
		} else if ( $type === 'layout_part' ) {
			$basePath = DOT_THEME_LAYOUT_PARTS_PATH;
		} else if ( $type === 'layout' ) {
			$basePath = DOT_THEME_LAYOUTS_PATH;
		}

		$file       = $basePath . $slug . '/' . $slug . '.php';
		$file_found = acfe_locate_file_path( $file );

		if($selector) {
			$fields = get_field($selector) ? get_field($selector) : get_sub_field($selector);
		}

		if ( ! empty( $file_found ) ) {
			include( $file_found );
		}
	}
}