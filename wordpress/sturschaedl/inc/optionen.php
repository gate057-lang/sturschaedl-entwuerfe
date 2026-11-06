<?php
/**
 * Backend-Seite „Sturschädl“: alle Inhalte der Startseite und die Banddaten
 * zum Selberpflegen – ohne Code, mit Mediathek-Auswahl für Bilder.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Aufbau der Einstellungsseite: Reiter → Felder. */
function st_schema() {
	return array(
		'design'   => array(
			'titel'  => 'Design',
			'felder' => array(
				array( 'h', 'Welches Design soll die Website haben?' ),
				array( 'design', 'design', 'Design', 'Texte, Bilder und Termine bleiben beim Wechseln erhalten. Tipp: Design wählen, speichern, dann „Website ansehen“.' ),
				array( 'h', 'Zusatzbilder' ),
				array( 'hero_bild2', 'bild', 'Bogenbild links', 'Nur im Design „Hochzeit Elegant“ (drei Bögen oben).' ),
				array( 'hero_bild3', 'bild', 'Bogenbild rechts', 'Nur im Design „Hochzeit Elegant“.' ),
			),
		),
		'start'    => array(
			'titel'  => 'Startseite',
			'felder' => array(
				array( 'h', 'Großes Titelbild oben' ),
				array( 'hero_bild', 'bild', 'Titelfoto', 'Am besten ein Querformat-Bandfoto. Die Gesichter sollten in der oberen Bildhälfte sein, unten steht der Schriftzug.' ),
				array( 'hero_kicker', 'text', 'Kleine rote Zeile' ),
				array( 'hero_zier', 'text', 'Zierschrift über dem Namen' ),
				array( 'hero_titel', 'text', 'Großer Titel' ),
				array( 'hero_text', 'textarea', 'Einleitungstext' ),
				array( 'hero_btn1', 'text', 'Knopf 1 (führt zu Kontakt)' ),
				array( 'hero_btn2', 'text', 'Knopf 2 (führt zu Medien)' ),
				array( 'naechst_leer', 'text', 'Kasten „Nächster Auftritt“, wenn kein Termin ansteht', 'Steht ein Termin an, zeigt der Kasten automatisch den nächsten Termin.' ),
				array( 'h', 'Laufband (rotes Band)' ),
				array( 'laufband', 'textarea', 'Begriffe', 'Ein Begriff pro Zeile. Die Liste läuft endlos durch.' ),
				array( 'h', 'Zahlen & Fakten' ),
				array( 'fakten', 'liste', 'Fakten', '', array( 'zahl' => array( 'text', 'Zahl' ), 'text' => array( 'text', 'Beschreibung' ) ) ),
				array( 'h', 'Angebote (drei Bildkacheln)' ),
				array( 'angebote_kicker', 'text', 'Kleine rote Zeile' ),
				array( 'angebote_titel', 'textarea', 'Überschrift', 'Zeilenumbruch = neue Zeile in der Überschrift.' ),
				array( 'angebote_text', 'textarea', 'Text rechts daneben' ),
				array( 'angebote', 'liste', 'Kacheln', '', array( 'bild' => array( 'bild', 'Bild' ), 'titel' => array( 'text', 'Titel' ), 'text' => array( 'textarea', 'Text' ), 'link' => array( 'seite', 'Ziel' ), 'linktext' => array( 'text', 'Linktext' ) ) ),
				array( 'h', 'Vorstellung mit Bandfoto' ),
				array( 'intro_bild', 'bild', 'Foto' ),
				array( 'intro_kicker', 'text', 'Kleine rote Zeile' ),
				array( 'intro_titel', 'textarea', 'Überschrift' ),
				array( 'intro_gross', 'textarea', 'Großer Satz' ),
				array( 'intro_text', 'textarea', 'Text' ),
				array( 'h', 'Termine auf der Startseite' ),
				array( 'start_termine', 'zahl', 'Wie viele Termine zeigen?' ),
				array( 'h', 'Instagram-Kacheln' ),
				array( 'insta_bilder', 'bilder', 'Sechs Bilder', 'Klick auf ein Bild öffnet euer Instagram-Profil.' ),
			),
		),
		'termine'  => array(
			'titel'  => 'Termine & Aufruf',
			'felder' => array(
				array( 'h', 'Zeile „Euer Fest?“' ),
				array( 'frei_zeile', 'ja', 'Über den Terminen eine Einladung zum Anfragen zeigen' ),
				array( 'frei_titel', 'text', 'Titel' ),
				array( 'frei_text', 'text', 'Text' ),
				array( 'h', 'Aufruf-Band vor der Fußzeile' ),
				array( 'cta_kicker', 'text', 'Kleine rote Zeile' ),
				array( 'cta_titel', 'text', 'Überschrift' ),
				array( 'cta_text', 'textarea', 'Text' ),
				array( 'cta_btn', 'text', 'Knopf' ),
				array( 'p', 'Die Termine selbst pflegt ihr links im Menü unter <a href="edit.php?post_type=termin">Termine</a>.' ),
			),
		),
		'medien'   => array(
			'titel'  => 'Medien & Band',
			'felder' => array(
				array( 'h', 'Video auf der Seite „Medien“' ),
				array( 'video_url', 'text', 'YouTube- oder Vimeo-Adresse', 'Leer lassen, dann wird kein Video gezeigt. Das Video lädt erst nach Klick (Datenschutz).' ),
				array( 'video_bild', 'bild', 'Vorschaubild' ),
				array( 'video_titel', 'text', 'Bildunterschrift' ),
				array( 'h', 'Fotogalerie auf der Seite „Medien“' ),
				array( 'galerie', 'bilder', 'Fotos', 'Beliebig viele. Reihenfolge per Ziehen ändern.' ),
				array( 'h', 'Besetzung auf der Seite „Über uns“' ),
				array( 'mitglieder_titel', 'text', 'Überschrift' ),
				array( 'mitglieder_text', 'text', 'Text daneben' ),
				array( 'mitglieder', 'liste', 'Mitglieder', 'Ohne Foto erscheint der Stierschädel als Platzhalter.', array( 'bild' => array( 'bild', 'Foto' ), 'name' => array( 'text', 'Name' ), 'rolle' => array( 'text', 'Instrument / Rolle' ) ) ),
			),
		),
		'kontakt'  => array(
			'titel'  => 'Kontakt',
			'felder' => array(
				array( 'h', 'Kontaktdaten' ),
				array( 'kontakt_name', 'text', 'Ansprechpartner' ),
				array( 'telefon', 'text', 'Telefon' ),
				array( 'email', 'text', 'E-Mail (wird angezeigt)' ),
				array( 'anfrage_an', 'text', 'Anfragen gehen an', 'An diese Adresse schickt das Formular die Anfragen.' ),
				array( 'firma', 'text', 'Name / Firma' ),
				array( 'adresse', 'textarea', 'Anschrift' ),
				array( 'instagram', 'text', 'Instagram-Adresse' ),
				array( 'facebook', 'text', 'Facebook-Adresse' ),
				array( 'fuss_text', 'textarea', 'Kurztext in der Fußzeile' ),
				array( 'anlaesse', 'textarea', 'Anlässe im Formular', 'Ein Anlass pro Zeile.' ),
			),
		),
	);
}

