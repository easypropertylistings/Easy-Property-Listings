<?php
/**
 * Hook reference for the EPL code reference.
 *
 * Lists every EPL action and filter (do_action / apply_filters) in core and
 * writes hook-docs.html into the ApiGen output, using ApiGen's own page shell
 * so it looks like the rest of the site. Each hook links to the line it is
 * fired on in ApiGen's source pages.
 *
 * Run from the plugin folder, after `apigen`:
 *   php apigen/hook-docs.php [output folder, default epl-apidocs]
 *
 * Adapted from WooCommerce's hook-docs.
 *
 * @package EPL
 * @since 3.6.2 Command line script for ApiGen 7: scans every lib/ folder (page builders, integrations and updates were missed), links hooks to their source line, and copies ApiGen 7's page layout.
 */

// phpcs:disable WordPress.Security.EscapeOutput, WordPress.WP.AlternativeFunctions -- Command line tool: WordPress isn't loaded, so its escaping and filesystem functions don't exist. Output is escaped with htmlspecialchars().

if ( 'cli' !== PHP_SAPI ) {
	exit( "Run from the command line: php apigen/hook-docs.php\n" );
}

/**
 * Finds EPL hooks in the plugin's PHP files.
 *
 * @since 3.6.2 Command line version for ApiGen 7.
 */
class EPL_Hook_Finder {

	/**
	 * Plugin folder, with a trailing slash.
	 *
	 * @var string
	 */
	private $plugin_dir;

	/**
	 * Set up.
	 *
	 * @param string $plugin_dir Plugin folder.
	 *
	 * @since 3.6.2
	 */
	public function __construct( $plugin_dir ) {
		$this->plugin_dir = rtrim( $plugin_dir, '/' ) . '/';
	}

	/**
	 * Files to scan, grouped under a heading per lib/ folder.
	 *
	 * @return array Heading => list of paths relative to the plugin folder.
	 *
	 * @since 3.6.2 Every lib/ folder, found automatically, so new folders are never missed.
	 */
	public function get_file_groups() {
		$groups = array();

		foreach ( glob( $this->plugin_dir . 'lib/*', GLOB_ONLYDIR ) as $folder ) {
			$name  = basename( $folder );
			$files = $this->get_php_files( 'lib/' . $name );
			if ( $files ) {
				$groups[ ucwords( str_replace( '-', ' ', $name ) ) . ' (lib/' . $name . ')' ] = $files;
			}
		}

		$groups['Other (plugin folder)'] = array( 'easy-property-listings.php', 'uninstall.php' );

		return $groups;
	}

	/**
	 * Every PHP file under a folder, sorted.
	 *
	 * @param string $folder Folder relative to the plugin folder.
	 *
	 * @return array Paths relative to the plugin folder.
	 *
	 * @since 3.6.2
	 */
	private function get_php_files( $folder ) {
		$files    = array();
		$iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $this->plugin_dir . $folder, FilesystemIterator::SKIP_DOTS ) );

		foreach ( $iterator as $file ) {
			if ( 'php' === $file->getExtension() ) {
				$files[] = substr( $file->getPathname(), strlen( $this->plugin_dir ) );
			}
		}

		sort( $files );

		return $files;
	}

	/**
	 * Hooks fired in a list of files.
	 *
	 * Hook names built from variables are shown with the variable part in
	 * braces, e.g. epl_{$post_type}_meta.
	 *
	 * @param array $files Paths relative to the plugin folder.
	 *
	 * @return array Hook name => type, path and line of the first use, and every file it's used in.
	 *
	 * @since 3.6.2
	 */
	public function find_hooks( $files ) {
		$hooks = array();

		foreach ( $files as $path ) {
			$tokens     = token_get_all( file_get_contents( $this->plugin_dir . $path ) );
			$token_type = false;

			foreach ( $tokens as $index => $token ) {
				if ( ! is_array( $token ) ) {
					continue;
				}

				if ( 'do_action' === $token[1] ) {
					$token_type = 'action';
					continue;
				}

				if ( 'apply_filters' === $token[1] ) {
					$token_type = 'filter';
					continue;
				}

				if ( ! $token_type || '' === trim( $token[1] ) ) {
					continue;
				}

				$hook = $this->read_hook_name( $tokens, $index );

				if ( isset( $hooks[ $hook ] ) ) {
					$hooks[ $hook ]['files'][] = $path;
				} else {
					$hooks[ $hook ] = array(
						'type'  => $token_type,
						'path'  => $path,
						'line'  => $token[2],
						'files' => array( $path ),
					);
				}

				$token_type = false;
			}
		}

		// Only EPL hooks, not WordPress ones such as the_content.
		foreach ( array_keys( $hooks ) as $hook ) {
			if ( false === strpos( $hook, 'epl_' ) && false === strpos( $hook, 'property' ) ) {
				unset( $hooks[ $hook ] );
			}
		}

		ksort( $hooks );

		return $hooks;
	}

	/**
	 * Hook name starting at a token, joining the parts of names built with variables.
	 *
	 * @param array $tokens All tokens of the file.
	 * @param int   $index  Token holding the start of the hook name.
	 *
	 * @return string Hook name.
	 *
	 * @since 3.6.2 Same logic as the old hook-docs.php.
	 */
	private function read_hook_name( $tokens, $index ) {
		$hook = trim( $tokens[ $index ][1], "'\"" );

		if ( '_' !== substr( $hook, -1 ) ) {
			return $hook;
		}

		$hook .= '{';
		$open  = true;
		$loop  = 0;

		while ( isset( $tokens[ $index + $loop + 1 ] ) ) {
			++$loop;
			$next = $tokens[ $index + $loop ];
			$next = trim( trim( is_string( $next ) ? $next : $next[1], '"' ), "'" );

			if ( in_array( $next, array( '.', '{', '}', '"', "'", ' ', '' ), true ) ) {
				continue;
			}

			if ( in_array( $next, array( ',', ';', ')' ), true ) ) {
				if ( $open ) {
					$hook .= '}';
				}
				break;
			}

			if ( '_' === substr( $next, 0, 1 ) ) {
				$next = '}' . $next;
				$open = false;
			}

			if ( '_' === substr( $next, -1 ) ) {
				$next .= '{';
				$open  = true;
			}

			$hook .= $next;
		}

		return $hook;
	}

	/**
	 * Link to the line in ApiGen 7's source page, e.g. source-lib.includes.functions.html#43.
	 *
	 * @param string $path Path relative to the plugin folder.
	 * @param int    $line Line number.
	 *
	 * @return string Relative URL.
	 *
	 * @since 3.6.2
	 */
	public function source_url( $path, $line ) {
		return 'source-' . str_replace( '/', '.', substr( $path, 0, -4 ) ) . '.html#' . $line;
	}
}

