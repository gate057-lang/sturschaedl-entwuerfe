<header class="rr-hero">
	<div class="ph" style="background-image:url('<?php echo esc_url( st_img( st_opt( 'hero_bild' ), 'st-hero' ) ); ?>')"></div>
	<div class="band raute"></div>
	<img class="bull" src="<?php echo esc_url( ST_URI . '/assets/img/bulle-hell.png' ); ?>" alt="">
	<div class="wrap">
		<div class="kicker"><?php echo esc_html( st_opt( 'hero_kicker' ) ); ?></div>
		<h1><span><?php echo esc_html( st_opt( 'hero_zier' ) ); ?></span><?php echo esc_html( st_opt( 'hero_titel' ) ); ?></h1>
		<p><?php echo esc_html( st_opt( 'hero_text' ) ); ?></p>
		<div style="display:flex;gap:14px;flex-wrap:wrap"><a class="btn red" href="<?php echo esc_url( st_link( 'kontakt' ) ); ?>"><?php echo esc_html( st_opt( 'hero_btn1' ) ); ?></a><a class="btn ghost" href="<?php echo esc_url( st_link( 'medien' ) ); ?>"><?php echo esc_html( st_opt( 'hero_btn2' ) ); ?></a></div>
	</div>
</header>
