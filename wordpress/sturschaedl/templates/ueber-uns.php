<?php
/**
 * Template Name: Über uns (mit Besetzung)
 * Seiteninhalt aus dem Block-Editor, danach die Mitglieder aus „Sturschädl → Medien & Band“.
 */
get_header();
the_post();
get_template_part( 'template-parts/seitenkopf' );
echo '<div class="st-content">';
the_content();
echo '</div>';
$mitglieder = (array) st_opt( 'mitglieder' );
if ( $mitglieder ) :
	?>
<section style="background:var(--bg2)"><div class="wrap">
	<div class="sec-head"><div><div class="kicker">Die Besetzung</div><h2><?php echo esc_html( st_opt( 'mitglieder_titel' ) ); ?></h2></div><p><?php echo esc_html( st_opt( 'mitglieder_text' ) ); ?></p></div>
	<div class="members">
		<?php foreach ( $mitglieder as $m ) : $u = st_img( $m['bild'], 'medium_large' ); ?>
			<div class="member"><div class="ph<?php echo $u ? ' has-img' : ''; ?>"<?php echo $u ? ' style="background-image:url(\'' . esc_url( $u ) . '\')"' : ''; ?>></div><div class="t"><b><?php echo esc_html( $m['name'] ); ?></b><span><?php echo esc_html( $m['rolle'] ); ?></span></div></div>
		<?php endforeach; ?>
	</div>
</div></section>
<?php endif; ?>
<?php
get_template_part( 'template-parts/cta' );
get_footer();
