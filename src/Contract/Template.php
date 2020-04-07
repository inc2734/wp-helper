<?php
/**
 * @package inc2734/wp-helper
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Inc2734\WP_Helper\Contract;

use DirectoryIterator;

trait Template {

	/**
	 * Return included files and directories
	 *
	 * @param string $directory
	 * @param boolean $exclude_underscore
	 * @return void
	 */
	public static function get_include_files( $directory, $exclude_underscore = false ) {
		$return = [
			'files'       => [],
			'directories' => [],
		];

		if ( ! is_dir( $directory ) ) {
			return $return;
		}

		$directory_iterator = new DirectoryIterator( $directory );

		foreach ( $directory_iterator as $file ) {
			if ( $file->isDot() ) {
				continue;
			}

			if ( $file->isDir() ) {
				$return['directories'][] = $file->getPathname();
				continue;
			}

			if ( 'php' !== $file->getExtension() ) {
				continue;
			}

			if ( $exclude_underscore ) {
				if ( 0 === strpos( $file->getBasename(), '_' ) ) {
					continue;
				}
			}

			$return['files'][] = $file->getPathname();
		}

		return $return;
	}

	/**
	 * Returns PHP file list
	 *
	 * @param string Directory path
	 * @return array PHP file list
	 */
	public static function glob_recursive( $path ) {
		$files = [];
		if ( preg_match( '/\\' . DIRECTORY_SEPARATOR . 'vendor$/', $path ) ) {
			return $files;
		}

		foreach ( glob( $path . '/*' ) as $file ) {
			if ( is_dir( $file ) ) {
				$files = array_merge( $files, static::glob_recursive( $file ) );
			} elseif ( preg_match( '/\.php$/', $file ) ) {
				$files[] = $file;
			}
		}

		return $files;
	}

	/**
	 * Include php files
	 *
	 * @param string $directory
	 * @param boolean $exclude_underscore
	 * @return void
	 */
	public static function include_files( $directory, $exclude_underscore = false ) {
		if ( ! is_dir( $directory ) ) {
			return;
		}

		$files = static::get_include_files( $directory, $exclude_underscore );

		foreach ( $files['files'] as $file ) {
			include_once( $file );
		}

		foreach ( $files['directories'] as $directory ) {
			static::include_files( $directory, $exclude_underscore );
		}
	}

	/**
	 * Include php files
	 *
	 * @param string $directory
	 * @param boolean $exclude_underscore
	 * @return void
	 */
	public static function load_theme_files( $directory, $exclude_underscore = false ) {
		$directory = realpath( $directory );

		if ( ! is_dir( $directory ) ) {
			return;
		}

		$template_directory   = realpath( get_template_directory() );
		$stylesheet_directory = realpath( get_stylesheet_directory() );

		$files = static::get_include_files( $directory, $exclude_underscore );

		foreach ( $files['files'] as $file ) {
			$file = realpath( $file );
			$file = str_replace( $template_directory, '', $file );
			$file = str_replace( $stylesheet_directory, '', $file );
			$file = get_theme_file_path( $file );
			include_once( $file );
		}

		foreach ( $files['directories'] as $directory ) {
			static::load_theme_files( $directory, $exclude_underscore );
		}
	}
}
