<!DOCTYPE HTML>
<html<?php echo ' class="slug-'.basename(get_permalink()).'"'; ?>>
<head>
<meta charset="utf-8">
<title>
<?php
	/* Print the <title> tag based on what is being viewed. */
	global $page, $paged;
	wp_title( '|', true, 'right' );
	bloginfo( 'name' );
	?>
</title>
<base href="<?php bloginfo('template_url'); ?>/">
<link href="style.css" rel="stylesheet" type="text/css" media="all">
<?php wp_head(); ?>
</head>
<body>
<div class="layout">
  <h1><a href="<?php echo esc_url( home_url( '/' ) ); ?>">
    <?php bloginfo( 'name' ); ?>
    </a></h1>
  <p>
    <?php bloginfo( 'description' ); ?>
  </p>
  <?php if ( is_active_sidebar( 'sidebar' ) ) {  ?>
  <hr>
  <?php dynamic_sidebar('sidebar'); ?>
  <?php } // end is_active_sidebar ?>
  <hr>
  <p><?php echo date('Y'); ?> &copy;
    <?php bloginfo( 'name' ); ?>
  </p>
</div>
<?php wp_footer(); ?>
</body>
</html>