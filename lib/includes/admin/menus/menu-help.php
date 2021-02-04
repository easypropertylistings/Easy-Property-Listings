<?php
/**
 * Help Page
 *
 * @package     EPL
 * @subpackage  Admin/MenusHelp
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="wrap">
	<div id="epl-menu-start-here" class="epl-menu-content-wrapper epl-menu-start-here">
		<div class="epl-menu-content">
			<div class="epl-menu-section epl-clearfix">
				<div id="links" class="epl-dashboard-widget epl-widget-primary-left       epl-section-content">

					<div class="epl-widget-item left primary            epl-feature-section">
						<h1><?php esc_html_e( 'Welcome to Easy Property Listings', 'easy-property-listings' ); ?></h1>
						<p><?php esc_html_e( 'Nice! You installed Easy Property Listings and now you are ready to manage your listings.', 'easy-property-listings' ); ?></p>
						<p>
							<?php
								/* Translators: %s is a link. */
								printf( wp_kses_post( __( 'Below are some links and videos that will help you get started. If you still have questions, we have on-line documentation and tutorials packed with information or ask a question by opening a <a href="%s">support ticket</a>.', 'easy-property-listings' ) ), esc_url( 'https://easypropertylistings.com.au/support-ticket/' ) );
							?>
						</p>
					</div>
					<div class="epl-widget-item right sidebar            epl-feature-section-column">
						<div class="epl-widget-strip">
							<span class="epl-accent">Links</span>
						</div>
					</div>



				</div>

				<?php
				/**
				 * Author Level Quick Links visible to users who can edit_published_posts
				 *
				 * @since 2.0
				 **/
				?>
				<div id="links" class="epl-dashboard-widget epl-widget-primary-right                 epl-section-content">

					<div class="epl-widget-item left sidebar            epl-feature-section-column">
						<div class="epl-widget-strip">
							<span class="epl-accent">Links</span>
						</div>
					</div>

					<div class="epl-widget-item  right primary                  epl-feature-section">
						<h2 class="epl-section-title"><?php esc_html_e( 'Important Links', 'easy-property-listings' ); ?></h2>
						<ul>
							<li><a href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'epl-about' ), 'index.php' ) ) ); ?>#guide-theme"><?php esc_html_e( 'What\'s New', 'easy-property-listings' ); ?></a></li>
							<li><a href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'epl-getting-started' ), 'index.php' ) ) ); ?>"><?php esc_html_e( 'Getting Started', 'easy-property-listings' ); ?></a></li>
							<li><a href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'epl-about' ), 'index.php' ) ) ); ?>#guide-changelog"><?php esc_html_e( 'Full Change Log', 'easy-property-listings' ); ?></a></li>
							<li><a href="http://codex.easypropertylistings.com.au/"><?php esc_html_e( 'Documentation', 'easy-property-listings' ); ?></a></li>
							<li><a href="https://easypropertylistings.com.au/support-the-site/"><?php esc_html_e( 'Support the project', 'easy-property-listings' ); ?></a></li>
							<li><a href="https://easypropertylistings.com.au/support-ticket/"><?php esc_html_e( 'Visit Support', 'easy-property-listings' ); ?></a></li>
							<li><a href="https://wordpress.org/support/view/plugin-reviews/easy-property-listings"><strong><?php esc_html_e( 'Write a Review', 'easy-property-listings' ); ?></strong></a></li>
						</ul>
					</div>


				</div>

				<?php
				/**
				 * Managing Users: Visible to Editor and Administrators only as the video goes into more detail about managing users.
				 *
				 * @since 2.1
				 **/


					?>
				<div id="admin" class="epl-dashboard-widget epl-widget-primary-left video">

					<div class="epl-widget-item left primary">
						<strong><?php esc_html_e( 'Administrator tutorial', 'easy-property-listings' ); ?></strong>
						<h1 class="epl-section-title"><?php esc_html_e( 'Managing authors and their profiles', 'easy-property-listings' ); ?></h1>
						<p><?php esc_html_e( 'You can add new users and assign them to listings or they can add their own. The user can also updated and edit the information displayed in their profile and make changes from their profile page.', 'easy-property-listings' ); ?></p>
						<p><?php esc_html_e( 'Fast forward video to 5:00 minutes to see how you can quickly update your profile.', 'easy-property-listings' ); ?></p>
					</div>
					<div class="epl-widget-item right sidebar">
						<div class="featured-image">
							<?php $video_add_listing_id = 'FX1eXeWQAis'; ?>

							<div class="video-container">
								<iframe width="475" height="267" src="//www.youtube.com/embed/<?php echo esc_attr( $video_add_listing_id ); ?>" frameborder="0" allowfullscreen ></iframe>
							</div>
						</div>
					</div>

				</div>


				<div id="profile" class="epl-dashboard-widget  epl-widget-primary-left video">
					<div class="epl-widget-item left primary">
						<h1 class="epl-section-title"><?php esc_html_e( 'Your profile and author box', 'easy-property-listings' ); ?></h1>
						<p><?php esc_html_e( 'Managing your profile is very important to help you connect with your visitors and gives you an opportunity to tell them about how you can help them.', 'easy-property-listings' ); ?></p>

						<p><?php esc_html_e( 'You can quickly edit your profile and update and add your phone number, social media links and bio or even add a video.', 'easy-property-listings' ); ?></p>

						<p><?php esc_html_e( 'Fast forward video to 5:00 minutes to see how you can quickly update your profile, update your contact details, edit your bio and add a YouTube video.', 'easy-property-listings' ); ?></p>
					</div>
					<div class="epl-widget-item right sidebar">
						<div class="featured-image">
							<?php $video_add_listing_id = 'FX1eXeWQAis'; ?>

							<div class="video-container">
								<iframe width="475" height="267" src="//www.youtube.com/embed/<?php echo esc_attr( $video_add_listing_id ); ?>" frameborder="0" allowfullscreen ></iframe>
							</div>
						</div>
					</div>
				</div>

				<div id="add-listing" class="epl-dashboard-widget  epl-widget-primary-left video">

						<div class="epl-widget-item left primary">
							<h1 class="epl-section-title"><?php esc_html_e( 'Adding Listings', 'easy-property-listings' ); ?></h1>
							<p><?php esc_html_e( 'The video will show you how to add a listing quickly and easily.', 'easy-property-listings' ); ?></p>
						</div>
						<div class="epl-widget-item right sidebar">
							<div class="featured-image">
								<?php $video_add_listing_id = 'h6B8LLecfbw'; ?>

								<div class="video-container">
									<iframe width="475" height="267" src="//www.youtube.com/embed/<?php echo esc_attr( $video_add_listing_id ); ?>" frameborder="0" allowfullscreen ></iframe>
								</div>
							</div>
						</div>

				</div>

				<div id="heading" class="epl-dashboard-widget  epl-widget-primary-left video">
					<div class="epl-widget-item left primary">
						<h3><?php esc_html_e( 'Listing Heading', 'easy-property-listings' ); ?></h3>
						<p><?php esc_html_e( 'When a property is being sold the "heading" is frequently changed and can cause link issues. Not to mention the search engine benefits. Use the full listing address as the title.', 'easy-property-listings' ); ?></p>

						<h3><?php esc_html_e( 'Content Editor', 'easy-property-listings' ); ?></h3>
						<p><?php esc_html_e( 'Enter your descriptive text for your listing here. Add images, insert shortcodes, or plain text.', 'easy-property-listings' ); ?></p>

						<h3><?php esc_html_e( 'Author', 'easy-property-listings' ); ?></h3>
						<p><?php esc_html_e( 'Set the primary listing agent as the author. Update your contact details, bio and social media links from your profile.', 'easy-property-listings' ); ?></p>
					</div>
					<div class="epl-widget-item right sidebar">
						<div class="featured-image">
							<img src="<?php echo esc_url( EPL_PLUGIN_URL . 'lib/assets/images/screenshots/epl-add-listing-title.png' ); ?>" class="epl-help-screenshots"/>
						</div>
					</div>
				</div>




				<div id="featured-image" class="epl-dashboard-widget  epl-widget-primary-left video">
					<div class="epl-widget-item left primary">
						<h3><?php esc_html_e( 'Featured Image', 'easy-property-listings' ); ?></h3>
						<p><?php esc_html_e( 'Set your featured property image.', 'easy-property-listings' ); ?></p>

						<h3><?php esc_html_e( 'Listing Image Gallery', 'easy-property-listings' ); ?></h3>
						<p><?php esc_html_e( 'Add a gallery of images to your listings with the WordPress Add Media button.', 'easy-property-listings' ); ?></p>

						<p><?php esc_html_e( 'If set to automatic, just upload your images to the listing and press x to close the media upload box once the images are attached to the listing.', 'easy-property-listings' ); ?></p>

					</div>

					<div class="epl-widget-item right sidebar">
						<img src="<?php echo esc_url( EPL_PLUGIN_URL . 'lib/assets/images/screenshots/epl-add-listing-gallery.png' ); ?>" class="epl-welcome-screenshots"/>
					</div>
				</div>




				<div id="listing-details" class="epl-dashboard-widget  epl-widget-primary-left video">
					<div class="epl-widget-item left primary">
						<h2><?php esc_html_e( 'Listing Details', 'easy-property-listings' ); ?></h2>

						<h3><?php esc_html_e( 'Heading', 'easy-property-listings' ); ?></h3>
						<p><?php esc_html_e( 'Enter the descriptive listing headline like "Great Property with Views".', 'easy-property-listings' ); ?></p>

						<h3><?php esc_html_e( 'Second Listing Agent', 'easy-property-listings' ); ?></h3>
						<p><?php esc_html_e( 'If the listing has two real estate agents marketing it, enter their WordPress user name here. The primary agent is the post Author.', 'easy-property-listings' ); ?></p>

						<h3><?php esc_html_e( 'Inspection Times', 'easy-property-listings' ); ?></h3>
						<p><?php esc_html_e( 'Add your inspection dates. ', 'easy-property-listings' ); ?></p>

					</div>

					<div class="epl-widget-item right sidebar">
						<img src="<?php echo esc_url( EPL_PLUGIN_URL . 'lib/assets/images/screenshots/epl-add-listing-details.png' ); ?>" class="epl-welcome-screenshots"/>
					</div>
				</div>

				<div id="search-location" class="epl-dashboard-widget  epl-widget-primary-left video">
					<div class="epl-widget-item left primary">
						<h2><?php esc_html_e( 'Search by location', 'easy-property-listings' ); ?></h2>

						<p><?php esc_html_e( 'Although the address details are added into the Property Address box the location search you also need to add the City/Suburb to the location search taxonomy.', 'easy-property-listings' ); ?></p>
						<p><?php esc_html_e( 'This works like post tags and will populate the search widget/shortcode with your listings and it will automatically filter out options if no listings have that option.', 'easy-property-listings' ); ?></p>
					</div>

					<div class="epl-widget-item right sidebar">
						<img src="<?php echo esc_url( EPL_PLUGIN_URL . 'lib/assets/images/screenshots/epl-add-listing-details-location.png' ); ?>" class="epl-welcome-screenshots"/>
					</div>
				</div>


			</div>
		</div>
	</div>
</div>

