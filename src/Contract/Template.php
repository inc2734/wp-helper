<?php
/**
 * @package inc2734/wp-helper
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Inc2734\WP_Helper\Contract;

use FilesystemIterator;
use DirectoryIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

trait Template {

	/**
	 * Recursively returns a list of files in the specified directory.
	 *
	 * @param string  $directory          Target directory.
	 * @param boolean $exclude_underscore Return true if you want to exclude underscore.
	 */
	public static function get_files( $directory, $exclude_underscore = false ) {
		$files = [];

		$directory = realpath( $directory );
		if ( ! is_dir( $directory ) ) {
			return $files;
		}

		$iterator = new RecursiveDirectoryIterator( $directory, FilesystemIterator::SKIP_DOTS );
		$iterator = new RecursiveIteratorIterator( $iterator );

		foreach ( $iterator as $file ) {
			if ( ! $file->isFile() ) {
				continue;
			}

			if ( 'php' !== $file->getExtension() ) {
				continue;
			}

			if ( $exclude_underscore && 0 === strpos( $file->getBasename(), '_' ) ) {
				continue;
			}

			$files[] = realpath( $file->getPathname() );
		}

		if ( ! $files ) {
			return $files;
		}

		usort(
			$files,
			function( $a, $b ) {
				$adeps = substr_count( $a, DIRECTORY_SEPARATOR );
				$bdeps = substr_count( $b, DIRECTORY_SEPARATOR );

				if ( $adeps === $bdeps ) {
					return 0;
				}

				return $adeps > $bdeps ? 1 : -1;
			}
		);

		return $files;
	}

	/**
	 * Recursively returns a list of files in the specified directory.
	 * Return file pathes are relative path from the theme.
	 *
	 * @param string  $directory          Target directory. Full path or directory slug.
	 * @param boolean $exclude_underscore Return true if you want to exclude underscore.
	 */
	public static function get_theme_files( $directory, $exclude_underscore = false ) {
		$directory          = realpath( $directory );
		$template_directory = realpath( get_template_directory() );

		// Relative path
		if ( false === strpos( $directory, realpath( ABSPATH ) ) ) {
			$directory = $template_directory . DIRECTORY_SEPARATOR . $directory;
		}

		$files = static::get_files( $directory, $exclude_underscore );
		if ( ! $files ) {
			return $files;
		}

		$relative_files = [];
		foreach ( $files as $filepath ) {
			$relative_files[] = str_replace(
				realpath( trailingslashit( $template_directory ) ),
				'',
				realpath( $filepath )
			);
		}

		return $relative_files;
	}

	/**
	 * Load file using include_once().
	 *
	 * @param string|array $directory_or_files Target directory (Full path or directory slug) or filepath list.
	 * @param boolean      $exclude_underscore Return true if you want to exclude underscore.
	 */
	public static function include_files( $directory_or_files, $exclude_underscore = false ) {
		if ( is_array( $directory_or_files ) ) {
			$files = $directory_or_files;
			if ( $exclude_underscore ) {
				$files = array_filter(
					$files,
					function( $filepath ) {
						return 0 !== strpos( basename( $filepath ), '_' );
					}
				);
			}
		} else {
			$directory = $directory_or_files;
			$files     = static::get_files( $directory, $exclude_underscore );
			if ( ! $files ) {
				return $files;
			}
		}

		foreach ( $files as $filepath ) {
			static::include_file( $filepath );
		}
	}

	/**
	 * Load file using include_once().
	 *
	 * @param string $filepath File path.
	 */
	public static function include_file( $filepath ) {
		if ( file_exists( $filepath ) ) {
			include_once( $filepath );
		}
	}

	/**
	 * Include php files using get_theme_file_path().
	 *
	 * @param string|array $directory_or_files Target directory (Full path or directory slug) or filename list.
	 * @param boolean      $exclude_underscore Return true if you want to exclude underscore.
	 */
	public static function load_theme_files( $directory_or_files, $exclude_underscore = false ) {
		if ( is_array( $directory_or_files ) ) {
			$files = $directory_or_files;
			if ( $exclude_underscore ) {
				$files = array_filter(
					$files,
					function( $filename ) {
						return 0 !== strpos( $filename, '_' );
					}
				);
			}
		} else {
			$directory = $directory_or_files;
			$files     = static::get_theme_files( $directory, $exclude_underscore );
			if ( ! $files ) {
				return $files;
			}
		}

		foreach ( $files as $filename ) {
			static::load_theme_file( $filename );
		}
	}

	/**
	 * Load file using get_theme_file_path().
	 *
	 * @param string $filename File basename.
	 */
	public static function load_theme_file( $filename ) {
		$filepath = get_theme_file_path( $filename );
		if ( file_exists( $filepath ) ) {
			include_once( $filepath );
		}
	}
}
