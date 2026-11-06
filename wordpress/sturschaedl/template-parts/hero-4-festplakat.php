<?php $f = array_slice( (array) st_opt( 'fakten' ), 0, 3 ); ?>
<header class="pl-hero"><div class="wrap"><div class="poster">
	<img class="bull" src="<?php echo esc_url( ST_URI . '/assets/img/bulle-dunkel.png' ); ?>" alt="">
	<div class="top"><?php echo esc_html( st_opt( 'hero_zier' ) ); ?></div>
	<div class="stars">★ ★ ★ ★ ★</div>
	<h1><?php echo esc_html( st_opt( 'hero_titel' ) ); ?></h1>
	<div class="sub"><?php echo esc_html( st_opt( 'hero_kicker' ) ); ?></div>
	<img class="ph" src="<?php echo esc_url( st_img( st_opt( 'hero_bild' ), 'large' ) ); ?>" alt="<?php echo esc_attr( 'Die Band ' . get_bloginfo( 'name' ) ); ?>">
	<?php if ( $f ) : ?><div class="bottom"><?php foreach ( $f as $x ) : ?><div><b><?php echo esc_html( $x['zahl'] ); ?></b><?php echo esc_html( $x['text'] ); ?></div><?php endforeach; ?></div><?php endif; ?>
	<div style="margin-top:28px;display:flex;gap:14px;justify-content:center;flex-wrap:wrap"><a class="btn red" href="<?php echo esc_url( st_link( 'kontakt' ) ); ?>"><?php echo esc_html( st_opt( 'hero_btn1' ) ); ?></a><a class="btn ghost" href="<?php echo esc_url( st_link( 'termine' ) ); ?>">Termine</a></div>
</div></div></header>
