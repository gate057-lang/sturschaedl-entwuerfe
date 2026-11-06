<?php
/**
 * Sturschädl – Theme-Funktionen.
 *
 * Aufbau:
 *  inc/optionen.php     Backend-Seite „Sturschädl“ (Startseite, Laufband, Fakten, Bilder, Kontakt …)
 *  inc/termine.php      Beitragstyp „Termine“ mit Datum, Uhrzeit, Ort
 *  inc/seiten-meta.php  Kopfbereich je Seite (Kicker, Zierzeile, Untertitel)
 *  inc/anfrage.php      Anfrageformular mit Versand per E-Mail
 *  inc/einrichtung.php  Ersteinrichtung beim Aktivieren (Seiten, Menü, Bilder, Beispieltermine)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ST_VERSION', '1.0.0' );
define( 'ST_DIR', get_template_directory() );
define( 'ST_URI', get_template_directory_uri() );

require ST_DIR . '/inc/helfer.php';
require ST_DIR . '/inc/optionen.php';
require ST_DIR . '/inc/termine.php';
require ST_DIR . '/inc/seiten-meta.php';
require ST_DIR . '/inc/anfrage.php';
require ST_DIR . '/inc/einrichtung.php';

add_action( 'after_setup_theme', function () {
	load_theme_textdomain( 'sturschaedl', ST_DIR . '/languages' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'editor-styles' );
	add_editor_style( array( 'assets/fonts.css', 'assets/editor.css' ) );
	add_post_type_support( 'page', 'excerpt' );
	register_nav_menus( array(
		'haupt'   => 'Hauptmenü (oben)',
		'fuss'    => 'Fußzeile – Band',
		'rechtl'  => 'Fußzeile – Impressum/Datenschutz',
	) );
	add_image_size( 'st-hero', 2400, 1600 );
} );

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'st-fonts', ST_URI . '/assets/fonts.css', array(), ST_VERSION );
	wp_enqueue_style( 'st-style', get_stylesheet_uri(), array( 'st-fonts' ), ST_VERSION );
	$d = st_design();
	if ( file_exists( ST_DIR . '/assets/designs/' . $d . '-fonts.css' ) ) {
		wp_enqueue_style( 'st-design-fonts', ST_URI . '/assets/designs/' . $d . '-fonts.css', array(), ST_VERSION );
	}
	wp_enqueue_style( 'st-design', ST_URI . '/assets/designs/' . $d . '.css', array( 'st-style' ), ST_VERSION );
	wp_enqueue_script( 'st-site', ST_URI . '/assets/site.js', array(), ST_VERSION, true );
} );

add_filter( 'body_class', function ( $classes ) {
	$d         = st_design();
	$designs   = st_designs();
	$classes[] = 'design-' . $d;
	foreach ( array_filter( explode( ' ', $designs[ $d ]['cls'] ) ) as $c ) {
		$classes[] = $c;
	}
	return $classes;
} );

/* Ein paar Blockstile, damit Redakteure die Theme-Optik im Editor anklicken können. */
add_action( 'init', function () {
	register_block_style( 'core/paragraph', array( 'name' => 'kicker', 'label' => 'Kicker (rot, klein)' ) );
	register_block_style( 'core/paragraph', array( 'name' => 'gross', 'label' => 'Großer Einleitungstext' ) );
	register_block_style( 'core/image', array( 'name' => 'rahmen', 'label' => 'Mit rotem Rahmen' ) );
	register_block_style( 'core/group', array( 'name' => 'abschnitt', 'label' => 'Abschnitt (volle Breite)' ) );
	register_block_style( 'core/group', array( 'name' => 'abschnitt-dunkel', 'label' => 'Abschnitt, abgesetzt' ) );
	register_block_style( 'core/list', array( 'name' => 'haken', 'label' => 'Haken-Liste, zweispaltig' ) );

	register_block_pattern_category( 'sturschaedl', array( 'label' => 'Sturschädl' ) );
} );