add_action( 'admin_menu', function () {
	add_menu_page( 'Sturschädl', 'Sturschädl', 'edit_pages', 'sturschaedl', 'st_options_page', 'dashicons-format-audio', 3 );
} );

add_action( 'admin_init', function () {
	register_setting( 'sturschaedl', 'sturschaedl', array( 'sanitize_callback' => 'st_sanitize' ) );
} );

/* Redakteure (nicht nur Admins) dürfen speichern. */
add_filter( 'option_page_capability_sturschaedl', function () {
	return 'edit_pages';
} );

add_action( 'admin_enqueue_scripts', function ( $hook ) {
	if ( 'toplevel_page_sturschaedl' !== $hook ) {
		return;
	}
	wp_enqueue_media();
	wp_enqueue_script( 'jquery-ui-sortable' );
	wp_enqueue_script( 'st-admin', ST_URI . '/assets/admin.js', array( 'jquery', 'jquery-ui-sortable' ), ST_VERSION, true );
	wp_enqueue_style( 'st-admin', ST_URI . '/assets/admin.css', array(), ST_VERSION );
} );

function st_sanitize( $in ) {
	$alt = (array) get_option( 'sturschaedl', array() );
	$out = $alt;
	$in  = (array) $in;
	$schema = st_schema();
	// Nur die Felder des abgeschickten Reiters verarbeiten (sonst würden Haken anderer Reiter zurückgesetzt).
	$reiter = isset( $in['_reiter'] ) && isset( $schema[ $in['_reiter'] ] ) ? array( $schema[ $in['_reiter'] ] ) : $schema;
	foreach ( $reiter as $tab ) {
		foreach ( $tab['felder'] as $f ) {
			if ( in_array( $f[0], array( 'h', 'p' ), true ) ) {
				continue;
			}
			list( $key, $typ ) = $f;
			if ( ! array_key_exists( $key, $in ) && 'ja' !== $typ ) {
				continue;
			}
			$v = isset( $in[ $key ] ) ? $in[ $key ] : '';
			if ( 'liste' === $typ ) {
				$rows = array();
				foreach ( (array) $v as $row ) {
					$clean = array();
					foreach ( $f[4] as $sk => $sdef ) {
						$clean[ $sk ] = st_sanitize_wert( $sdef[0], isset( $row[ $sk ] ) ? $row[ $sk ] : '' );
					}
					if ( implode( '', $clean ) !== '' ) {
						$rows[] = $clean;
					}
				}
				$out[ $key ] = $rows;
			} else {
				$out[ $key ] = st_sanitize_wert( $typ, $v, isset( $f[4] ) ? $f[4] : null );
			}
		}
	}
	return $out;
}

