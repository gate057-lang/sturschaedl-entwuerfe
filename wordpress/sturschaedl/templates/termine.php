<?php
/**
 * Template Name: Termine (automatische Liste)
 * Kommende Termine und Rückblick aus „Termine“. Der Seiteninhalt erscheint darüber.
 */
get_header();
the_post();
get_template_part( 'template-parts/seitenkopf' );
$kommend = st_termine( 'kommend' );
$vorbei  = st_termine( 'vorbei', 30 );
$content = trim( get_the_content() );
if ( $content ) {
	echo '<div class="st-content">';
	the_content();
	echo '</div>';
}
?>
<section><div class="wrap">
	<div class="sec-head"><div><div class="kicker">Live</div><h2>Demnächst</h2></div><p>Euer Fest ist noch nicht dabei? Dann wird’s Zeit.</p></div>
	<div class="gigs">
		<?php
		foreach ( $kommend as $t ) {
			st_termin_zeile( $t );
		}
		st_frei_zeile( true );
		if ( ! $kommend && '1' !== (string) st_opt( 'frei_zeile' ) ) {
			echo '<p class="empty-note">Neue Termine folgen in Kürze.</p>';
		}
		?>
	</div>
</div></section>
<?php if ( $vorbei ) : ?>
<section style="padding-top:0"><div class="wrap">
	<div class="sec-head"><div><div class="kicker">Schee war’s</div><h2>Rückblick</h2></div></div>
	<div class="gigs"><?php foreach ( $vorbei as $t ) { st_termin_zeile( $t ); } ?></div>
</div></section>
<?php endif; ?>
<?php
get_template_part( 'template-parts/cta' );
get_footer();
