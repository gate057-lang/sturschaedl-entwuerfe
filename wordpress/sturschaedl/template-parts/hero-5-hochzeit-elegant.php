<header class="el-hero"><div class="wrap">
	<div class="kicker"><?php echo esc_html( st_opt( 'hero_kicker' ) ); ?></div>
	<h1><?php echo esc_html( st_opt( 'hero_zier' ) ); ?><br><i><?php echo esc_html( st_opt( 'hero_titel' ) ); ?></i></h1>
	<p><?php echo esc_html( st_opt( 'hero_text' ) ); ?></p>
	<div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap"><a class="btn red" href="<?php echo esc_url( st_link( 'kontakt' ) ); ?>"><?php echo esc_html( st_opt( 'hero_btn1' ) ); ?></a><a class="btn ghost" href="<?php echo esc_url( st_link( 'hochzeit' ) ); ?>">So läuft’s ab</a></div>
	<div class="arch">
		<img class="s" src="<?php echo esc_url( st_img( st_opt( 'hero_bild2' ), 'large' ) ); ?>" alt="">
		<img class="m" src="<?php echo esc_url( st_img( st_opt( 'hero_bild' ), 'large' ) ); ?>" alt="<?php echo esc_attr( 'Die Band ' . get_bloginfo( 'name' ) ); ?>">
		<img class="s" src="<?php echo esc_url( st_img( st_opt( 'hero_bild3' ), 'large' ) ); ?>" alt="">
	</div>
	<div class="sig">Eure <?php echo esc_html( st_opt( 'hero_titel' ) ); ?></div>
</div></header>
