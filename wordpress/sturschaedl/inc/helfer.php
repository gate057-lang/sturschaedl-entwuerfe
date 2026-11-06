<?php
/**
 * Kleine Helfer für Optionen, Bilder und Seiten-Links.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Standardwerte aller Einstellungen (Inhalte von der bisherigen Website und Instagram). */
function st_defaults() {
	return array(
		'design'        => '1-bierzelt-nacht',
		'hero_bild2'    => 'ig-7.jpg',
		'hero_bild3'    => 'ig-2.jpg',
		'hero_bild'     => 'band-wirtshaus.jpg',
		'hero_kicker'   => 'Bayrisch · Rockig · Dorfmusi',
		'hero_zier'     => 'Mia san die',
		'hero_titel'    => 'Sturschädl',
		'hero_text'     => 'Eure Party- und Hochzeitsband aus der Oberpfalz. Fünf Mannsbuider, Lederhosn, Quetschn und E-Gitarre – vom Sektempfang bis zum letzten Maß.',
		'hero_btn1'     => 'Termin anfragen →',
		'hero_btn2'     => 'Live erleben',
		'naechst_leer'  => 'Saison 2027 – Termine folgen',
		'laufband'      => "Kirwa\nBierzelt\nHochzeit\nFeuerwehrfest\nFasching\nFirmenfeier\nGeburtstag",
		'fakten'        => array(
			array( 'zahl' => '2015', 'text' => 'auf da Bühne seit' ),
			array( 'zahl' => '5', 'text' => 'g’standne Mannsbuider' ),
			array( 'zahl' => '10+', 'text' => 'Jahre Gaudi im Land' ),
			array( 'zahl' => '~970', 'text' => 'Follower auf Instagram' ),
		),
		'angebote_kicker' => 'Was mia spuin',
		'angebote_titel'  => "Für jedes Fest\ndie richtige Gaudi",
		'angebote_text'   => 'Klassiker, aktuelle Charts und echte Party-Hymnen – bayrisch-rockig arrangiert und live gespielt.',
		'angebote'      => array(
			array( 'bild' => 'ig-7.jpg', 'titel' => 'Hochzeit', 'text' => 'Vom Sektempfang über die Brautentführung bis zum Finale auf da Tanzfläche.', 'link' => 'hochzeit', 'linktext' => 'Mehr erfahren →' ),
			array( 'bild' => 'ig-1.jpg', 'titel' => 'Kirwa & Bierzelt', 'text' => 'Kirwa, Feuerwehr- und Schützenfest: Wenn mia am Start san, bleibt keiner sitzen.', 'link' => 'termine', 'linktext' => 'Termine ansehen →' ),
			array( 'bild' => 'ig-2.jpg', 'titel' => 'Party & Firma', 'text' => 'Firmenevent, runder Geburtstag oder Faschingsball – mia passen uns eurem Abend an.', 'link' => 'kontakt', 'linktext' => 'Anfragen →' ),
		),
		'intro_bild'    => 'band-wald.jpg',
		'intro_kicker'  => 'Servus bei die Sturschädl',
		'intro_titel'   => "Ned bloß a Band –\na Versprechen.",
		'intro_gross'   => 'Wenn’s bei eich amoi so richtig scheppern soll, dann seid’s bei uns genau richtig.',
		'intro_text'    => 'Mia spuin mit Herzblut, Leidenschaft und am Schuss bayerischer Lässigkeit. Und weil mia uns ned verbiagn lassn, hoaß ma Sturschädl.',
		'start_termine' => 3,
		'frei_zeile'    => '1',
		'frei_titel'    => 'Euer Fest?',
		'frei_text'     => 'Saison 2027 wird grad geplant – Wunschtermine jetzt anfragen.',
		'insta_bilder'  => array( 'ig-1.jpg', 'ig-2.jpg', 'ig-8.jpg', 'ig-4.jpg', 'ig-6.jpg', 'ig-9.jpg' ),
		'cta_kicker'    => 'Euer Fest – unser Auftritt',
		'cta_titel'     => 'Lasst’s scheppern!',
		'cta_text'      => 'Kirwa, Feuerwehrfest, Hochzeit oder runder Geburtstag: Schreibt’s uns, wann und wo – mia meldn uns gscheid schnell zruck.',
		'cta_btn'       => 'Termin anfragen →',
		'galerie'       => array( 'ig-1.jpg', 'ig-2.jpg', 'ig-9.jpg', 'ig-4.jpg', 'band-wald.jpg', 'ig-6.jpg', 'ig-11.jpg', 'ig-7.jpg', 'band-wirtshaus.jpg', 'ig-10.jpg' ),
		'video_url'     => '',
		'video_bild'    => 'ig-8.jpg',
		'video_titel'   => 'Was ein Jahr 2025! · Saisonrückblick',
		'mitglieder_titel' => 'Fünf Sturschädl',
		'mitglieder_text'  => 'Die Mannsbuider hinter da Musi.',
		'mitglieder'    => array(
			array( 'name' => 'Bensch', 'rolle' => 'Gesang · Gitarre', 'bild' => '' ),
			array( 'name' => 'Name', 'rolle' => 'Bass', 'bild' => '' ),
			array( 'name' => 'Name', 'rolle' => 'Steirische / Akkordeon', 'bild' => '' ),
			array( 'name' => 'Name', 'rolle' => 'Akustikgitarre', 'bild' => '' ),
			array( 'name' => 'Name', 'rolle' => 'Schlagzeug · Cajón', 'bild' => '' ),
		),
		'kontakt_name'  => 'Bensch Seitz',
		'telefon'       => '+49 160 5555637',
		'email'         => 'sturschaedl@web.de',
		'anfrage_an'    => 'sturschaedl@web.de',
		'firma'         => 'Sturschädl GbR',
		'adresse'       => "Kugelgasse 7\n92364 Deining",
		'instagram'     => 'https://www.instagram.com/stur.schaedl_band/',
		'facebook'      => '',
		'fuss_text'     => 'Bayrisch-rockige Party- und Hochzeitsband aus Deining in der Oberpfalz. Seit 2015.',
		'anlaesse'      => "Hochzeit\nKirwa / Fest\nFirmenfeier\nGeburtstag\nSonstiges",
	);
}

