<?php
/**
 * Beitragstyp „Termine“: Titel = Name des Fests, dazu Datum, Uhrzeit, Ort und Hinweis.
 * Kommende und vergangene Termine sortiert das Theme automatisch.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', function () {
	register_post_type( 'termin', array(
		'labels'        => array(
			'name'               => 'Termine',
			'singular_name'      => 'Termin',
			'add_new'            => 'Neuer Termin',
			'add_new_item'       => 'Neuen Termin anlegen',
			'edit_item'          => 'Termin bearbeiten',
			'new_item'           => 'Neuer Termin',
			'view_item'          => 'Termin ansehen',
			'search_items'       => 'Termine durchsuchen',
			'not_found'          => 'Keine Termine gefunden',
			'not_found_in_trash' => 'Keine Termine im Papierkorb',
			'all_items'          => 'Alle Termine',
			'menu_name'          => 'Termine',
		),
		'public'        => false,
		'show_ui'       => true,
		'show_in_menu'  => true,
		'show_in_rest'  => false,
		'menu_position' => 4,
		'menu_icon'     => 'dashicons-calendar-alt',
		'supports'      => array( 'title' ),
	) );
} );

/* Titelfeld-Platzhalter */
add_filter( 'enter_title_here', function ( $text, $post ) {
	return 'termin' === $post->post_type ? 'Name des Fests, z. B. Kirwa Stöckelsberg' : $text;
}, 10, 2 );

add_action( 'add_meta_boxes_termin', function () {
	add_meta_box( 'st_termin', 'Termin-Daten', 'st_termin_box', 'termin', 'normal', 'high' );
} );

