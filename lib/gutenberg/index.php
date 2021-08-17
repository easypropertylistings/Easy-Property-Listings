<?php
/**
 * @package EPL
 */

//  Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// EPL Core Gutenberg Class.
require EPL_PATH_LIB . 'gutenberg/inc/class-epl-gutenberg-block.php';
require EPL_PATH_LIB . 'gutenberg/inc/enqueue-scripts.php';

// Dynamic Blocks.
require EPL_PATH_LIB . 'gutenberg/src/listing/index.php';
require EPL_PATH_LIB . 'gutenberg/src/listing-search/index.php';