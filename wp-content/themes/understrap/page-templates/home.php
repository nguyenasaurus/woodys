<?php
/**
 * Template Name: Home
 *
 * Template for displaying a page without sidebar even if a sidebar widget is published.
 *
 * @package understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();
$container = get_theme_mod( 'understrap_container_type' );
?>

<div class="wrapper light" id="full-width-page-wrapper">

	<div class="<?php echo esc_attr( $container ); ?>" id="content">

		<div class="row">

			<div class="col-md-12 content-area" id="primary">

				<main class="site-main" id="main" role="main">
				<?php if( have_rows('cottages') ): 

					while( have_rows('cottages') ): the_row(); 
						
						// vars
						$cottage = get_sub_field('cottage_1');
						$link = get_sub_field('name');
						
						?>
						<div id="hero">
							<p><?php print_r($cottage); ?></p>
						</div>
					<?php endwhile; ?>

				<?php endif; ?>
				</main><!-- #main -->
				<article class="cta-text-container">
					<?php if( have_rows('text_block') ) :
						while( have_rows('text_block') ) : the_row(); 
						
						$title = get_sub_field('title');
						$subtitle = get_sub_field('subtitle');
						$text = get_sub_field('main_text');
						?>

						<h3><?php echo($title); ?></h3>
						<h4><?php echo($subtitle); ?></h4>
						<p><?php echo($text); ?></p>
					<?php endwhile; ?>
					<?php endif; ?>
				</article>

				<section class="lodgix-search-bar">
					<h3><?php the_field('cta_for_search'); ?></h3>
					<?php if ( is_front_page() ) : ?>
						<?php get_template_part( 'global-templates/hero' ); ?>
					<?php endif; ?>
				</section>
			</div><!-- #primary -->

		</div><!-- .row end -->

	</div><!-- #content -->

</div><!-- #full-width-page-wrapper -->

<?php get_footer(); ?>
