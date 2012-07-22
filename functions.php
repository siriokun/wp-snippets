<?php 
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