<?php
/*
 * Author Box: Advanced Style
 *
 * @package EPL
 * @subpackage Theme
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<!-- Author Box Container Tabbed -->
<div id="epl-box<?php echo $epl_author->author_id; ?>" class="epl-author-box">		
	<ul class="author-tabs">
		<?php
			
			$author_tabs = epl_author_tabs();
			$counter = 1;
			foreach($author_tabs as $author_tab){
				$current_class = $counter == 1? 'author-current':''; ?>
				<?php 
					ob_start();
					apply_filters('epl_author_tab_'.$author_tab.'_callback',call_user_func('epl_author_tab_'.str_replace(' ','_',$author_tab)));
					$op = ob_get_clean();
					// remove tab if callback function output is ''
					if($op == '')  {
						continue;
					}
					
				?>

				<li class="tab-link <?php echo $current_class; ?>" data-tab="tab-<?php echo $counter;?>"><?php _e($author_tab, 'epl'); ?></li><?php
				$counter ++;
			}
		?>
	</ul>

	<div class="author-box-outer-wrapper epl-clearfix">			
		<div class="author-box author-image">
			<?php
				echo apply_filters('epl_author_tab_image',epl_author_tab_image());
			?>
		</div>
		
		<?php
			$counter = 1;
			foreach($author_tabs as $author_tab){
				$current_tab 	= strtolower('author-'.$author_tab);
				$current_class	= $counter == 1? 'author-current':''; ?>
				<div id="tab-<?php echo $counter; ?>" class="<?php epl_author_class ($current_tab .' author-tab-content '.$current_class) ?>">
					<?php apply_filters('epl_author_tab_'.$author_tab.'_callback',call_user_func('epl_author_tab_'.str_replace(' ','_',$author_tab)))  ?>
				</div>
				<?php
				$counter ++;
			}
		?>
	</div>
</div>

