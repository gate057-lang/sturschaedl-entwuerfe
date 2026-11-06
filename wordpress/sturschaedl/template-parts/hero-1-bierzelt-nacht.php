<?php
$naechster = st_termine( 'kommend', 1 );
?>
<header class="hero">
	<div class="bg" style="background-image:url('<?php echo esc_url( st_img( st_opt( 'hero_bild' ), 'st-hero' ) ); ?>')"></div>
	<a class="next-gig" href="<?php echo esc_url( st_link( 'termine' ) ); ?>">Nächster Auftritt
		<?php if ( $naechster ) : $t = $naechster[0]; $d = get_post_meta( $t->ID, 'st_datum', true ); ?>
			<strong><?php echo esc_html( get_the_title( $t ) ); ?></strong><?php echo esc_html( date_i18n( 'l, j. F Y', strtotime( $d ) ) ); ?>
		<?php else : ?>
			<strong><?php echo esc_html( st_opt( 'naechst_leer' ) ); ?></strong>Jetzt noch Wunschtermin sichern
		<?php endif; ?>
	</a>
	<div class="wrap"><div class="hero-grid">
		<div class="lockup">
			<img class="emb" src="<?php echo esc_url( ST_URI . '/assets/img/bulle-hell.png' ); ?>" alt="Stierschädel-Logo">
			<span class="rule"></span>
			<div>
				<div class="kicker"><?php echo esc_html( st_opt( 'hero_kicker' ) ); ?></div>
				<h1><span class="fr"><?php echo esc_html( st_opt( 'hero_zier' ) ); ?></span><?php echo esc_html( st_opt( 'hero_titel' ) ); ?></h1>
			</div>
		</div>
		<div>
			<p class="lead"><?php echo esc_html( st_opt( 'hero_text' ) ); ?></p>
			<div class="cta"><a class="btn red" href="<?php echo esc_url( st_link( 'kontakt' ) ); ?>"><?php echo esc_html( st_opt( 'hero_btn1' ) ); ?></a><a class="btn ghost" href="<?php echo esc_url( st_link( 'medien' ) ); ?>"><?php echo esc_html( st_opt( 'hero_btn2' ) ); ?></a></div>
		</div>
	</div></div>
</header>