/** Die fünf Designs (Entwürfe) – CSS in assets/designs/<slug>.css, Kopfbereich in template-parts/hero-<slug>.php */
function st_designs() {
	return array(
		'1-bierzelt-nacht'   => array( 'name' => 'Bierzelt-Nacht', 'cls' => '', 'info' => 'Dunkel, Rot, Bühnenlicht. Wie ein Konzertabend im Festzelt.' ),
		'2-wirtshaus'        => array( 'name' => 'Wirtshaus', 'cls' => 'light paper', 'info' => 'Hell und warm, Serifenschrift. Bodenständig und einladend.' ),
		'3-rock-n-raute'     => array( 'name' => 'Rock’n’Raute', 'cls' => '', 'info' => 'Schwarz mit bayerischer Raute. Die rockigste Variante.' ),
		'4-festplakat'       => array( 'name' => 'Festplakat', 'cls' => 'light', 'info' => 'Altes Veranstaltungsplakat: Packpapier, Stempel-Rot.' ),
		'5-hochzeit-elegant' => array( 'name' => 'Hochzeit Elegant', 'cls' => 'light', 'info' => 'Ruhig, edel, Grün und Gold. Spricht Brautpaare an.' ),
	);
}

/** Aktives Design. */
function st_design() {
	$d = st_opt( 'design' );
	return array_key_exists( $d, st_designs() ) ? $d : '1-bierzelt-nacht';
}

