<?php
/**
 * Template Name: Kontakt (Anfrageformular)
 * Formular + Kontaktkästen. Daten aus „Sturschädl → Kontakt“.
 */
get_header();
the_post();
get_template_part( 'template-parts/seitenkopf' );
?>
<section><div class="wrap contact">
	<?php st_anfrage_formular(); ?>
	<aside class="side">
		<div class="box"><h3>Direkt anrufen</h3><p style="color:var(--mute)"><?php echo esc_html( st_opt( 'kontakt_name' ) ); ?></p>
			<?php if ( st_opt( 'telefon' ) ) : ?><a class="big" href="tel:<?php echo esc_attr( st_tel( st_opt( 'telefon' ) ) ); ?>"><?php echo esc_html( st_opt( 'telefon' ) ); ?></a><?php endif; ?>
			<?php if ( st_opt( 'email' ) ) : ?><a class="big" style="font-size:22px" href="mailto:<?php echo esc_attr( antispambot( st_opt( 'email' ) ) ); ?>"><?php echo esc_html( antispambot( st_opt( 'email' ) ) ); ?></a><?php endif; ?>
		</div>
		<div class="box"><h3><?php echo esc_html( st_opt( 'firma' ) ); ?></h3><p style="color:var(--mute)"><?php echo st_br( st_opt( 'adresse' ) ); // phpcs:ignore ?></p></div>
		<?php if ( st_opt( 'instagram' ) || st_opt( 'facebook' ) ) : ?>
		<div class="box"><h3>Folgt uns</h3><p style="color:var(--mute)">
			<?php if ( st_opt( 'instagram' ) ) : ?><a href="<?php echo esc_url( st_opt( 'instagram' ) ); ?>" target="_blank" rel="noopener">Instagram</a><br><?php endif; ?>
			<?php if ( st_opt( 'facebook' ) ) : ?><a href="<?php echo esc_url( st_opt( 'facebook' ) ); ?>" target="_blank" rel="noopener">Facebook</a><?php endif; ?>
		</p></div>
		<?php endif; ?>
		<?php if ( trim( get_the_content() ) ) : ?><div class="box st-content"><?php the_content(); ?></div><?php endif; ?>
	</aside>
</div></section>
<?php
get_footer();
