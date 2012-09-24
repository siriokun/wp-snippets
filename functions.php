<?php
// Add Widget Support
if ( function_exists('register_sidebar') ) {
    register_sidebar(array(
		'name' => __( 'Sidebar' ),
		'id' => 'sidebar',
		'description' => __( '' ),
		'before_title' => '<h3>',
		'after_title' => '</h3>',
		'before_widget' => '<div class="widget %2$s">',
		'after_widget'  => '</div>',
	));
}
/**
 * HTML Widget
 * @link https://gist.github.com/884827
 */
class WP_Widget_HTML extends WP_Widget {

  function WP_Widget_HTML() {
    $widget_ops = array('classname' => 'widget_html', 'description' => 'Custom HTML Snippet');
    $control_ops = array('width' => 400, 'height' => 350);
    $this->WP_Widget('html', __('HTML'), $widget_ops, $control_ops);
  }

  function widget( $args, $instance ) {
    add_filter('widget_html', 'do_shortcode');
    extract($args);
    $html = apply_filters('widget_html', $instance['html'], $instance );
    echo $before_widget;
    echo $html;
    echo $after_widget;
  }

  function update( $new_instance, $old_instance ) {
    $instance = $old_instance;
    if ( current_user_can('unfiltered_html') )
      $instance['html'] = $new_instance['html'];
    else
      $instance['html'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['html']) ) ); // wp_filter_post_kses() expects slashed
      return $instance;
  }

  function form( $instance ) {
    $instance = wp_parse_args( (array) $instance, array( 'html' => '' ) );
    $html = format_to_edit($instance['html']);
?>
<textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('html'); ?>" name="<?php echo $this->get_field_name('html'); ?>"><?php echo $html; ?></textarea>
<?php
  }
}
register_widget('WP_Widget_HTML');
/**
 * Hide Welcome Panel for WordPress Multisite
 * @link http://wpengineer.com/2470/hide-welcome-panel-for-wordpress-multisite/
 */
function si_hide_welcome_panel_for_multisite() {
	
	if ( ! is_multisite() ) // don't check, if you will use this on single install of WP
		return;
	
	if ( 2 === (int) get_user_meta( get_current_user_id(), 'show_welcome_panel', TRUE ) )
		update_user_meta( get_current_user_id(), 'show_welcome_panel', 0 );
}
add_action( 'load-index.php', 'si_hide_welcome_panel_for_multisite' );
/**
 * Move Featured Image Metabox on 'gallery' post type
 * @author Bill Erickson
 * @link http://www.billerickson.net/code/move-featured-image-metabox
 */
function si_gallery_image_metabox() {
	remove_meta_box( 'postimagediv', 'gallery', 'side' );
	add_meta_box('postimagediv', __('Custom Image'), 'post_thumbnail_meta_box', 'gallery', 'normal', 'high');
}
add_action('do_meta_boxes', 'si_gallery_image_metabox' );
/**
 * Take the textarea code out of the default fields and print it on top.
 * @link https://gist.github.com/2553604
 * @param  array $input Default fields if called as filter
 * @return string|void
 */
function si_move_textarea( $input = array () )
{
	static $textarea = '';

	if ( 'comment_form_defaults' === current_filter() )
	{
		// Copy the field to our internal variable …
		$textarea = $input['comment_field'];
		// … and remove it from the defaults array.
		$input['comment_field'] = '';
		return $input;
	}

	print $textarea;
}
add_action( 'comment_form_defaults', 'si_move_textarea' );
add_action( 'comment_form_top', 'si_move_textarea' );
/**
 * Remove WordPress Comment Form URL field
 * @link https://forrst.com/posts/Remove_WordPress_Comment_Form_URL_field-22J
 */
function si_hide_comment_url($fields) {
    unset($fields['url']);
    return $fields;
}
add_filter('comment_form_default_fields','si_hide_comment_url');
/**
 * Fills the default content for post type 'post' if it is not empty.
 * See wp-admin/includes/post.php function get_default_post_to_edit()
 * There are also the filters 'default_title' and 'default_excerpt'
 * @param string $content
 * @param object $post
 * @return string
 */
function si_preset_editor_content( $content, $post )
{
    if ( '' !== $content or 'post' !== $post->post_type )
    {
        return $content;
    }
    
    return 'This is the <em>default</em> content. You may customize it.';
}
add_filter( 'default_content', 'si_preset_editor_content', 10, 2 );
/*
* Disables the header image for properly written themes like TwentyEleven.
* @link https://gist.github.com/2553604
*/
function remove_header_image_path()
{
	return 'remove-header';
}
add_filter( 'theme_mod_header_image', 'remove_header_image_path', 11 );
function remove_header_image_support()
{
	remove_submenu_page( 'themes.php', 'custom-header' );
	// The page is still accessible, so we disable at least uploads.
	_remove_theme_support( 'custom-header' );
	_remove_theme_support( 'custom-header-uploads' );
}
add_action( 'admin_menu', 'remove_header_image_support', 20 );
/**
 * Create a nav menu with very basic markup.
 * @link https://gist.github.com/1053467
 */
class Simple_Walker_Nav_Menu extends Walker_Nav_Menu
{
	/**
	 * Start the element output.
	 *
	 * @param  string $output Passed by reference. Used to append additional content.
	 * @param  object $item   Menu item data object.
	 * @param  int $depth     Depth of menu item. May be used for padding.
	 * @param  array $args    Additional strings.
	 * @return void
	 */
	public function start_el( &$output, $item, $depth, $args )
	{
		$output     .= '<li>';
		$attributes  = '';

		! empty ( $item->attr_title )
			// Avoid redundant titles
			and $item->attr_title !== $item->title
			and $attributes .= ' title="' . esc_attr( $item->attr_title ) .'"';

		! empty ( $item->url )
			and $attributes .= ' href="' . esc_attr( $item->url ) .'"';

		$attributes  = trim( $attributes );
		$title       = apply_filters( 'the_title', $item->title, $item->ID );
		$item_output = "$args->before<a $attributes>$args->link_before$title</a>"
						. "$args->link_after$args->after";

		// Since $output is called by reference we don't need to return anything.
		$output .= apply_filters(
			'walker_nav_menu_start_el'
			,   $item_output
			,   $item
			,   $depth
			,   $args
		);
	}

	/**
	 * @see Walker::start_lvl()
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @return void
	 */
	public function start_lvl( &$output )
	{
		$output .= '<ul class="sub-menu">';
	}

	/**
	 * @see Walker::end_lvl()
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @return void
	 */
	public function end_lvl( &$output )
	{
		$output .= '</ul>';
	}

	/**
	 * @see Walker::end_el()
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @return void
	 */
	function end_el( &$output )
	{
		$output .= '</li>';
	}
}
/**
 * Remove inline CSS generated on wp_head when Recent Comments widget active.
 * @link https://gist.github.com/2887406
 */
function si_remove_recent_comments_css() {  
        global $wp_widget_factory;  
        remove_action( 'wp_head', array( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style' ) );  
    }  
add_action( 'widgets_init', 'si_remove_recent_comments_css' );