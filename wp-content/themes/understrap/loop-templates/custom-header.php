<?php
/**
 * Post rendering content according to caller of get_template_part.
 *
 * @package understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<article class="feature-img-and-title-container">
	<?php the_title( '<h1 class="page-title">', '</h1>' ); ?>
	
    <div class="feature-img-container">
        <?php if ( has_post_thumbnail() ) {
            the_post_thumbnail();
        } ?>
    </div>
</article>
