<?php

if ( !defined( 'ABSPATH' ) || !function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

if ( !class_exists( 'Calyx_Utilities' ) ) {

	class Calyx_Utilities extends _Calyx_Core {

		function version_monitor( array $customization, $dependency ) {
		}

		function benchmark( $label = '' ) {
		}

		function get_file_path( string $after ) {
			return dirname( __DIR__ ) . '/' . $after;
		}

		function get_asset_url( string $theme_relative_path ) {
			if ( empty( $theme_relative_path ) )
				return false;

			if ( !function_exists( 'locate_template' ) )
				return $this->get_stylesheet_asset_url( $theme_relative_path );

			$path = $this->get_theme_asset_path( $theme_relative_path );

			if ( !empty( $path ) )
				return str_replace(
					array( get_stylesheet_directory(), get_template_directory() ),
					array( get_stylesheet_directory_uri(), get_template_directory_uri() ),
					$path
				);

			return false;

		}

			function get_stylesheet_asset_url( string $theme_relative_path ) {
				return str_replace(
					get_stylesheet_directory(),
					get_stylesheet_directory_uri(),
					$this->get_stylesheet_asset_path( $theme_relative_path )
				);
			}

			function get_template_asset_url( string $theme_relative_path ) {
				return str_replace(
					get_template_directory(),
					get_template_directory_uri(),
					$this->get_template_asset_path( $theme_relative_path )
				);
			}

		function get_theme_asset_path( string $theme_relative_path ) {
			if ( empty( $theme_relative_path ) )
				return false;

			if (
				!function_exists( 'locate_template' )
				|| !defined( 'STYLESHEETPATH' )
				||     empty( STYLESHEETPATH )
			)
				return $this->get_stylesheet_asset_path( $theme_relative_path );

			$path = locate_template( $theme_relative_path );

			if (
				empty( $path )
				|| (
					false !== stripos( $theme_relative_path, '.min.' )
					&& (
						SCRIPT_DEBUG
						|| ( stripos( $theme_relative_path, '.css' ) && !COMPRESS_CSS     )
						|| ( stripos( $theme_relative_path, '.js'  ) && !COMPRESS_SCRIPTS )
					)
				)
			)
				$unminified_path = locate_template( str_replace( '.min.', '.', $theme_relative_path ) );

			if ( !empty( $unminified_path ) )
				  return $unminified_path;

			if ( !empty( $path ) )
				  return $path;

			return false;
		}

			function get_stylesheet_asset_path( string $theme_relative_path ) {
				$directory = dirname( __DIR__ );

				if (
					function_exists( 'get_stylesheet_directory' )
					&& defined( 'STYLESHEETPATH' )
					&&   !empty( STYLESHEETPATH )
				)
					$directory = get_stylesheet_directory();

				return $this->_get_un_minified_asset_path(
					$directory,
					$theme_relative_path
				);
			}

			function get_template_asset_path( string $theme_relative_path ) {
				$directory = dirname( __DIR__ );

				if (
					function_exists( 'get_template_directory' )
					&& defined( 'TEMPLATEPATH' )
					&&   !empty( TEMPLATEPATH )
				)
					$directory = get_template_directory();

				return $this->_get_un_minified_asset_path(
					$directory,
					$theme_relative_path
				);
			}

			private function _get_un_minified_path( string $directory, string $theme_relative_path ) {
				$path = $directory . '/' . $theme_relative_path;

				if (
					!file_exists( $path )
					|| (
						false !== stripos( $theme_relative_path, '.min.' )
						&& (
							SCRIPT_DEBUG
							|| (  stripos( $theme_relative_path, '.css' ) && !COMPRESS_CSS     )
							|| (  stripos( $theme_relative_path, '.js'  ) && !COMPRESS_SCRIPTS )
						)
					)
				)
					$path = $directory . '/' . str_replace( '.min.', '.', $theme_relative_path );

				return !empty( $path )
					? $path
					: false;
			}

		/**
		 * Get template part.
		 *
		 * @param array $template_names Array of template paths to check.
		 * @param array $args           Array of variables for template part to use.
		 *
		 * @see locate_template()
		 */
		function get_template_part( $template_paths, $args = array() ) {
			$template_part = locate_template( $template_paths );

			if ( !empty( $template_part ) ) {
				extract( $args );

				$template_part_slug = trim( str_replace( '.php', '', str_replace( array(
					get_stylesheet_directory(),
					get_template_directory(),
				), '', $template_part ) ) );

				$action_handle = 'get_template_part_' . ltrim( $template_part_slug, '/' );

				do_action( $action_handle, $template_part_slug, '' );

				require $template_part;
			}
		}

	}

}

if ( !function_exists( 'get_theme_file_path' ) ) {

	function get_theme_file_path( string $after ) {
		return Calyx()->utils()->get_file_path( $after );
	}

}

if ( !function_exists( 'get_theme_asset_url' ) ) {

	function get_theme_asset_url( string $theme_relative_path ) {
		return Calyx()->utils()->get_asset_url( $theme_relative_path );
	}

}

?>
