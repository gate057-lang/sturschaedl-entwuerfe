<?php
/**
 * Seitenkopf-Felder für Seiten: Kicker und Zierzeile (Untertitel = Textauszug).
 * Als Seitenleisten-Kasten im Block-Editor (klassische Meta-Box, funktioniert überall).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'add_meta_boxes_page', function () {
	add_meta_box( 'st_kopf', 'Seitenkopf (Sturschädl)', 'st_kopf_box', 'page', 'side', 'high' );
} );

function st_kopf_box( $post ) {
	wp_nonce_field( 'st_kopf', 'st_kopf_nonce' );
	?>
	<p><label for="st_kicker"><strong>Kleine rote Zeile</strong></label><br>
	<input type="text" id="st_kicker" name="st_kicker" class="widefat" value="<?php echo esc_attr( get_post_meta( $post->ID, 'st_kicker', true ) ); ?>" placeholder="z. B. Live-Auftritte"></p>
	<p><label for="st_zier"><strong>Zierschrift über dem Titel</strong></label><br>
	<input type="text" id="st_zier" name="st_zier" class="widefat" value="<?php echo esc_attr( get_post_meta( $post->ID, 'st_zier', true ) ); ?>" placeholder="z. B. Euer Tag,"></p>
	<p class="description">Hintergrundbild = „Beitragsbild“, Untertitel = „Textauszug“ (beides ebenfalls hier rechts in der Seitenleiste).</p>
	<?php
}

add_action( 'save_post_page', function ( $post_id ) {
	if ( ! isset( $_POST['st_kopf_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['st_kopf_nonce'] ), 'st_kopf' ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	foreach ( array( 'st_kicker', 'st_zier' ) as $k ) {
		update_post_meta( $post_id, $k, isset( $_POST[ $k ] ) ? sanitize_text_field( wp_unslash( $_POST[ $k ] ) ) : '' );
	}
} );
