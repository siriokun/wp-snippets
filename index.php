<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>
<?php
	/* Print the <title> tag based on what is being viewed. */
	global $page, $paged;
	wp_title( '|', true, 'right' );
	?>
</title>
<base href="<?php bloginfo('template_url'); ?>/">
<link href="style.css" rel="stylesheet" type="text/css" media="all">
<?php wp_head(); ?>
</head>

<body>
<h1><a href="<?php echo esc_url( home_url( '/' ) ); ?>">
  <?php bloginfo( 'name' ); ?>
  </a></h1>
<p>
  <?php bloginfo( 'description' ); ?>
</p>
<?php wp_footer(); ?>
</body>
</html>