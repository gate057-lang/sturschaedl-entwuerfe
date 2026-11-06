<?php
/**
 * Anfrageformular: Versand per wp_mail() an „Anfragen gehen an“.
 * Schutz: Nonce, verstecktes Lockfeld gegen Spam-Bots, Mindest-Ausfüllzeit, Datenschutz-Haken.
 * Hinweis: Damit Mails sicher ankommen, beim Hoster ein SMTP-Plugin (z. B. „WP Mail SMTP“) einrichten.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function st_anfrage_formular() {
	$status   = isset( $_GET['anfrage'] ) ? sanitize_key( $_GET['anfrage'] ) : '';
	$anlaesse = st_lines( st_opt( 'anlaesse' ) );
	$ds       = get_privacy_policy_url() ?: st_link( 'datenschutz' );
	?>
	<form class="f" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<?php if ( 'ok' === $status ) : ?>
			<div class="notice-box ok" role="status"><strong>Vergelt’s Gott!</strong> Eure Anfrage ist bei uns angekommen. Mia meldn uns gscheid schnell.</div>
		<?php elseif ( 'fehler' === $status ) : ?>
			<div class="notice-box" role="alert">Da hat was ned geklappt. Bitte Pflichtfelder prüfen oder direkt anrufen.</div>
		<?php endif; ?>
		<input type="hidden" name="action" value="st_anfrage">
		<input type="hidden" name="st_zeit" value="<?php echo esc_attr( time() ); ?>">
		<?php wp_nonce_field( 'st_anfrage', 'st_anfrage_nonce' ); ?>
		<label class="hp" aria-hidden="true">Bitte leer lassen<input type="text" name="st_website" tabindex="-1" autocomplete="off"></label>
		<?php if ( $anlaesse ) : ?>
		<div class="full"><span style="display:block;margin-bottom:7px;font:700 13px/1 var(--fh);letter-spacing:.14em;text-transform:uppercase;color:var(--mute)">Was feiert ihr?</span>
			<span class="chips">
				<?php foreach ( $anlaesse as $i => $a ) : ?>
					<label><input type="radio" name="st_anlass" value="<?php echo esc_attr( $a ); ?>" <?php checked( 0, $i ); ?>><?php echo esc_html( $a ); ?></label>
				<?php endforeach; ?>
			</span>
		</div>
		<?php endif; ?>
		<label>Datum<input type="date" name="st_datum"></label>
		<label>Ort<input type="text" name="st_ort" placeholder="z. B. Deining"></label>
		<label>Name *<input type="text" name="st_name" required autocomplete="name" placeholder="Vor- und Nachname"></label>
		<label>Gäste (ca.)<input type="text" name="st_gaeste" inputmode="numeric" placeholder="z. B. 150"></label>
		<label>E-Mail *<input type="email" name="st_email" required autocomplete="email" placeholder="name@beispiel.de"></label>
		<label>Telefon<input type="tel" name="st_telefon" autocomplete="tel" placeholder="für Rückfragen"></label>
		<label class="full">Nachricht<textarea name="st_nachricht" placeholder="Erzählt’s uns von eurem Fest …"></textarea></label>
		<label class="full consent"><input type="checkbox" name="st_ds" value="1" required><span>Ich bin einverstanden, dass meine Angaben zur Bearbeitung der Anfrage gespeichert und verwendet werden. Mehr in der <a href="<?php echo esc_url( $ds ); ?>">Datenschutzerklärung</a>. *</span></label>
		<div class="full"><button class="btn red" type="submit">Anfrage senden →</button></div>
	</form>
	<?php
}

function st_anfrage_senden() {
	$zurueck = wp_get_referer() ?: st_link( 'kontakt' );
	$zurueck = remove_query_arg( 'anfrage', $zurueck );
	$ok      = isset( $_POST['st_anfrage_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['st_anfrage_nonce'] ), 'st_anfrage' );

	// Spam-Bots: Lockfeld ausgefüllt oder zu schnell abgeschickt → still „ok“ melden, nichts senden.
	$bot = ! empty( $_POST['st_website'] ) || ( time() - (int) ( $_POST['st_zeit'] ?? 0 ) ) < 3;

	$f = array();
	foreach ( array( 'st_anlass', 'st_datum', 'st_ort', 'st_name', 'st_gaeste', 'st_telefon' ) as $k ) {
		$f[ $k ] = isset( $_POST[ $k ] ) ? sanitize_text_field( wp_unslash( $_POST[ $k ] ) ) : '';
	}
	$f['st_email']     = isset( $_POST['st_email'] ) ? sanitize_email( wp_unslash( $_POST['st_email'] ) ) : '';
	$f['st_nachricht'] = isset( $_POST['st_nachricht'] ) ? sanitize_textarea_field( wp_unslash( $_POST['st_nachricht'] ) ) : '';

	if ( ! $ok || '' === $f['st_name'] || ! is_email( $f['st_email'] ) || empty( $_POST['st_ds'] ) ) {
		wp_safe_redirect( add_query_arg( 'anfrage', 'fehler', $zurueck ) . '#inhalt' );
		exit;
	}
	if ( ! $bot ) {
		$datum = $f['st_datum'] ? date_i18n( 'd.m.Y', strtotime( $f['st_datum'] ) ) : '–';
		$text  = "Neue Anfrage über die Website\n\n"
			. 'Anlass:   ' . ( $f['st_anlass'] ?: '–' ) . "\n"
			. 'Datum:    ' . $datum . "\n"
			. 'Ort:      ' . ( $f['st_ort'] ?: '–' ) . "\n"
			. 'Gäste:    ' . ( $f['st_gaeste'] ?: '–' ) . "\n\n"
			. 'Name:     ' . $f['st_name'] . "\n"
			. 'E-Mail:   ' . $f['st_email'] . "\n"
			. 'Telefon:  ' . ( $f['st_telefon'] ?: '–' ) . "\n\n"
			. "Nachricht:\n" . ( $f['st_nachricht'] ?: '–' ) . "\n";
		$an = st_opt( 'anfrage_an' ) ?: get_option( 'admin_email' );
		wp_mail(
			$an,
			'Anfrage ' . ( $f['st_anlass'] ?: '' ) . ' am ' . $datum . ' – ' . $f['st_name'],
			$text,
			array( 'Reply-To: ' . $f['st_name'] . ' <' . $f['st_email'] . '>' )
		);
	}
	wp_safe_redirect( add_query_arg( 'anfrage', 'ok', $zurueck ) . '#inhalt' );
	exit;
}
add_action( 'admin_post_nopriv_st_anfrage', 'st_anfrage_senden' );
add_action( 'admin_post_st_anfrage', 'st_anfrage_senden' );
