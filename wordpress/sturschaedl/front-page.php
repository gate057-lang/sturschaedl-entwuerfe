<?php
/**
 * Startseite – alle Inhalte kommen aus „Sturschädl → Startseite“, die Termine aus „Termine“.
 * Der Kopfbereich wechselt je nach gewähltem Design (template-parts/hero-<design>.php).
 */
get_header();
$laufband = st_lines( st_opt( 'laufband' ) );
$fakten   = (array) st_opt( 'fakten' );
$angebote = (array) st_opt( 'angebote' );
$insta    = (array) st_opt( 'insta_bilder' );

get_template_part( 'template-parts/hero', st_design() );

if ( $laufband ) :
	$reihe = '';
	foreach ( $laufband as $w ) {
		$reihe .= esc_html( $w ) . ' <em>✕</em> ';
	}
	?>
	<div class="strip" aria-hidden="true"><span><?php echo $reihe . $reihe; // phpcs:ignore ?></span></div>
<?php endif; ?>

<?php if ( $fakten ) : ?>
<section><div class="wrap">
	<div class="facts" style="grid-template-columns:repeat(<?php echo (int) min( 4, count( $fakten ) ); ?>,1fr)">
		<?php foreach ( $fakten as $f ) : ?>
			<div><b><?php echo esc_html( $f['zahl'] ); ?></b><span><?php echo esc_html( $f['text'] ); ?></span></div>
		<?php endforeach; ?>
	</div>
</div></section>
<?php endif; ?>

<?php if ( $angebote ) : ?>
<section style="padding-top:0"><div class="wrap">
	<div class="sec-head"><div><div class="kicker"><?php echo esc_html( st_opt( 'angebote_kicker' ) ); ?></div><h2><?php echo st_br( st_opt( 'angebote_titel' ) ); // phpcs:ignore ?></h2></div><p><?php echo esc_html( st_opt( 'angebote_text' ) ); ?></p></div>
	<div class="offers">
		<?php foreach ( $angebote as $i => $a ) : ?>
			<a class="offer" href="<?php echo esc_url( st_link( $a['link'] ) ); ?>">
				<?php if ( st_img( $a['bild'] ) ) : ?><img src="<?php echo esc_url( st_img( $a['bild'] ) ); ?>" alt="<?php echo esc_attr( st_alt( $a['bild'] ) ); ?>" loading="lazy"><?php endif; ?>
				<span class="kicker"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
				<h3><?php echo esc_html( $a['titel'] ); ?></h3>
				<p><?php echo esc_html( $a['text'] ); ?></p>
				<span class="arrow"><?php echo esc_html( $a['linktext'] ); ?></span>
			</a>
		<?php endforeach; ?>
	</div>
</div></section>
<?php endif; ?>

<section style="background:var(--bg2)"><div class="wrap split">
	<div class="photo-frame"><img src="<?php echo esc_url( st_img( st_opt( 'intro_bild' ) ) ); ?>" alt="<?php echo esc_attr( st_alt( st_opt( 'intro_bild' ) ) ?: 'Die Band ' . get_bloginfo( 'name' ) ); ?>" loading="lazy"></div>
	<div class="txt">
		<div class="kicker"><?php echo esc_html( st_opt( 'intro_kicker' ) ); ?></div>
		<h2><?php echo st_br( st_opt( 'intro_titel' ) ); // phpcs:ignore ?></h2>
		<p class="big"><?php echo esc_html( st_opt( 'intro_gross' ) ); ?></p>
		<p><?php echo esc_html( st_opt( 'intro_text' ) ); ?></p>
		<a class="btn ghost" href="<?php echo esc_url( st_link( 'ueber-uns' ) ); ?>" style="margin-top:12px">Lernt uns kennen →</a>
	</div>
</div></section>

<?php
$anzahl = (int) st_opt( 'start_termine' );
if ( $anzahl > 0 ) :
	$kommend = st_termine( 'kommend', $anzahl );
	$rest    = $anzahl - count( $kommend );
	$vorbei  = $rest > 0 ? st_termine( 'vorbei', $rest ) : array();
	?>
<section><div class="wrap">
	<div class="sec-head"><div><div class="kicker">Live-Auftritte</div><h2>Do san mia</h2></div><a class="btn ghost" href="<?php echo esc_url( st_link( 'termine' ) ); ?>">Alle Termine →</a></div>
	<div class="gigs">
		<?php
		if ( ! $kommend ) {
			st_frei_zeile( false );
		}
		foreach ( $kommend as $t ) {
			st_termin_zeile( $t );
		}
		foreach ( $vorbei as $t ) {
			st_termin_zeile( $t, 'War a Gaudi' );
		}
		?>
	</div>
</div></section>
<?php endif; ?>

<?php if ( $insta ) : ?>
<section style="padding-top:0"><div class="wrap">
	<?php $handle = trim( (string) wp_parse_url( st_opt( 'instagram' ), PHP_URL_PATH ), '/' ); ?>
	<div class="sec-head"><div><div class="kicker"><?php echo esc_html( $handle ? '@' . $handle : 'Instagram' ); ?></div><h2>Frisch vom Fest</h2></div><?php if ( st_opt( 'instagram' ) ) : ?><a class="btn ghost" href="<?php echo esc_url( st_opt( 'instagram' ) ); ?>" target="_blank" rel="noopener">Auf Instagram folgen →</a><?php endif; ?></div>
	<div class="insta">
		<?php foreach ( array_slice( $insta, 0, 6 ) as $b ) : if ( ! st_img( $b, 'medium_large' ) ) { continue; } ?>
			<a href="<?php echo esc_url( st_opt( 'instagram' ) ?: '#' ); ?>" target="_blank" rel="noopener"><img src="<?php echo esc_url( st_img( $b, 'medium_large' ) ); ?>" alt="<?php echo esc_attr( st_alt( $b ) ); ?>" loading="lazy"></a>
		<?php endforeach; ?>
	</div>
</div></section>
<?php endif; ?>

<?php
get_template_part( 'template-parts/cta' );
get_footer();
