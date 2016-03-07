<?php

    // admin only functions

    /**
     * Renders map after in address meta box
     */
    function epl_admin_listing_map($address) { ?>
        <div id='epl_admin_map_canvas' data-address="<?php echo $address; ?>"></div> <?php
    }

    add_action('epl_admin_listing_map','epl_admin_listing_map');