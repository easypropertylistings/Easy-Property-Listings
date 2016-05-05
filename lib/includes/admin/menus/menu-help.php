<?php
/**
 * Help Page
 *
 * @package     EPL
 * @subpackage  Admin/Menus
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div class="wrap">
	<div id="epl-menu-start-here" class="epl-menu-content-wrapper epl-menu-start-here">
		<div class="epl-menu-content">
			<div class="epl-menu-section epl-clearfix">
				<h1><?php _e('Welcome to Easy Property Listings', 'easy-property-listings' ); ?></h1>
				<p><?php _e('Nice! You installed Easy Property Listings and now you are ready to manage your listings.', 'easy-property-listings' ); ?>
				<p><?php echo $link = sprintf( __('Below are some links and videos that will help you get started. If you still have questions, we have on-line documentation and tutorials packed with information or ask a question by opening a <a href="%s">support ticket</a>.', 'easy-property-listings' ) , esc_url( 'https://easypropertylistings.com.au/support-ticket/' ) ); ?></p>
				<hr>


				<?php
				/**
				 * Author Level Quick Links visible to users who can edit_published_posts
				 *
				 * @since 2.0
				**/
				?>
				<div id="links" class="epl-section-content">
					<div class="epl-feature-section">
						<h2 class="epl-section-title"><?php _e( 'Important Links', 'easy-property-listings' ); ?></h2>
						<ul>
							<li><a href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'epl-about' ), 'index.php' ) ) ); ?>#guide-theme"><?php _e( 'What\'s New', 'easy-property-listings'  ); ?></a></li>
							<li><a href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'epl-getting-started' ), 'index.php' ) ) ); ?>"><?php _e( 'Getting Started', 'easy-property-listings'  ); ?></a></li>
							<li><a href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'epl-about' ), 'index.php' ) ) ); ?>#guide-changelog"><?php _e( 'Full Change Log', 'easy-property-listings'  ); ?></a></li>
							<li><a href="http://codex.easypropertylistings.com.au/"><?php _e('Documentation', 'easy-property-listings' ); ?></a></li>
							<li><a href="https://easypropertylistings.com.au/support-the-site/"><?php _e( 'Support the project', 'easy-property-listings'  ); ?></a></li>
							<li><a href="https://easypropertylistings.com.au/support-ticket/"><?php _e( 'Visit Support', 'easy-property-listings'  ); ?></a></li>
							<li><a href="https://wordpress.org/support/view/plugin-reviews/easy-property-listings"><strong><?php _e( 'Write a Review', 'easy-property-listings'  ); ?></strong></a></li>
						</ul>
					</div>
				</div>
				<hr>


				<?php
				/**
				 * Managing Users: Visible to Editor and Administrators only as the video goes into more detail about managing users.
				 *
				 * @since 2.1
				**/

				if ( current_user_can( 'edit_others_posts_not_ready' ) ) :
				?>
				<div id="add-listing" class="epl-section-content epl-section-admin-tutorial">
					<div class="epl-feature-section">
						<div class="epl-col">
							<strong><?php _e( 'Administrator tutorial', 'easy-property-listings' ); ?></strong>
							<h1 class="epl-section-title"><?php _e( 'Managing authors and their profiles', 'easy-property-listings' ); ?></h1>
							<p><?php _e( 'You can add new users and assign them to listings or they can add their own. The user can also updated and edit the information displayed in their profile and make changes from their profile page.', 'easy-property-listings' ); ?></p>
							<p><?php _e( 'Fast forward video to 5:00 minutes to see how you can quickly update your profile.', 'easy-property-listings' ); ?></p>
						</div>
						<div class="epl-col-last">
							<div class="featured-image">
								<?php $video_add_listing_id = 'FX1eXeWQAis'; ?>

								<div class="video-container">
									<iframe width="475" height="267" src="//www.youtube.com/embed/<?php echo $video_add_listing_id; ?>" frameborder="0" allowfullscreen ></iframe>
								</div>
							</div>
						</div>
					</div>

				<hr>
				</div>
				<?php endif;?>

				<div id="add-listing" class="epl-section-content epl-section-author-tutorial">
					<div class="epl-feature-section">
						<div class="epl-col">
							<h1 class="epl-section-title"><?php _e( 'Your profile and author box', 'easy-property-listings' ); ?></h1>
							<p><?php _e( 'Managing your profile is very important to help you connect with your visitors and gives you an opportunity to tell them about how you can help them.', 'easy-property-listings' ); ?></p>

							<p><?php _e( 'You can quickly edit your profile and update and add your phone number, social media links and bio or even add a video.', 'easy-property-listings' ); ?></p>

							<p><?php _e( 'Fast forward video to 5:00 minutes to see how you can quickly update your profile, update your contact details, edit your bio and add a YouTube video.', 'easy-property-listings' ); ?></p>
						</div>
						<div class="epl-col-last">
							<div class="featured-image">
								<?php $video_add_listing_id = 'FX1eXeWQAis'; ?>

								<div class="video-container">
									<iframe width="475" height="267" src="//www.youtube.com/embed/<?php echo $video_add_listing_id; ?>" frameborder="0" allowfullscreen ></iframe>
								</div>
							</div>
						</div>
					</div>

				<hr>
				</div>

				<div id="add-listing" class="epl-section-content">

					<div class="epl-feature-section">
						<div class="epl-col">
							<h1 class="epl-section-title"><?php _e( 'Adding Listings', 'easy-property-listings' ); ?></h1>
							<p><?php _e( 'The video will show you how to add a listing quickly and easily.', 'easy-property-listings' ); ?></p>
						</div>
						<div class="epl-col-last">
							<div class="featured-image">
								<?php $video_add_listing_id = 'h6B8LLecfbw'; ?>

								<div class="video-container">
									<iframe width="475" height="267" src="//www.youtube.com/embed/<?php echo $video_add_listing_id; ?>" frameborder="0" allowfullscreen ></iframe>
								</div>
							</div>
						</div>
					</div>

					<div class="epl-feature-section">
						<div class="epl-col">
							<h3><?php _e( 'Title', 'easy-property-listings'  );?></h3>
							<p><?php _e( 'When a property is being sold the "heading" is frequently changed and can cause link issues. Not to mention the search engine benefits. Use the full listing address as the title.', 'easy-property-listings'  );?></p>

							<h3><?php _e( 'Content Editor', 'easy-property-listings'  );?></h3>
							<p><?php _e( 'Enter your descriptive text for your listing here. Add images, insert shortcodes, or plain text.', 'easy-property-listings'  );?></p>

							<h3><?php _e( 'Author', 'easy-property-listings'  );?></h3>
							<p><?php _e( 'Set the primary listing agent as the author. Update your contact details, bio and social media links from your profile.', 'easy-property-listings'  );?></p>
						</div>
						<div class="epl-col epl-col-last">
							<div class="featured-image">
								<img src="<?php echo EPL_PLUGIN_URL . 'lib/assets/images/screenshots/epl-add-listing-title.png'; ?>" class="epl-help-screenshots"/>
							</div>
						</div>
					</div>

					<div class="epl-feature-section">
						<div class="epl-col">
							<h3><?php _e( 'Featured Image', 'easy-property-listings'  );?></h3>
							<p><?php _e( 'Set your featured property image.' , 'easy-property-listings'  ); ?></p>

							<h3><?php _e( 'Listing Image Gallery', 'easy-property-listings'  );?></h3>
							<p><?php _e( 'Add a gallery of images to your listings with the WordPress Add Media button.' , 'easy-property-listings'  ); ?></p>

							<p><?php _e( 'If set to automatic, just upload your images to the listing and press x to close the media upload box once the images are attached to the listing.', 'easy-property-listings'  );?></p>

						</div>

						<div class="epl-col epl-col-last">
							<img src="<?php echo EPL_PLUGIN_URL . 'lib/assets/images/screenshots/epl-add-listing-gallery.png'; ?>" class="epl-welcome-screenshots"/>
						</div>
					</div>

					<div class="epl-feature-section">
						<div class="epl-col epl-half-left">
							<h2><?php _e( 'Listing Details', 'easy-property-listings'  );?></h2>

							<h3><?php _e( 'Heading', 'easy-property-listings'  );?></h3>
							<p><?php _e( 'Enter the descriptive listing headline like "Great Property with Views".', 'easy-property-listings'  );?></p>

							<h3><?php _e( 'Second Listing Agent', 'easy-property-listings'  );?></h3>
							<p><?php _e( 'If the listing has two real estate agents marketing it, enter their WordPress user name here. The primary agent is the post Author.', 'easy-property-listings'  );?></p>

							<h3><?php _e( 'Inspection Times', 'easy-property-listings'  );?></h3>
							<p><?php _e( 'Add your inspection dates. ', 'easy-property-listings'  );?></p>

						</div>

						<div class="epl-col epl-half-right">
							<img src="<?php echo EPL_PLUGIN_URL . 'lib/assets/images/screenshots/epl-add-listing-details.png'; ?>" class="epl-welcome-screenshots"/>
						</div>
					</div>

					<div class="epl-feature-section">
						<div class="epl-col epl-half-left">
							<h2><?php _e( 'Search by location', 'easy-property-listings'  );?></h2>

							<p><?php _e( 'Although the address details are added into the Property Address box the location search you also need to add the City/Suburb to the location search taxonomy.', 'easy-property-listings'  );?></p>
							<p><?php _e( 'This works like post tags and will populate the search widget/shortcode with your listings and it will automatically filter out options if no listings have that option.', 'easy-property-listings'  );?></p>
						</div>

						<div class="epl-col epl-half-right">
							<img src="<?php echo EPL_PLUGIN_URL . 'lib/assets/images/screenshots/epl-add-listing-details-location.png'; ?>" class="epl-welcome-screenshots"/>
						</div>
					</div>

				<hr>
				</div>

			</div>
		</div>
	</div>
</div>