$epl_plugin_dir = dirname( __DIR__ );
$epl_output_dir = isset( $argv[1] ) ? rtrim( $argv[1], '/' ) : $epl_plugin_dir . '/epl-apidocs';
$epl_tree_page  = $epl_output_dir . '/tree.html';

if ( ! file_exists( $epl_tree_page ) ) {
	fwrite( STDERR, "No $epl_tree_page: run apigen first.\n" );
	exit( 1 );
}

$epl_finder = new EPL_Hook_Finder( $epl_plugin_dir );
$epl_total  = 0;

ob_start();

echo "<h1>Action and Filter Hook Reference</h1>\n";

foreach ( $epl_finder->get_file_groups() as $epl_heading => $epl_files ) {
	$epl_hooks = $epl_finder->find_hooks( $epl_files );

	if ( ! $epl_hooks ) {
		continue;
	}

	$epl_total += count( $epl_hooks );

	echo '<table class="table epl-hooks-table">' . "\n";
	echo '<tr><th class="table-heading" colspan="3">' . htmlspecialchars( $epl_heading ) . "</th></tr>\n";

	foreach ( $epl_hooks as $epl_hook => $epl_details ) {
		$epl_file_names = array_unique( array_map( 'basename', $epl_details['files'] ) );

		printf(
			'<tr id="%1$s"><td class="table-cell"><code><a href="%2$s">%3$s</a></code></td><td class="table-cell table-shrink">%4$s</td><td class="table-cell">%5$s</td></tr>' . "\n",
			htmlspecialchars( $epl_hook ),
			htmlspecialchars( $epl_finder->source_url( $epl_details['path'], $epl_details['line'] ) ),
			htmlspecialchars( $epl_hook ),
			htmlspecialchars( $epl_details['type'] ),
			htmlspecialchars( implode( ', ', $epl_file_names ) )
		);
	}

	echo "</table>\n";
}

$epl_content = ob_get_clean();

// Wrap the list in ApiGen's own page: everything outside the tree page's content area.
$epl_page = file_get_contents( $epl_tree_page );
$epl_page = preg_replace( '#(<div class="layout-content">).*?(\s*</div>\s*<div class="layout-footer">)#s', '$1' . "\n" . str_replace( '$', '\$', $epl_content ) . '$2', $epl_page, 1 );
$epl_page = preg_replace( '#<title>Tree#', '<title>Action and Filter Hook Reference', $epl_page, 1 );
$epl_page = str_replace( '<li class="active">', '<li>', $epl_page );
$epl_page = str_replace( '<li class="epl-hooks">', '<li class="epl-hooks active">', $epl_page );

file_put_contents( $epl_output_dir . '/hook-docs.html', $epl_page );

echo "hook-docs.html: $epl_total hooks.\n";