function st_termin_box( $post ) {
	wp_nonce_field( 'st_termin', 'st_termin_nonce' );
	$d = get_post_meta( $post->ID, 'st_datum', true );
	$u = get_post_meta( $post->ID, 'st_uhrzeit', true );
	$o = get_post_meta( $post->ID, 'st_ort', true );
	$h = get_post_meta( $post->ID, 'st_hinweis', true );
	$l = get_post_meta( $post->ID, 'st_link', true );
	?>
	<style>.st-tb{display:grid;grid-template-columns:160px 1fr;gap:12px 16px;align-items:center;max-width:720px}.st-tb input{width:100%}.st-tb small{grid-column:2;color:#646970;margin-top:-6px}</style>
	<div class="st-tb">
		<label for="st_datum"><strong>Datum *</strong></label>
		<input type="date" id="st_datum" name="st_datum" value="<?php echo esc_attr( $d ); ?>" required>
		<label for="st_uhrzeit">Beginn</label>
		<input type="time" id="st_uhrzeit" name="st_uhrzeit" value="<?php echo esc_attr( $u ); ?>">
		<label for="st_ort">Ort</label>
		<input type="text" id="st_ort" name="st_ort" value="<?php echo esc_attr( $o ); ?>" placeholder="z. B. 92348 Berg bei Neumarkt i.d.OPf.">
		<label for="st_hinweis">Zusatz</label>
		<input type="text" id="st_hinweis" name="st_hinweis" value="<?php echo esc_attr( $h ); ?>" placeholder="z. B. 100 Jahre SG Altdorf · Eintritt frei">
		<label for="st_link">Link (optional)</label>
		<input type="url" id="st_link" name="st_link" value="<?php echo esc_attr( $l ); ?>" placeholder="https://… (Veranstalter, Facebook-Event)">
		<small>Vergangene Termine wandern automatisch in den „Rückblick“.</small>
	</div>
	<?php
}

add_action( 'save_post_termin', function ( $post_id ) {
	if ( ! isset( $_POST['st_termin_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['st_termin_nonce'] ), 'st_termin' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$datum = isset( $_POST['st_datum'] ) ? sanitize_text_field( wp_unslash( $_POST['st_datum'] ) ) : '';
	update_post_meta( $post_id, 'st_datum', preg_match( '/^\d{4}-\d{2}-\d{2}$/', $datum ) ? $datum : '' );
	$zeit = isset( $_POST['st_uhrzeit'] ) ? sanitize_text_field( wp_unslash( $_POST['st_uhrzeit'] ) ) : '';
	update_post_meta( $post_id, 'st_uhrzeit', preg_match( '/^\d{2}:\d{2}$/', $zeit ) ? $zeit : '' );
	foreach ( array( 'st_ort', 'st_hinweis' ) as $k ) {
		update_post_meta( $post_id, $k, isset( $_POST[ $k ] ) ? sanitize_text_field( wp_unslash( $_POST[ $k ] ) ) : '' );
	}
	update_post_meta( $post_id, 'st_link', isset( $_POST['st_link'] ) ? esc_url_raw( wp_unslash( $_POST['st_link'] ) ) : '' );
} );

/* Spalten in der Terminliste */
add_filter( 'manage_termin_posts_columns', function ( $cols ) {
	return array(
		'cb'         => $cols['cb'],
		'title'      => 'Fest',
		'st_datum'   => 'Datum',
		'st_ort'     => 'Ort',
		'st_status'  => 'Status',
	);
} );
add_action( 'manage_termin_posts_custom_column', function ( $col, $id ) {
	$d = get_post_meta( $id, 'st_datum', true );
	if ( 'st_datum' === $col ) {
		echo $d ? esc_html( date_i18n( 'D, d.m.Y', strtotime( $d ) ) ) : '—';
		$u = get_post_meta( $id, 'st_uhrzeit', true );
		echo $u ? ' · ' . esc_html( $u ) . ' Uhr' : '';
	} elseif ( 'st_ort' === $col ) {
		echo esc_html( get_post_meta( $id, 'st_ort', true ) );
	} elseif ( 'st_status' === $col ) {
		echo ( $d && $d >= current_time( 'Y-m-d' ) ) ? '<span style="color:#1a7f37;font-weight:600">kommend</span>' : '<span style="color:#787c82">vorbei</span>';
	}
}, 10, 2 );
add_filter( 'manage_edit-termin_sortable_columns', function ( $cols ) {
	$cols['st_datum'] = 'st_datum';
	return $cols;
} );
add_action( 'pre_get_posts', function ( $q ) {
	if ( is_admin() && $q->is_main_query() && 'termin' === $q->get( 'post_type' ) ) {
		$q->set( 'meta_key', 'st_datum' );
		$q->set( 'orderby', 'meta_value' );
		if ( ! $q->get( 'order' ) || ! isset( $_GET['orderby'] ) ) {
			$q->set( 'order', 'DESC' );
		}
	}
} );

/**
 * Termine holen.
 *
 * @param string $wann  'kommend' oder 'vorbei'
 * @param int    $anzahl -1 = alle
 */
function st_termine( $wann = 'kommend', $anzahl = -1 ) {
	$heute = current_time( 'Y-m-d' );
	return get_posts( array(
		'post_type'      => 'termin',
		'post_status'    => 'publish',
		'posts_per_page' => $anzahl,
		'meta_key'       => 'st_datum',
		'orderby'        => 'meta_value',
		'order'          => 'kommend' === $wann ? 'ASC' : 'DESC',
		'meta_query'     => array(
			array(
				'key'     => 'st_datum',
				'value'   => $heute,
				'compare' => 'kommend' === $wann ? '>=' : '<',
				'type'    => 'DATE',
			),
		),
	) );
}

/** Eine Terminzeile ausgeben. */
function st_termin_zeile( $post, $tag_text = '' ) {
	$d     = get_post_meta( $post->ID, 'st_datum', true );
	$u     = get_post_meta( $post->ID, 'st_uhrzeit', true );
	$o     = get_post_meta( $post->ID, 'st_ort', true );
	$h     = get_post_meta( $post->ID, 'st_hinweis', true );
	$link  = get_post_meta( $post->ID, 'st_link', true );
	$ts    = $d ? strtotime( $d ) : 0;
	$vorbei = $d && $d < current_time( 'Y-m-d' );
	$teile = array_filter( array( $o, $u ? $u . ' Uhr' : '', $h ) );
	if ( '' === $tag_text ) {
		$tag_text = $vorbei ? 'Vorbei' : ( $ts ? date_i18n( 'l', $ts ) : '' );
	}
	?>
	<div class="gig<?php echo $vorbei ? ' past' : ''; ?>">
		<div class="d"><span><?php echo esc_html( $ts ? date_i18n( 'M', $ts ) : '' ); ?></span><b><?php echo esc_html( $ts ? date_i18n( 'd', $ts ) : '?' ); ?></b></div>
		<div><h3><?php echo esc_html( get_the_title( $post ) ); ?></h3><p><?php echo esc_html( implode( ' · ', $teile ) ); ?></p></div>
		<?php if ( $link && ! $vorbei ) : ?>
			<a class="tag" href="<?php echo esc_url( $link ); ?>" target="_blank" rel="noopener">Infos →</a>
		<?php else : ?>
			<span class="tag"><?php echo esc_html( $tag_text ); ?></span>
		<?php endif; ?>
	</div>
	<?php
}

/** Platzhalterzeile „Euer Fest?“ */
function st_frei_zeile( $mit_link = true ) {
	if ( '1' !== (string) st_opt( 'frei_zeile' ) ) {
		return;
	}
	?>
	<div class="gig free">
		<div class="d"><span><?php echo esc_html( current_time( 'Y' ) + ( (int) current_time( 'n' ) >= 10 ? 1 : 0 ) ); ?></span><b>?</b></div>
		<div><h3><?php echo esc_html( st_opt( 'frei_titel' ) ); ?></h3><p><?php echo esc_html( st_opt( 'frei_text' ) ); ?></p></div>
		<?php if ( $mit_link ) : ?>
			<a class="tag" href="<?php echo esc_url( st_link( 'kontakt' ) ); ?>">Anfragen →</a>
		<?php else : ?>
			<span class="tag">Frei</span>
		<?php endif; ?>
	</div>
	<?php
}
