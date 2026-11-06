<?php
/**
 * Normale Seite: Kopf + Inhalt aus dem Block-Editor + Aufruf-Band.
 */
get_header();
while ( have_posts() ) :
	the_post();
	get_template_part( 'template-parts/seitenkopf' );
	echo '<div class="st-content">';
	the_content();
	echo '</div>';
endwhile;
get_template_part( 'template-parts/cta' );
get_footer();
