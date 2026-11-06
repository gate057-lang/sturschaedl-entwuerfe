<?php
/**
 * Kopf: Navigation über dem Titelbild.
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="screen-reader-text" href="#inhalt">Zum Inhalt springen</a>
<div class="site">
<nav class="nav" aria-label="Hauptmenü"><div class="wrap">
	<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
		<img class="lg-h" src="<?php echo esc_url( ST_URI . '/assets/img/bulle-hell.png' ); ?>" alt="">
		<img class="lg-d" src="<?php echo esc_url( ST_URI . '/assets/img/bulle-dunkel.png' ); ?>" alt="">
		<span><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span>
	</a>
	<?php
	wp_nav_menu( array(
		'theme_location' => 'haupt',
		'container'      => false,
		'menu_id'        => 'hauptmenue',
		'depth'          => 1,
		'fallback_cb'    => 'st_menu_fallback',
	) );
	?>
	<a class="btn red" href="<?php echo esc_url( st_link( 'kontakt' ) ); ?>">Jetzt anfragen</a>
	<button class="burger" type="button" aria-controls="hauptmenue" aria-expanded="false" aria-label="Menü öffnen"><i></i><i></i><i></i></button>
</div></nav>
<main id="inhalt">