function st_sanitize_wert( $typ, $v, $extra = null ) {
	switch ( $typ ) {
		case 'textarea':
			return sanitize_textarea_field( wp_unslash( $v ) );
		case 'bild':
			return is_numeric( $v ) ? absint( $v ) : sanitize_file_name( (string) $v );
		case 'bilder':
			$ids = array_filter( array_map( 'trim', explode( ',', (string) $v ) ) );
			return array_values( array_map( function ( $x ) {
				return is_numeric( $x ) ? absint( $x ) : sanitize_file_name( $x );
			}, $ids ) );
		case 'zahl':
			return max( 0, min( 20, absint( $v ) ) );
		case 'ja':
			return $v ? '1' : '0';
		case 'seite':
			return is_numeric( $v ) ? absint( $v ) : sanitize_key( $v );
		case 'design':
			return array_key_exists( $v, st_designs() ) ? $v : '1-bierzelt-nacht';
		case 'auswahl':
			return ( is_array( $extra ) && isset( $extra[ $v ] ) ) ? $v : '';
		default:
			return sanitize_text_field( wp_unslash( $v ) );
	}
}

function st_options_page() {
	$schema = st_schema();
	$tab    = isset( $_GET['tab'] ) && isset( $schema[ sanitize_key( $_GET['tab'] ) ] ) ? sanitize_key( $_GET['tab'] ) : 'design';
	?>
	<div class="wrap st-opt">
		<h1><img src="<?php echo esc_url( ST_URI . '/assets/img/bulle-dunkel.png' ); ?>" alt="" width="40"> Sturschädl – Inhalte pflegen
			<a class="page-title-action" href="<?php echo esc_url( ST_URI . '/anleitung.pdf' ); ?>" target="_blank" rel="noopener">Anleitung öffnen (PDF)</a></h1>
		<?php settings_errors(); ?>
		<nav class="nav-tab-wrapper">
			<?php foreach ( $schema as $k => $t ) : ?>
				<a class="nav-tab<?php echo $k === $tab ? ' nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=sturschaedl&tab=' . $k ) ); ?>"><?php echo esc_html( $t['titel'] ); ?></a>
			<?php endforeach; ?>
		</nav>
		<form method="post" action="options.php">
			<?php settings_fields( 'sturschaedl' ); ?>
			<input type="hidden" name="sturschaedl[_reiter]" value="<?php echo esc_attr( $tab ); ?>">
			<table class="form-table" role="presentation"><tbody>
			<?php
			foreach ( $schema[ $tab ]['felder'] as $f ) {
				if ( 'h' === $f[0] ) {
					echo '<tr class="st-h"><th colspan="2"><h2>' . esc_html( $f[1] ) . '</h2></th></tr>';
					continue;
				}
				if ( 'p' === $f[0] ) {
					echo '<tr><td colspan="2"><p>' . wp_kses_post( $f[1] ) . '</p></td></tr>';
					continue;
				}
				$key   = $f[0];
				$typ   = $f[1];
				$label = $f[2];
				$hilfe = isset( $f[3] ) ? $f[3] : '';
				echo '<tr><th scope="row"><label for="st-' . esc_attr( $key ) . '">' . esc_html( $label ) . '</label></th><td>';
				if ( 'liste' === $typ ) {
					st_feld_liste( $key, $f[4], st_opt( $key ) );
				} else {
					st_feld( $typ, 'sturschaedl[' . $key . ']', st_opt( $key ), 'st-' . $key, isset( $f[4] ) ? $f[4] : null );
				}
				if ( $hilfe ) {
					echo '<p class="description">' . esc_html( $hilfe ) . '</p>';
				}
				echo '</td></tr>';
			}
			?>
			</tbody></table>
			<?php submit_button( 'Änderungen speichern' ); ?>
		</form>
		<?php if ( 'design' === $tab && current_user_can( 'manage_options' ) ) : ?>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-top:8px">
				<input type="hidden" name="action" value="st_einrichten"><?php wp_nonce_field( 'st_einrichten' ); ?>
				<p><button class="button">Fehlende Inhalte einspielen</button> <span class="description">Legt fehlende Seiten, Menüs, Bilder und Beispieltermine neu an. Vorhandenes bleibt unverändert.</span></p>
			</form>
		<?php endif; ?>
		<p class="st-hinweis">Seiten wie „Hochzeiten“ oder „Über uns“ bearbeitet ihr unter <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=page' ) ); ?>">Seiten</a>, das Menü unter <a href="<?php echo esc_url( admin_url( 'nav-menus.php' ) ); ?>">Design → Menüs</a>.</p>
	</div>
	<?php
}

