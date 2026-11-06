<?php $f = array_slice( (array) st_opt( 'fakten' ), 0, 3 ); ?>
<header class="wh-hero"><div class="wrap">
	<div>
		<div class="kicker"><?php echo esc_html( st_opt( 'hero_kicker' ) ); ?></div>
		<h1><em><?php echo esc_html( st_opt( 'hero_zier' ) ); ?></em><?php echo esc_html( st_opt( 'hero_titel' ) ); ?></h1>
		<p><?php echo esc_html( st_opt( 'hero_text' ) ); ?></p>
		<div class="cta" style="display:flex;gap:12px;flex-wrap:wrap"><a class="btn red" href="<?php echo esc_url( st_link( 'kontakt' ) ); ?>"><?php echo esc_html( st_opt( 'hero_btn1' ) ); ?></a><a class="btn ghost" href="<?php echo esc_url( st_link( 'medien' ) ); ?>"><?php echo esc_html( st_opt( 'hero_btn2' ) ); ?></a></div>
		<?php if ( $f ) : ?><div class="badge"><?php foreach ( $f as $x ) : ?><div><b><?php echo esc_html( $x['zahl'] ); ?></b><?php echo esc_html( $x['text'] ); ?></div><?php endforeach; ?></div><?php endif; ?>
	</div>
	<div class="pic"><img src="<?php echo esc_url( st_img( st_opt( 'hero_bild' ), 'large' ) ); ?>" alt="<?php echo esc_attr( 'Die Band ' . get_bloginfo( 'name' ) ); ?>"><div class="stamp">Bayrisch<br>&amp; Rockig<?php echo ! empty( $f[0]['zahl'] ) ? '<br>seit ' . esc_html( $f[0]['zahl'] ) : ''; ?></div></div>
</div></header>