/** Einstellung lesen (mit Standardwert). */
function st_opt( $key ) {
	static $opts = null;
	if ( null === $opts ) {
		$opts = wp_parse_args( (array) get_option( 'sturschaedl', array() ), st_defaults() );
	}
	return isset( $opts[ $key ] ) ? $opts[ $key ] : '';
}

/**
 * Bild-URL ermitteln. Werte sind Mediathek-IDs (Zahl) oder – solange noch nichts
 * eingespielt ist – Dateinamen aus dem Theme-Ordner assets/img.
 */
function st_img( $val, $size = 'large' ) {
	if ( is_numeric( $val ) && (int) $val > 0 ) {
		$url = wp_get_attachment_image_url( (int) $val, $size );
		if ( $url ) {
			return $url;
		}
	}
	if ( is_string( $val ) && '' !== $val && preg_match( '/^[a-z0-9._-]+$/i', $val ) && file_exists( ST_DIR . '/assets/img/' . $val ) ) {
		return ST_URI . '/assets/img/' . $val;
	}
	return '';
}

/** Alternativtext eines Mediathek-Bildes. */
function st_alt( $val ) {
	if ( is_numeric( $val ) ) {
		return (string) get_post_meta( (int) $val, '_wp_attachment_image_alt', true );
	}
	return '';
}

/** Link zu einer Theme-Seite (termine, hochzeit, medien, ueber, kontakt) oder einer eingetragenen Adresse. */
function st_link( $key ) {
	if ( is_numeric( $key ) && (int) $key > 0 ) {
		$url = get_permalink( (int) $key );
		if ( $url ) {
			return $url;
		}
	}
	if ( preg_match( '#^(https?:)?//|^/|^\#|^mailto:|^tel:#', $key ) ) {
		return $key;
	}
	$seiten = (array) get_option( 'sturschaedl_seiten', array() );
	if ( ! empty( $seiten[ $key ] ) && get_post_status( $seiten[ $key ] ) === 'publish' ) {
		return get_permalink( $seiten[ $key ] );
	}
	$page = get_page_by_path( $key );
	return $page ? get_permalink( $page ) : home_url( '/' . $key . '/' );
}

/** Mehrzeiligen Text mit <br> ausgeben. */
function st_br( $text ) {
	return nl2br( esc_html( $text ), false );
}

/** Zeilen einer Textarea als Liste. */
function st_lines( $text ) {
	return array_values( array_filter( array_map( 'trim', preg_split( '/\r?\n|,/', (string) $text ) ) ) );
}

/** Telefonnummer für tel:-Link. */
function st_tel( $nr ) {
	return preg_replace( '/[^0-9+]/', '', (string) $nr );
}

/** Menü-Walker, der nur <a>-Links ausgibt (für die Fußzeile). */
class St_Link_Walker extends Walker_Nav_Menu {
	private $trenner;
	private $n = 0;
	public function __construct( $trenner = '' ) {
		$this->trenner = $trenner;
	}
	public function start_lvl( &$output, $depth = 0, $args = null ) {}
	public function end_lvl( &$output, $depth = 0, $args = null ) {}
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$output .= ( $this->n++ && $this->trenner ? esc_html( $this->trenner ) : '' ) . '<a href="' . esc_url( $item->url ) . '">' . esc_html( $item->title ) . '</a>';
	}
	public function end_el( &$output, $item, $depth = 0, $args = null ) {}
}

/** Menü, solange unter „Design → Menüs“ noch keins zugewiesen ist. */
function st_menu_fallback() {
	echo '<ul id="hauptmenue">';
	foreach ( array( 'termine' => 'Termine', 'hochzeit' => 'Hochzeiten', 'medien' => 'Medien', 'ueber-uns' => 'Über uns', 'kontakt' => 'Kontakt' ) as $k => $l ) {
		echo '<li><a href="' . esc_url( st_link( $k ) ) . '">' . esc_html( $l ) . '</a></li>';
	}
	echo '</ul>';
}
