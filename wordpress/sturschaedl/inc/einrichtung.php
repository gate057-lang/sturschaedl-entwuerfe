<?php
/**
 * Ersteinrichtung beim Aktivieren des Themes:
 * Bilder in die Mediathek, Seiten mit Inhalt, Startseite, Menüs, Beispieltermine, Einstellungen.
 * Bereits vorhandene Seiten/Termine werden NICHT überschrieben (Abgleich über die Titelform/Slug).
 * Erneut auslösbar unter „Sturschädl → Design → Fehlende Inhalte einspielen“.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'after_switch_theme', function () {
	if ( ! get_option( 'sturschaedl_eingerichtet' ) ) {
		st_einrichten();
	}
} );

add_action( 'admin_post_st_einrichten', function () {
	if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( 'st_einrichten' ) ) {
		wp_die( 'Keine Berechtigung.' );
	}
	st_einrichten();
	wp_safe_redirect( admin_url( 'admin.php?page=sturschaedl&tab=design&eingerichtet=1' ) );
	exit;
} );

add_action( 'admin_notices', function () {
	if ( isset( $_GET['eingerichtet'] ) ) {
		echo '<div class="notice notice-success is-dismissible"><p>Fehlende Seiten, Menüs, Bilder und Beispieltermine wurden eingespielt.</p></div>';
	}
} );

/** Ein Bild aus assets/img in die Mediathek übernehmen (einmalig). */
function st_bild_importieren( $datei, $alt = '' ) {
	$map = (array) get_option( 'sturschaedl_medien', array() );
	if ( ! empty( $map[ $datei ] ) && get_post( $map[ $datei ] ) ) {
		return (int) $map[ $datei ];
	}
	$pfad = ST_DIR . '/assets/img/' . $datei;
	if ( ! file_exists( $pfad ) ) {
		return 0;
	}
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$upload = wp_upload_bits( $datei, null, file_get_contents( $pfad ) ); // phpcs:ignore
	if ( ! empty( $upload['error'] ) ) {
		return 0;
	}
	$typ = wp_check_filetype( $upload['file'] );
	$id  = wp_insert_attachment( array(
		'post_mime_type' => $typ['type'],
		'post_title'     => 'Sturschädl – ' . preg_replace( '/\.\w+$/', '', $datei ),
		'post_status'    => 'inherit',
	), $upload['file'] );
	if ( is_wp_error( $id ) || ! $id ) {
		return 0;
	}
	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $upload['file'] ) );
	if ( $alt ) {
		update_post_meta( $id, '_wp_attachment_image_alt', $alt );
	}
	$map[ $datei ] = $id;
	update_option( 'sturschaedl_medien', $map, false );
	return (int) $id;
}

