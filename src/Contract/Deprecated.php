<?php
/**
 * @package inc2734/wp-helper
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Inc2734\WP_Helper\Contract;

use DirectoryIterator;

trait Deprecated {

	/**
	 * Return included files and directories.
	 *
	 * @deprecated
	 *
	 * @param string  $directory          Target directory.
	 * @param boolean $exclude_underscore Return true if you want to exclude underscore.
	 * @return array
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
	 * Returns PHP file list.
	 *
	 * @deprecated
	 *
	 * @param string $path Directory path.
	 * @return array PHP file list.
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
}

