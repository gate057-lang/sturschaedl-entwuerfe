<?php
/**
 * Template Name: Medien (Video + Galerie)
 * Video und Fotos aus „Sturschädl → Medien & Band“.
 */
get_header();
the_post();
get_template_part( 'template-parts/seitenkopf' );
$video   = st_opt( 'video_url' );
$galerie = (array) st_opt( 'galerie' );
$embed   = '';
if ( $video ) {
	if ( preg_match( '~(?:youtu\.be/|youtube\.com/(?:watch\?v=|embed/|shorts/))([\w-]{11})~', $video, $m ) ) {
		$embed = 'https://www.youtube-nocookie.com/embed/' . $m[1] . '?autoplay=1&rel=0';
	} elseif ( preg_match( '~vimeo\.com/(\d+)~', $video, $m ) ) {
		$embed = 'https://player.vimeo.com/video/' . $m[1] . '?autoplay=1&dnt=1';
	}
}
?>
<?php if ( $embed ) : ?>
<section><div class="wrap">
	<button type="button" class="video" data-embed="<?php echo esc_url( $embed ); ?>" style="background-image:url('<?php echo esc_url( st_img( st_opt( 'video_bild' ), 'large' ) ); ?>')" aria-label="Video abspielen">
		<span class="play"></span><span class="cap"><?php echo esc_html( st_opt( 'video_titel' ) ); ?></span>
		<span class="hint">Mit Klick wird das Video vom Anbieter geladen. Dabei werden Daten übertragen.</span>
	</button>
</div></section>
<?php endif; ?>
<?php if ( trim( get_the_content() ) ) : ?><div class="st-content"><?php the_content(); ?></div><?php endif; ?>
<?php if ( $galerie ) : ?>
<section<?php echo $embed ? ' style="padding-top:0"' : ''; ?>><div class="wrap">
	<div class="gal">
		<?php
		$muster = array( 'w2 h2', '', 'h2', '', 'w2', '', '', '', 'w2', '' );
		foreach ( $galerie as $i => $b ) :
			$url = st_img( $b, 'large' );
			if ( ! $url ) {
				continue;
			}
			?>
			<a class="<?php echo esc_attr( $muster[ $i % count( $muster ) ] ); ?>" href="<?php echo esc_url( st_img( $b, 'full' ) ); ?>" data-lightbox><img src="<?php echo esc_url( $url ); ?>" alt="<?php echo esc_attr( st_alt( $b ) ); ?>" loading="lazy"></a>
		<?php endforeach; ?>
	</div>
</div></section>
<?php endif; ?>
<?php
get_template_part( 'template-parts/cta' );
get_footer();
