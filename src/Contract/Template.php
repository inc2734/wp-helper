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
	 * Include php files
	 *
	 * @param string $directory
	 * @param boolean $exclude_underscore
	 * @return void
	 */
	public static function include_files( $directory, $exclude_underscore = false ) {
		$directory = realpath( $directory );
		if ( ! is_dir( $directory ) ) {
			return;
		}

		$iterator = new RecursiveDirectoryIterator( $directory, FilesystemIterator::SKIP_DOTS );
		$iterator = new RecursiveIteratorIterator( $iterator );

		$files = [];

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
			return;
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

		foreach ( $files as $filepath ) {
			include_once( $filepath );
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

		$iterator = new RecursiveDirectoryIterator( $directory, FilesystemIterator::SKIP_DOTS );
		$iterator = new RecursiveIteratorIterator( $iterator );

		$files = [];

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
			return;
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

		foreach ( $files as $filepath ) {
			$basepath = str_replace( $template_directory, '', $filepath );
			$basepath = str_replace( $stylesheet_directory, '', $basepath );
			$filepath = get_theme_file_path( $basepath );
			include_once( $filepath );
		}
	}
}
