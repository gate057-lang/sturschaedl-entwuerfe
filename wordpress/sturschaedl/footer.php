<?php
/**
 * Fußzeile mit Kontaktdaten aus „Sturschädl → Kontakt“.
 */
$insta = st_opt( 'instagram' );
$fb    = st_opt( 'facebook' );
?>
</main>
<footer><div class="wrap">
	<div class="foot">
		<div><div class="fr"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></div><p style="margin-top:12px;max-width:300px"><?php echo esc_html( st_opt( 'fuss_text' ) ); ?></p></div>
		<div><h4>Band</h4>
			<?php
			if ( has_nav_menu( 'fuss' ) ) {
				wp_nav_menu( array( 'theme_location' => 'fuss', 'container' => false, 'items_wrap' => '%3$s', 'depth' => 1, 'walker' => new St_Link_Walker() ) );
			} else {
				foreach ( array( 'termine' => 'Termine', 'hochzeit' => 'Hochzeiten', 'medien' => 'Medien', 'ueber-uns' => 'Über uns' ) as $k => $l ) {
					echo '<a href="' . esc_url( st_link( $k ) ) . '">' . esc_html( $l ) . '</a>';
				}
			}
			?>
		</div>
		<div><h4>Kontakt</h4>
			<?php if ( st_opt( 'kontakt_name' ) ) : ?><a><?php echo esc_html( st_opt( 'kontakt_name' ) ); ?></a><?php endif; ?>
			<?php if ( st_opt( 'telefon' ) ) : ?><a href="tel:<?php echo esc_attr( st_tel( st_opt( 'telefon' ) ) ); ?>"><?php echo esc_html( st_opt( 'telefon' ) ); ?></a><?php endif; ?>
			<?php if ( st_opt( 'email' ) ) : ?><a href="mailto:<?php echo esc_attr( antispambot( st_opt( 'email' ) ) ); ?>"><?php echo esc_html( antispambot( st_opt( 'email' ) ) ); ?></a><?php endif; ?>
		</div>
		<div><h4>Folgt uns</h4>
			<?php if ( $insta ) : ?><a href="<?php echo esc_url( $insta ); ?>" target="_blank" rel="noopener">Instagram</a><?php endif; ?>
			<?php if ( $fb ) : ?><a href="<?php echo esc_url( $fb ); ?>" target="_blank" rel="noopener">Facebook</a><?php endif; ?>
		</div>
	</div>
	<div class="legal">
		<span>© <?php echo esc_html( current_time( 'Y' ) . ' ' . st_opt( 'firma' ) . ' · ' . str_replace( "\n", ' · ', st_opt( 'adresse' ) ) ); ?></span>
		<span>
			<?php
			if ( has_nav_menu( 'rechtl' ) ) {
				wp_nav_menu( array( 'theme_location' => 'rechtl', 'container' => false, 'items_wrap' => '%3$s', 'depth' => 1, 'walker' => new St_Link_Walker( ' · ' ) ) );
			} else {
				$teile = array();
				foreach ( array( 'impressum' => 'Impressum', 'datenschutz' => 'Datenschutz' ) as $k => $l ) {
					$teile[] = '<a href="' . esc_url( st_link( $k ) ) . '">' . esc_html( $l ) . '</a>';
				}
				echo implode( ' · ', $teile ); // phpcs:ignore
			}
			?>
		</span>
	</div>
</div></footer>
</div>
<?php wp_footer(); ?>
</body>
</html>
