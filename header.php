<html>
<head>
	<?php wp_head();?>
</head>
<body>
	<header id="masthead" class="site-header" role="banner">
		<div class="header-main ">
			<h1 class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name');?></a></h1>
			<nav  id="primary-navigation" class="site-navigation primary-navigation container" role="navigation">
				<?php wp_nav_menu(array('theme_location' => 'primary', 'menu_class' => 'nav-menu', 'menu_id' => 'primary-menu'));?>
			</nav>
		</div>
	</header>