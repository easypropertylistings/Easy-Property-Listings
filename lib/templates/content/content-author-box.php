<?php
/**
 * Author Box: Advanced Style
 *
 * @package     EPL
 * @subpackage  Templates/Content
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<!-- Author Box Container Tabbed -->
<div id="epl-box<?php echo esc_attr( $epl_author->author_id ); ?>" class="epl-author-box-container">
	<ul class="epl-author-tabs author-tabs">
		<?php

			$author_tabs = epl_author_tabs();
			$counter     = 1;
		foreach ( $author_tabs as $k  => &$author_tab ) {
			$current_class = 1 === $counter ? 'epl-author-current' : '';
			?>
				<?php
				ob_start();
				$output_html = apply_filters( 'epl_author_tab_' . $k . '_callback', call_user_func( 'epl_author_tab_' . str_replace( ' ', '_', $k ), $epl_author ), $epl_author );
				$author_tab  = array( 'label' => $author_tab );

				echo $output_html; // phpcs:ignore WordPress.Security.EscapeOutput

				$author_tab['content'] = ob_get_clean();
				// Remove tab if callback function output is ''.
				if ( trim( $author_tab['content'] ) === '' ) {
					unset( $author_tabs[ $k ] );
					continue;
				}

				?>

				<li class="tab-link <?php echo esc_attr( $current_class ); ?>" data-tab="tab-<?php echo esc_attr( $counter ); ?>"><?php echo esc_html( $author_tab['label'] ); ?></li>
			<?php
			$counter ++;
		}
		?>
	</ul>

	<div class="epl-author-box-outer-wrapper author-box-outer-wrapper epl-clearfix">
		<div class="epl-author-box epl-author-image author-box author-image">
			<?php
				do_action( 'epl_author_thumbnail', $epl_author );
			?>
		</div>

		<?php
			$counter = 1;
		foreach ( $author_tabs as $k => $tab ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride
			$current_tab   = strtolower( 'epl-author-' . $k );
			$current_class = 1 === $counter ? 'epl-author-current' : '';
			?>
				<div id="tab-<?php echo esc_attr( $counter ); ?>" class="<?php epl_author_class( $current_tab . ' epl-author-tab-content ' . $current_class ); ?>">
					<?php
					echo $tab['content']; // phpcs:ignore WordPress.Security.EscapeOutput
					?>
				</div>
				<?php
				$counter ++;
		}
		?>
	</div>
</div>

