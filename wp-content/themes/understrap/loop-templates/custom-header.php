<?php
/**
 * Post rendering content according to caller of get_template_part.
 *
 * @package understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>

<article class="feature-img-and-title-container <?php get_page_temaplate_base(); ?>">
	<?php the_title( '<h1 class="page-title title-heading">', '</h1>' ); ?>
	
    <div class="feature-img-container">
        <?php if ( has_post_thumbnail() ) {
            the_post_thumbnail();
        } ?>
    </div>
</article>
