<?php
/**
 * Custom functions that act independently of the theme templates.
 *
 * @package understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

function get_page_temaplate_base() {
        $theme = get_page_template_slug() ;
        $name = pathinfo($theme, PATHINFO_FILENAME);
        echo $name;
}
