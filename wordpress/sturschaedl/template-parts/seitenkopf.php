<?php
/**
 * Kopf der Unterseiten: Beitragsbild als Hintergrund, Kicker, Zierzeile, Titel, Untertitel (Textauszug).
 */
$id     = get_queried_object_id();
$bild   = get_the_post_thumbnail_url( $id, 'st-hero' );
$kicker = get_post_meta( $id, 'st_kicker', true );
$zier   = get_post_meta( $id, 'st_zier', true );
$unter  = has_excerpt( $id ) ? get_the_excerpt( $id ) : '';
?>
<header class="phead">
	<?php if ( $bild ) : ?><div class="bg" style="background-image:url('<?php echo esc_url( $bild ); ?>')"></div><?php endif; ?>
	<div class="wrap">
		<?php if ( $kicker ) : ?><div class="kicker"><?php echo esc_html( $kicker ); ?></div><?php endif; ?>
		<h1><?php if ( $zier ) : ?><span class="fr" style="color:var(--red);font-size:.6em;display:block"><?php echo esc_html( $zier ); ?></span><?php endif; ?><?php echo esc_html( get_the_title( $id ) ); ?></h1>
		<?php if ( $unter ) : ?><p><?php echo esc_html( $unter ); ?></p><?php endif; ?>
	</div>
</header>
