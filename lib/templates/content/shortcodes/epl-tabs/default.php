<?php
/**
 * EPL Tabs Shortcode Default View
 *
 * @package     EPL
 * @subpackage  Shortcodes/Templates
 * @copyright   Copyright (c) 2020, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.5.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} 
if( empty($tabs) )
    return;
?>
<div id="epl-tabs-shortcode-wrapper-<?php echo esc_attr( $attributes['id'] ); ?>" class="epl-tabs-shortcode-wrapper <?php echo esc_attr($attributes['wrap_class']); ?>">

    <?php if( !empty($attributes['title']) ) : ?>
        <h4><?php echo wp_kses_post($attributes['title']); ?></h4>
    <?php endif; ?>

    <div class="epl-tabs-shortcode epl-tabs-<?php echo esc_attr($attributes['type']).' '.esc_attr($attributes['class']); ?>">
        <ul>
            <?php
                foreach( $tabs as $tab ) { ?>
                    <li>
                        <a href="#epl-tab-<?php echo sanitize_title( $tab['id'] ); ?>">
                            <?php
                                echo wp_kses_post( $tab['title'] );
                            ?>
                        </a>
                    </li> <?php
                }
            ?>
        </ul>

        <?php echo $content; ?>
        
    </div>
</div>