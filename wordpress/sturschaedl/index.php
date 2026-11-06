<?php
/**
 * Rückfall-Vorlage (Beiträge, Archive, Suche).
 */
get_header();
?>
<header class="phead"><div class="wrap"><h1><?php echo esc_html( is_home() ? 'Neuigkeiten' : wp_strip_all_tags( get_the_archive_title() ) ); ?></h1></div></header>
<section><div class="wrap">
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
	<article style="margin-bottom:48px">
		<div class="kicker"><?php echo esc_html( get_the_date() ); ?></div>
		<h2 style="font-size:42px;margin:8px 0 12px"><a href="<?php the_permalink(); ?>" style="text-decoration:none"><?php the_title(); ?></a></h2>
		<div class="st-content"><?php is_singular() ? the_content() : the_excerpt(); ?></div>
	</article>
<?php endwhile; the_posts_pagination(); else : ?>
	<p>Hier gibt’s grad nix zum Anschauen.</p>
<?php endif; ?>
</div></section>
<?php
get_footer();