function st_feld( $typ, $name, $wert, $id, $extra = null ) {
	switch ( $typ ) {
		case 'textarea':
			printf( '<textarea class="large-text" rows="3" id="%s" name="%s">%s</textarea>', esc_attr( $id ), esc_attr( $name ), esc_textarea( $wert ) );
			break;
		case 'zahl':
			printf( '<input type="number" min="0" max="20" class="small-text" id="%s" name="%s" value="%s">', esc_attr( $id ), esc_attr( $name ), esc_attr( $wert ) );
			break;
		case 'ja':
			printf( '<label><input type="checkbox" id="%s" name="%s" value="1" %s> anzeigen</label>', esc_attr( $id ), esc_attr( $name ), checked( '1', (string) $wert, false ) );
			break;
		case 'auswahl':
			echo '<select id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '">';
			foreach ( (array) $extra as $k => $l ) {
				printf( '<option value="%s" %s>%s</option>', esc_attr( $k ), selected( $k, $wert, false ), esc_html( $l ) );
			}
			echo '</select>';
			break;
		case 'design':
			echo '<div class="st-designs">';
			foreach ( st_designs() as $k => $dz ) {
				printf(
					'<label class="st-design-karte"><input type="radio" name="%s" value="%s" %s><img src="%s" alt=""><b>%s</b><span>%s</span></label>',
					esc_attr( $name ), esc_attr( $k ), checked( $k, st_design(), false ),
					esc_url( ST_URI . '/assets/vorschau/' . $k . '.jpg' ), esc_html( $dz['name'] ), esc_html( $dz['info'] )
				);
			}
			echo '</div>';
			break;
		case 'seite':
			$seiten = (array) get_option( 'sturschaedl_seiten', array() );
			if ( ! is_numeric( $wert ) && isset( $seiten[ $wert ] ) ) {
				$wert = $seiten[ $wert ];
			}
			wp_dropdown_pages( array(
				'name'             => $name,
				'id'               => $id,
				'selected'         => (int) $wert,
				'show_option_none' => '– Seite wählen –',
				'option_none_value' => '',
			) );
			break;
		case 'bild':
			$url = st_img( $wert, 'medium' );
			?>
			<div class="st-bild" data-multi="0">
				<input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $wert ); ?>">
				<div class="st-vorschau"><?php echo $url ? '<img src="' . esc_url( $url ) . '" alt="">' : ''; ?></div>
				<button type="button" class="button st-waehlen">Bild wählen</button>
				<button type="button" class="button-link st-weg"<?php echo $url ? '' : ' hidden'; ?>>entfernen</button>
			</div>
			<?php
			break;
		case 'bilder':
			$liste = (array) $wert;
			?>
			<div class="st-bild" data-multi="1">
				<input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( implode( ',', $liste ) ); ?>">
				<ul class="st-galerie">
					<?php foreach ( $liste as $b ) : $u = st_img( $b, 'thumbnail' ); if ( ! $u ) { continue; } ?>
						<li data-id="<?php echo esc_attr( $b ); ?>"><img src="<?php echo esc_url( $u ); ?>" alt=""><button type="button" class="st-x" aria-label="Bild entfernen">×</button></li>
					<?php endforeach; ?>
				</ul>
				<button type="button" class="button st-waehlen">Bilder hinzufügen</button>
			</div>
			<?php
			break;
		default:
			printf( '<input type="text" class="regular-text" id="%s" name="%s" value="%s">', esc_attr( $id ), esc_attr( $name ), esc_attr( $wert ) );
	}
}

