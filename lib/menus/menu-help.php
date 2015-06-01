<?php
/**
 * General Help Page options
 *
 * @since 2.0
 * @return void
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div class="wrap">
	<div id="epl-menu-start-here" class="epl-menu-content-wrapper epl-menu-start-here">
		<div class="epl-menu-content">
			<div class="epl-menu-section epl-clearfix">
				<h1><?php _e('Welcome to Easy Property Listings', 'epl'); ?></h1>
				<p><?php _e('Nice! You installed Easy Property Listings and now you are ready to manage your listings.', 'epl'); ?>
				<p><?php echo $link = sprintf( __('Below are some links and videos that will help you get started. If you still have questions, we have on-line tutorials packed with information, as well as a very active <a href="%s">support forum</a> where you can ask our team questions', 'epl') , esc_url( 'http://easypropertylistings.com.au/support/' ) ); ?></p>
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
						<h2 class="epl-section-title"><?php _e( 'Important Links', 'epl'); ?></h2>
						<ul>
							<li><a href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'epl-about' ), 'index.php' ) ) ); ?>#guide-theme"><?php _e( 'What\'s New', 'epl' ); ?></a></li>
							<li><a href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'epl-getting-started' ), 'index.php' ) ) ); ?>"><?php _e( 'Getting Started', 'epl' ); ?></a></li>
							<li><a href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'epl-about' ), 'index.php' ) ) ); ?>#guide-changelog"><?php _e( 'Full Change Log', 'epl' ); ?></a></li>
							<li><a href="http://easypropertylistings.com.au/documentation/"><?php _e('Documentation', 'epl'); ?></a></li>
							<li><a href="http://easypropertylistings.com.au/support-the-site/"><?php _e( 'Support the project', 'epl' ); ?></a></li>
							<li><a href="http://easypropertylistings.com.au/support/"><?php _e( 'Visit Support', 'epl' ); ?></a></li>
							<li><a href="https://wordpress.org/support/view/plugin-reviews/easy-property-listings"><strong><?php _e( 'Write a Review', 'epl' ); ?></strong></a></li>
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
							<strong><?php _e( 'Administrator tutorial', 'epl'); ?></strong>
							<h1 class="epl-section-title"><?php _e( 'Managing authors and their profiles', 'epl'); ?></h1>
							<p><?php _e( 'You can add new users and assign them to listings or they can add their own. The user can also updated and edit the information displayed in their profile and make changes from their profile page.', 'epl'); ?></p>
							<p><?php _e( 'Fast forward video to 5:00 minutes to see how you can quickly update your profile.', 'epl'); ?></p>
						</div>
						<div class="epl-col-last">
							<div class="featured-image">
								<?php echo wp_oembed_get('https://www.youtube.com/watch?v=FX1eXeWQAis', array('width'=>475)); ?>
							</div>
						</div>
					</div>
					
				<hr>
				</div>
				<?php endif;?>
				
				<div id="add-listing" class="epl-section-content epl-section-author-tutorial">
					<div class="epl-feature-section">
						<div class="epl-col">
							<h1 class="epl-section-title"><?php _e( 'Your profile and author box', 'epl'); ?></h1>
							<p><?php _e( 'Managing your profile is very important to help you connect with your visitors and gives you an opportunity to tell them about how you can help them.', 'epl'); ?></p>

							<p><?php _e( 'You can quickly edit your profile and update and add your phone number, social media links and bio or even add a video.', 'epl'); ?></p>
							
							<p><?php _e( 'Fast forward video to 5:00 minutes to see how you can quickly update your profile, update your contact details, edit your bio and add a YouTube video.', 'epl'); ?></p>
						</div>
						<div class="epl-col-last">
							<div class="featured-image">
								<?php echo wp_oembed_get('https://www.youtube.com/watch?v=FX1eXeWQAis', array('width'=>475)); ?>
							</div>
						</div>
					</div>
					
				<hr>
				</div>
				
				<div id="add-listing" class="epl-section-content">
				
					<div class="epl-feature-section">
						<div class="epl-col">
							<h1 class="epl-section-title"><?php _e( 'Adding Listings', 'epl'); ?></h1>
							<p><?php _e( 'The video will show you how to add a listing quickly and easily.', 'epl'); ?></p>
						</div>
						<div class="epl-col-last">
							<div class="featured-image">
								<?php echo wp_oembed_get('https://www.youtube.com/watch?v=h6B8LLecfbw', array('width'=>475)); ?>

							</div>
						</div>
					</div>
					
					<div class="epl-feature-section">
						<div class="epl-col">
							<h3><?php _e( 'Title', 'epl' );?></h3>
							<p><?php _e( 'When a property is being sold the "heading" is frequently changed and can cause link issues. Not to mention the search engine benefits. Use the full listing address as the title.', 'epl' );?></p>
							
							<h3><?php _e( 'Content Editor', 'epl' );?></h3>
							<p><?php _e( 'Enter your descriptive text for your listing here. Add images, insert shortcodes, or plain text.', 'epl' );?></p>

							<h3><?php _e( 'Author', 'epl' );?></h3>
							<p><?php _e( 'Set the primary listing agent as the author. Update your contact details, bio and social media links from your profile.', 'epl' );?></p>
						</div>
						<div class="epl-col epl-col-last">
							<div class="featured-image">
								<img src="<?php echo EPL_PLUGIN_URL . 'lib/assets/images/screenshots/epl-add-listing-title.png'; ?>" class="epl-help-screenshots"/>
							</div>
						</div>
					</div>
					
					<div class="epl-feature-section">
						<div class="epl-col">
							<h3><?php _e( 'Featured Image', 'epl' );?></h3>
							<p><?php _e( 'Set your featured property image.' , 'epl' ); ?></p>
							
							<h3><?php _e( 'Listing Image Gallery', 'epl' );?></h3>
							<p><?php _e( 'Add a gallery of images to your listings with the WordPress Add Media button.' , 'epl' ); ?></p>

							<p><?php _e( 'If set to automatic, just upload your images to the listing and press x to close the media upload box once the images are attached to the listing.', 'epl' );?></p>

						</div>
						
						<div class="epl-col epl-col-last">
							<img src="<?php echo EPL_PLUGIN_URL . 'lib/assets/images/screenshots/epl-add-listing-gallery.png'; ?>" class="epl-welcome-screenshots"/>
						</div>
					</div>
					
					<div class="epl-feature-section">
						<div class="epl-col epl-half-left">
							<h2><?php _e( 'Listing Details', 'epl' );?></h2>
							
							<h3><?php _e( 'Heading', 'epl' );?></h3>
							<p><?php _e( 'Enter the descriptive listing headline like "Great Property with Views".', 'epl' );?></p>
							
							<h3><?php _e( 'Second Listing Agent', 'epl' );?></h3>
							<p><?php _e( 'If the listing has two real estate agents marketing it, enter their WordPress user name here. The primary agent is the post Author.', 'epl' );?></p>
							
							<h3><?php _e( 'Inspection Times', 'epl' );?></h3>
							<p><?php _e( 'Add your inspection dates. ', 'epl' );?></p> 
							
						</div>
						
						<div class="epl-col epl-half-right">
							<img src="<?php echo EPL_PLUGIN_URL . 'lib/assets/images/screenshots/epl-add-listing-details.png'; ?>" class="epl-welcome-screenshots"/>
						</div>
					</div>
					
				<hr>
				</div>

			</div>
		</div>
	</div>
</div>