/* ---------- kleine Block-Bausteine für die Beispielinhalte ---------- */
function st_b_p( $t, $stil = '' ) {
	$a = $stil ? ' {"className":"is-style-' . $stil . '"}' : '';
	$c = $stil ? ' class="is-style-' . $stil . '"' : '';
	return "<!-- wp:paragraph$a -->\n<p$c>" . esc_html( $t ) . "</p>\n<!-- /wp:paragraph -->\n";
}
function st_b_h( $t, $level = 2 ) {
	$a = 2 === $level ? '' : ' {"level":' . $level . '}';
	return "<!-- wp:heading$a -->\n<h$level class=\"wp-block-heading\">" . str_replace( "\n", '<br>', esc_html( $t ) ) . "</h$level>\n<!-- /wp:heading -->\n";
}
function st_b_liste( $items, $stil = 'haken' ) {
	$li = '';
	foreach ( $items as $i ) {
		$li .= "<!-- wp:list-item -->\n<li>" . esc_html( $i ) . "</li>\n<!-- /wp:list-item -->\n";
	}
	return "<!-- wp:list {\"className\":\"is-style-$stil\"} -->\n<ul class=\"wp-block-list is-style-$stil\">$li</ul>\n<!-- /wp:list -->\n";
}
function st_b_gruppe( $inner, $klasse ) {
	return "<!-- wp:group {\"className\":\"$klasse\"} -->\n<div class=\"wp-block-group $klasse\">$inner</div>\n<!-- /wp:group -->\n";
}
function st_b_spalten( $links, $rechts ) {
	return "<!-- wp:columns {\"className\":\"st-split\"} -->\n<div class=\"wp-block-columns st-split\"><!-- wp:column -->\n<div class=\"wp-block-column\">$links</div>\n<!-- /wp:column -->\n\n<!-- wp:column -->\n<div class=\"wp-block-column\">$rechts</div>\n<!-- /wp:column --></div>\n<!-- /wp:columns -->\n";
}
function st_b_bild( $id ) {
	$url = wp_get_attachment_image_url( $id, 'large' );
	if ( ! $url ) {
		return '';
	}
	return "<!-- wp:image {\"id\":$id,\"sizeSlug\":\"large\",\"linkDestination\":\"none\",\"className\":\"is-style-rahmen\"} -->\n<figure class=\"wp-block-image size-large is-style-rahmen\"><img src=\"" . esc_url( $url ) . "\" alt=\"\" class=\"wp-image-$id\"/></figure>\n<!-- /wp:image -->\n";
}
function st_b_knopf( $t, $url ) {
	return "<!-- wp:buttons -->\n<div class=\"wp-block-buttons\"><!-- wp:button -->\n<div class=\"wp-block-button\"><a class=\"wp-block-button__link wp-element-button\" href=\"" . esc_url( $url ) . '">' . esc_html( $t ) . "</a></div>\n<!-- /wp:button --></div>\n<!-- /wp:buttons -->\n";
}