function st_feld_liste( $key, $spalten, $rows ) {
	$rows = array_values( (array) $rows );
	echo '<div class="st-liste" data-key="' . esc_attr( $key ) . '">';
	$render = function ( $i, $row ) use ( $key, $spalten ) {
		echo '<div class="st-zeile"><span class="st-griff dashicons dashicons-menu" title="Ziehen zum Sortieren"></span><div class="st-felder">';
		foreach ( $spalten as $sk => $sdef ) {
			echo '<div class="st-f st-f-' . esc_attr( $sdef[0] ) . '"><span class="st-l">' . esc_html( $sdef[1] ) . '</span>';
			st_feld( $sdef[0], 'sturschaedl[' . $key . '][' . $i . '][' . $sk . ']', isset( $row[ $sk ] ) ? $row[ $sk ] : '', 'st-' . $key . '-' . $i . '-' . $sk );
			echo '</div>';
		}
		echo '</div><button type="button" class="button-link st-zeile-weg">Zeile entfernen</button></div>';
	};
	echo '<div class="st-zeilen">';
	foreach ( $rows as $i => $row ) {
		$render( $i, $row );
	}
	echo '</div><script type="text/html" class="st-vorlage">';
	ob_start();
	$render( '__i__', array() );
	echo str_replace( '</script', '<\/script', ob_get_clean() ); // phpcs:ignore
	echo '</script><button type="button" class="button st-zeile-neu">+ Zeile hinzufügen</button></div>';
}
