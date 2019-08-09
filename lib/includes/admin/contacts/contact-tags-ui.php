<?php
/**
 * Contact Tags UI
 *
 * @package     EPL
 * @subpackage  Contacts
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class='wrap'>
	<div class="epl-contact-all-tags-wrap">
		<div class="epl-contact-all-tags-header">
			<h2 class="epl-contact-all-tags-title"><?php esc_html_e( 'Tags', 'easy-property-listings' ); ?></h2>
			<div class="epl-contact-all-tags-menu">
				<?php do_action( 'epl_contact_pre_all_tags_menu' ); ?>

				<?php do_action( 'epl_contact_post_all_tags_menu' ); ?>
			</div>
		</div>
		<div class="epl-contact-all-tags-container">
				<?php
				$contact_tags = get_terms( 'epl_contact_tag', array( 'hide_empty' => false ) );
				if ( ! empty( $contact_tags ) ) {
					if ( ! is_wp_error( $contact_tags ) ) {
						foreach ( $contact_tags as $term ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride
							$bgcolor = epl_get_contact_tag_bgcolor( $term->term_id );
							echo '<span><button class="epl-tag-btn" data-accent="' . esc_attr( $bgcolor ) . '" data-id="' . esc_attr( $term->term_id ) . '" id="contact-tag-' . esc_attr( $term->term_id ) . '" style="color:' . esc_attr( $bgcolor ) . '">' . esc_html( $term->name ) . '<span class="epl-term-count">' . esc_attr( $term->count ) . '</button></span>';
						}
					}
				}
				?>
		</div>

	</div>
	<?php if ( epl_get_errors() ) : ?>
	<div class="error settings-error">
		<?php epl_print_errors(); ?>
	</div>
<?php endif; ?>