function st_einrichten() {
	/* 1. Bilder */
	$alts = array(
		'band-wirtshaus.jpg' => 'Die Band Sturschädl im Wirtshaus',
		'band-wald.jpg'      => 'Die Band Sturschädl im Wald',
		'band-3.jpg'         => 'Die Band Sturschädl',
	);
	$m = array();
	foreach ( glob( ST_DIR . '/assets/img/*.jpg' ) as $f ) {
		$n       = basename( $f );
		if ( 'beton.jpg' === $n ) {
			continue;
		}
		$m[ $n ] = st_bild_importieren( $n, isset( $alts[ $n ] ) ? $alts[ $n ] : '' );
	}
	$id = function ( $n ) use ( $m ) {
		return ! empty( $m[ $n ] ) ? $m[ $n ] : $n;
	};

	/* 2. Einstellungen (nur wenn noch keine gespeichert sind) */
	if ( false === get_option( 'sturschaedl' ) ) {
		$o = st_defaults();
		foreach ( array( 'hero_bild', 'hero_bild2', 'hero_bild3', 'intro_bild', 'video_bild' ) as $k ) {
			$o[ $k ] = $id( $o[ $k ] );
		}
		foreach ( array( 'insta_bilder', 'galerie' ) as $k ) {
			$o[ $k ] = array_map( $id, $o[ $k ] );
		}
		foreach ( $o['angebote'] as &$a ) {
			$a['bild'] = $id( $a['bild'] );
		}
		unset( $a );
		add_option( 'sturschaedl', $o );
	}

	/* 3. Seiten */
	$kontakt_url = home_url( '/kontakt/' );
	$seiten      = array(
		'startseite'  => array( 'Startseite', '', '', '', '', '' ),
		'termine'     => array( 'Termine', 'templates/termine.php', 'ig-3.jpg', 'Live-Auftritte', '', 'Alle öffentlichen Auftritte auf einen Blick. Private Feiern und Hochzeiten führen wir hier natürlich nicht auf.' ),
		'hochzeit'    => array( 'unsere Musi', '', 'ig-7.jpg', 'Hochzeiten', 'Euer Tag,', 'Mia wissen, wann’s romantisch sei muss – und wann die Lederhosn brennt.' ),
		'medien'      => array( 'Medien', 'templates/medien.php', 'ig-8.jpg', 'Fotos & Videos', '', 'Fotos und Clips von Kirwa, Bierzelt und Hochzeit.' ),
		'ueber-uns'   => array( 'Sturschädl', 'templates/ueber-uns.php', 'band-wald.jpg', 'Über uns', 'Servus bei die', '' ),
		'kontakt'     => array( 'Kontakt', 'templates/kontakt.php', 'ig-6.jpg', 'Anfrage', '', 'Ein paar Eckdaten genügen – dann wissen mia gleich, ob’s passt, und meldn uns mit am Angebot.' ),
		'impressum'   => array( 'Impressum', 'templates/rechtliches.php', '', '', '', '' ),
		'datenschutz' => array( 'Datenschutzerklärung', 'templates/rechtliches.php', '', '', '', '' ),
	);
	$inhalt = array(
		'hochzeit'    =>
			st_b_gruppe(
				st_b_p( 'So läuft’s', 'kicker' ) . st_b_h( "Vom Sekt bis\nzum Schluss" ) . st_b_p( 'Vom ersten Glas bis zum letzten Tanz: Mia begleiten euren Tag so, wie ihr’s euch wünscht.', 'gross' )
				. st_b_gruppe(
					st_b_gruppe( st_b_h( 'Sektempfang', 3 ) . st_b_p( 'Entspannte Akustik-Musi zum Ankommen und Anstoßen.' ), 'st-schritt' )
					. st_b_gruppe( st_b_h( 'Essen & Reden', 3 ) . st_b_p( 'Dezent im Hintergrund, Mikro für Trauzeugen und Reden inklusive.' ), 'st-schritt' )
					. st_b_gruppe( st_b_h( 'Brautentführung', 3 ) . st_b_p( 'Mia kenna de Bräuch – und spuin mit, wenn’s sei muss.' ), 'st-schritt' )
					. st_b_gruppe( st_b_h( 'Tanzfläche', 3 ) . st_b_p( 'Hochzeitstanz, Klassiker, Charts und Party-Hymnen bis in d’Früh.' ), 'st-schritt' ),
					'st-flow'
				),
				'is-style-abschnitt'
			)
			. st_b_gruppe(
				st_b_spalten(
					st_b_p( 'Was ihr bekommt', 'kicker' ) . st_b_h( "Alles aus\noaner Hand" )
					. st_b_liste( array( 'Live-Band mit 5 Musikern', 'Eigene Ton- und Lichttechnik', 'Vorgespräch & Musikwünsche', 'Bayrisch, Schlager, Rock & Charts', 'Mikro für Reden & Spiele', 'Flexibles Ende' ) )
					. st_b_knopf( 'Hochzeit anfragen →', $kontakt_url ),
					st_b_bild( (int) $id( 'ig-7.jpg' ) )
				),
				'is-style-abschnitt-dunkel'
			),
		'ueber-uns'   =>
			st_b_gruppe(
				st_b_spalten(
					st_b_p( 'Wer mia san', 'kicker' ) . st_b_h( "Seit 2015\nauf jeder Bühne" )
					. st_b_p( 'Fünf g’standne Mannsbuider, die seit 2015 ois unsicher machen, was a Bühne und an Stromanschluss hat.', 'gross' )
					. st_b_p( 'Seit über zehn Jahr ziang mia etz scho durchs Land, um de Leit a gscheide Gaudi zum bereiten.' )
					. st_b_p( 'Egal ob Bierzelt, Firmenevent oder runder Geburtstag – mia packen die Klassiker, die aktuellen Charts und die echten Party-Hymnen aus.' ),
					st_b_bild( (int) $id( 'band-wirtshaus.jpg' ) )
				),
				'is-style-abschnitt'
			)
			. st_b_gruppe(
				st_b_spalten(
					st_b_bild( (int) $id( 'ig-4.jpg' ) ),
					st_b_p( 'Warum Sturschädl?', 'kicker' ) . st_b_h( "Weil mia uns ned\nverbiagn lassn." )
					. st_b_p( 'Wenn mia am Start san, bleibt keiner sitzen und kein Maßkrug voi.', 'gross' )
					. st_b_p( 'Lust auf a gscheide Party? Dann schreibt’s uns oder ruft’s an. Mia gfrein uns sakrisch drauf, eure Feier unvergesslich zum machen!' )
				),
				'is-style-abschnitt'
			),
		'impressum'   => st_b_h( 'Angaben gemäß § 5 DDG' ) . st_b_p( 'Sturschädl GbR, Kugelgasse 7, 92364 Deining' ) . st_b_p( 'Vertreten durch: [bitte Gesellschafter eintragen]' ) . st_b_h( 'Kontakt' ) . st_b_p( 'Telefon: +49 160 5555637 · E-Mail: sturschaedl@web.de' ) . st_b_p( '[Bitte vollständiges Impressum prüfen und ergänzen.]' ),
		'datenschutz' => st_b_p( '[Hier die Datenschutzerklärung einfügen, z. B. aus einem Generator. Wichtig: Kontaktformular, Server-Logdateien, Video erst nach Klick (YouTube-nocookie), Schriften werden lokal geladen – keine Verbindung zu Google Fonts.]' ),
	);
	$ids = (array) get_option( 'sturschaedl_seiten', array() );
	foreach ( $seiten as $slug => $s ) {
		if ( ! empty( $ids[ $slug ] ) && get_post( $ids[ $slug ] ) ) {
			continue;
		}
		$vorhanden = get_page_by_path( $slug );
		if ( $vorhanden ) {
			$ids[ $slug ] = $vorhanden->ID;
			continue;
		}
		$pid = wp_insert_post( array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => $s[0],
			'post_name'    => $slug,
			'post_content' => isset( $inhalt[ $slug ] ) ? $inhalt[ $slug ] : '',
			'post_excerpt' => $s[5],
		) );
		if ( ! $pid || is_wp_error( $pid ) ) {
			continue;
		}
		$ids[ $slug ] = $pid;
		if ( $s[1] ) {
			update_post_meta( $pid, '_wp_page_template', $s[1] );
		}
		if ( $s[2] && is_numeric( $id( $s[2] ) ) ) {
			set_post_thumbnail( $pid, (int) $id( $s[2] ) );
		}
		update_post_meta( $pid, 'st_kicker', $s[3] );
		update_post_meta( $pid, 'st_zier', $s[4] );
	}
	update_option( 'sturschaedl_seiten', $ids );

	/* Links der Angebots-Kacheln auf echte Seiten-IDs umstellen */
	$o = get_option( 'sturschaedl' );
	if ( is_array( $o ) && ! empty( $o['angebote'] ) ) {
		foreach ( $o['angebote'] as &$a ) {
			if ( ! is_numeric( $a['link'] ) && ! empty( $ids[ $a['link'] ] ) ) {
				$a['link'] = $ids[ $a['link'] ];
			}
		}
		unset( $a );
		update_option( 'sturschaedl', $o );
	}

	/* 4. Startseite + Datenschutzseite + Permalinks */
	if ( 'page' !== get_option( 'show_on_front' ) && ! empty( $ids['startseite'] ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $ids['startseite'] );
	}
	if ( ! get_option( 'wp_page_for_privacy_policy' ) || 'publish' !== get_post_status( get_option( 'wp_page_for_privacy_policy' ) ) ) {
		update_option( 'wp_page_for_privacy_policy', $ids['datenschutz'] );
	}
	if ( ! get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
		flush_rewrite_rules();
	}
	if ( in_array( get_option( 'blogname' ), array( '', 'WordPress', 'Meine WordPress-Website', 'My WordPress Website', 'My WordPress Blog' ), true ) ) {
		update_option( 'blogname', 'Sturschädl' );
		update_option( 'blogdescription', 'Bayrisch-rockige Party- und Hochzeitsband aus der Oberpfalz' );
	}

	/* 5. Menüs */
	$menues = array(
		'haupt'  => array( 'Sturschädl Hauptmenü', array( 'termine' => 'Termine', 'hochzeit' => 'Hochzeiten', 'medien' => 'Medien', 'ueber-uns' => 'Über uns', 'kontakt' => 'Kontakt' ) ),
		'fuss'   => array( 'Sturschädl Fußzeile', array( 'termine' => 'Termine', 'hochzeit' => 'Hochzeiten', 'medien' => 'Medien', 'ueber-uns' => 'Über uns' ) ),
		'rechtl' => array( 'Sturschädl Rechtliches', array( 'impressum' => 'Impressum', 'datenschutz' => 'Datenschutz' ) ),
	);
	$orte = (array) get_theme_mod( 'nav_menu_locations', array() );
	foreach ( $menues as $ort => $def ) {
		if ( ! empty( $orte[ $ort ] ) && wp_get_nav_menu_object( $orte[ $ort ] ) ) {
			continue;
		}
		$menu = wp_get_nav_menu_object( $def[0] );
		$mid  = $menu ? $menu->term_id : wp_create_nav_menu( $def[0] );
		if ( is_wp_error( $mid ) ) {
			continue;
		}
		if ( ! $menu ) {
			foreach ( $def[1] as $slug => $titel ) {
				if ( empty( $ids[ $slug ] ) ) {
					continue;
				}
				wp_update_nav_menu_item( $mid, 0, array(
					'menu-item-title'     => $titel,
					'menu-item-object'    => 'page',
					'menu-item-object-id' => $ids[ $slug ],
					'menu-item-type'      => 'post_type',
					'menu-item-status'    => 'publish',
				) );
			}
		}
		$orte[ $ort ] = $mid;
	}
	set_theme_mod( 'nav_menu_locations', $orte );

	/* 6. Beispieltermine (Saison 2026 von der bisherigen Website) */
	if ( ! get_posts( array( 'post_type' => 'termin', 'post_status' => 'any', 'numberposts' => 1, 'fields' => 'ids' ) ) ) {
		$termine = array(
			array( 'Kirwa Stöckelsberg', '2026-10-26', '20:00', '92348 Berg bei Neumarkt i.d.OPf.', '' ),
			array( 'Kerwa Unterferrieden', '2026-08-21', '19:00', '90559 Burgthann', '' ),
			array( 'Schützenfest Altdorf', '2026-07-26', '18:00', '85135 Titting-Altdorf', '100 Jahre SG Altdorf' ),
			array( 'Kirwa Lauterhofen', '2026-07-18', '20:00', '92283 Lauterhofen', '' ),
			array( 'Pfingstfest Döllwang', '2026-05-24', '19:00', '92364 Deining-Döllwang', '' ),
			array( 'Feuerwehrfest Hardt', '2026-05-09', '20:00', '91809 Wellheim', '' ),
			array( 'Kirwa Bernla', '2026-04-25', '20:00', '92355 Velburg-Bernla', '' ),
		);
		foreach ( $termine as $t ) {
			$tid = wp_insert_post( array( 'post_type' => 'termin', 'post_status' => 'publish', 'post_title' => $t[0] ) );
			if ( $tid && ! is_wp_error( $tid ) ) {
				update_post_meta( $tid, 'st_datum', $t[1] );
				update_post_meta( $tid, 'st_uhrzeit', $t[2] );
				update_post_meta( $tid, 'st_ort', $t[3] );
				update_post_meta( $tid, 'st_hinweis', $t[4] );
			}
		}
	}

	update_option( 'sturschaedl_eingerichtet', ST_VERSION );
}
