<?php
/**
 * Template Name: Schlichte Textseite (Impressum, Datenschutz)
 */
get_header();
the_post();
?>
<header class="phead" style="padding-bottom:40px"><div class="wrap"><h1><?php the_title(); ?></h1></div></header>
<div class="legal-page"><div class="wrap"><div class="st-content" style="max-width:820px"><?php the_content(); ?></div></div></div>
<?php
get_footer();
