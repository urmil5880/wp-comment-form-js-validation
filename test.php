https://wordpress.stackexchange.com/questions/168022/get-taxonomy-terms-for-parent-and-child

https://wordpress.stackexchange.com/questions/284765/filtering-custom-posts-with-ajax

https://stackoverflow.com/questions/33271973/wordpress-php-dropdown-filter-2x-with-single-ajax-request

https://itsmereal.com/simple-ajax-filter-or-search-for-wordpress/

<?php
// Register Custom Post Type
function custom_post_type_order_book() {

	$labels = array(
		'name'                  => _x( 'Book Orders', 'Post Type General Name', 'text_domain' ),
		'singular_name'         => _x( 'Book Order', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'             => __( 'Book Orders', 'text_domain' ),
		'name_admin_bar'        => __( 'Book Order', 'text_domain' ),
		'archives'              => __( 'Item Archives', 'text_domain' ),
		'attributes'            => __( 'Item Attributes', 'text_domain' ),
		'parent_item_colon'     => __( 'Parent Item:', 'text_domain' ),
		'all_items'             => __( 'Orders', 'text_domain' ),
		'add_new_item'          => __( 'Add New Item', 'text_domain' ),
		'add_new'               => __( 'Add New', 'text_domain' ),
		'new_item'              => __( 'New Item', 'text_domain' ),
		'edit_item'             => __( 'Edit Item', 'text_domain' ),
		'update_item'           => __( 'Update Item', 'text_domain' ),
		'view_item'             => __( 'View Item', 'text_domain' ),
		'view_items'            => __( 'View Items', 'text_domain' ),
		'search_items'          => __( 'Search Item', 'text_domain' ),
		'not_found'             => __( 'Not found', 'text_domain' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
		'featured_image'        => __( 'Featured Image', 'text_domain' ),
		'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
		'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
		'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
		'insert_into_item'      => __( 'Insert into item', 'text_domain' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'text_domain' ),
		'items_list'            => __( 'Items list', 'text_domain' ),
		'items_list_navigation' => __( 'Items list navigation', 'text_domain' ),
		'filter_items_list'     => __( 'Filter items list', 'text_domain' ),
	);
	$args = array(
		'label'                 => __( 'Book order', 'text_domain' ),
		'description'           => __( 'Post Type Description', 'text_domain' ),
		'labels'                => $labels,
		'supports'              => array(),
		'taxonomies'            => array(),
		'hierarchical'          => false,
		'public'                => false,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => false,
		'can_export'            => true,
		'has_archive'           => false,		
		'exclude_from_search'   => false,
		'publicly_queryable'    => false,
		'capability_type'       => 'post',
		'rewrite'				=> false,
	);
	register_post_type( 'order_book', $args );

}
add_action( 'init', 'custom_post_type_order_book', 0 );


function remove_post_type_order_field() {
	remove_post_type_support( 'order_book', 'title' );
	remove_post_type_support( 'order_book', 'editor' );
	remove_post_type_support( 'order_book', 'author' );
	remove_post_type_support( 'order_book', 'comments' );
	remove_post_type_support( 'order_book', 'revisions' );
}
add_action( 'init', 'remove_post_type_order_field' );


function disable_new_posts() {
// Hide sidebar link
global $submenu;
unset($submenu['edit.php?post_type=order_book'][10]);

// Hide link on listing page
if (isset($_GET['post_type']) && $_GET['post_type'] == 'order_book') {
    echo '<style type="text/css">
    
    a.page-title-action {display:none; }
    </style>';
}
}
add_action('admin_menu', 'disable_new_posts');


add_filter( 'post_row_actions', 'remove_row_actions', 10, 1 );
function remove_row_actions( $actions )
{
    if( get_post_type() === 'order_book' )
       // unset( $actions['edit'] );
        //unset( $actions['view'] );
        //unset( $actions['trash'] );
        unset( $actions['inline hide-if-no-js'] );
    return $actions;
}


function hide_publishing_actions(){
        $my_post_type = 'order_book';
        global $post;
        if($post->post_type == $my_post_type){
            echo '
                <style type="text/css">
                    #postbox-container-1{
                        display:none;
                    }
                    a.page-title-action {display:none; }
                </style>
            ';
        }
}
add_action('admin_head-post.php', 'hide_publishing_actions');
add_action('admin_head-post-new.php', 'hide_publishing_actions');

// Add the order Meta Boxes
function add_book_order_metaboxes() {
	add_meta_box('wpt_order_details', 'Order Details', 'wpt_order_details', 'order_book', 'advanced', 'default');
}
add_action( 'add_meta_boxes', 'add_book_order_metaboxes' );


// The order Location Metabox
function wpt_order_details() {
	global $post;
	
	// Noncename needed to verify where the data originated
	echo '<input type="hidden" name="bookordermeta_noncename" id="bookordermeta_noncename" value="' . 
	wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
	
    $buyer_name			= get_post_meta($post->ID, '_buyer_name', true);
	$buyer_address		= get_post_meta($post->ID, '_buyer_address', true);
	$buyer_email		= get_post_meta($post->ID, '_buyer_email', true);
	$buyer_mobile		= get_post_meta($post->ID, '_buyer_mobile', true);
	$buyer_book_name	= get_post_meta($post->ID, '_buyer_book_name', true);
    $buyer_book_id		= get_post_meta($post->ID, '_buyer_book_id', true);
    
	
	// Echo out the field
	/*echo '<p>Name:</p>';
	echo '<input type="text" name="_buyer_name" value="' . $buyer_name  . '" class="widefat" />';
	echo '<p>Address:</p>';
	echo '<input type="text" name="_buyer_address" value="' . $buyer_address  . '" class="widefat" />';
	echo '<p>Email:</p>';
	echo '<input type="text" name="_buyer_email" value="' . $buyer_email  . '" class="widefat" />';
	echo '<p>Mobile:</p>';
	echo '<input type="text" name="_buyer_mobile" value="' . $buyer_mobile  . '" class="widefat" />';
	echo '<p>Book Name:</p>';
	echo '<input type="text" name="_buyer_book_name" value="' . $buyer_book_name  . '" class="widefat" />';*/
	
	
	// Echo out the field
	echo '<p><strong>Name:</strong></p>';
	echo '<p>'.$buyer_name.'</p>';
	echo '<p><strong>Address:</strong></p>';
	echo '<p>'.$buyer_address.'</p>';
	echo '<p><strong>Email:</strong></p>';
	echo '<p>'.$buyer_email.'</p>';
	echo '<p><strong>Mobile:</strong></p>';
	echo '<p>'.$buyer_mobile.'</p>';
	echo '<p><strong>Book Name:</strong></p>';
	echo '<p><a href="">'.$buyer_book_name.'</a></p>';

}


// Save the Metabox Data

function wpt_save_book_order_meta($post_id, $post) {
	
	// verify this came from the our screen and with proper authorization,
	// because save_post can be triggered at other times
	if ( !wp_verify_nonce( $_POST['bookordermeta_noncename'], plugin_basename(__FILE__) )) {
	return $post->ID;
	}

	// Is the user allowed to edit the post or page?
	if ( !current_user_can( 'edit_post', $post->ID ))
		return $post->ID;

	// OK, we're authenticated: we need to find and save the data
	// We'll put it into an array to make it easier to loop though.
	
	$events_meta['_buyer_name'] 		= $_POST['_buyer_name'];
	$events_meta['_buyer_address'] 		= $_POST['_buyer_address'];
	$events_meta['_buyer_email'] 		= $_POST['_buyer_email'];
	$events_meta['_buyer_mobile'] 		= $_POST['_buyer_mobile'];
	$events_meta['_buyer_book_name'] 	= $_POST['_buyer_book_name'];
	
	// Add values of $events_meta as custom fields
	
	foreach ($events_meta as $key => $value) { // Cycle through the $events_meta array!
		if( $post->post_type == 'revision' ) return; // Don't store custom data twice
		$value = implode(',', (array)$value); // If $value is an array, make it a CSV (unlikely)
		if(get_post_meta($post->ID, $key, FALSE)) { // If the custom field already has a value
			update_post_meta($post->ID, $key, $value);
		} else { // If the custom field doesn't have a value
			add_post_meta($post->ID, $key, $value);
		}
		if(!$value) delete_post_meta($post->ID, $key); // Delete if blank
	}

}
//add_action('save_post', 'wpt_save_book_order_meta', 1, 2); // save the custom fields


// Add the JS
function qd_ajax_scripts() {
  wp_enqueue_script( 'frontend-ajax', get_template_directory_uri() . '/js/frontend-ajax.js', array('jquery'));
  wp_localize_script( 'frontend-ajax', 'frontendajax', array(
    // URL to wp-admin/admin-ajax.php to process the request
    'ajaxurl' => admin_url( 'admin-ajax.php' ),
    // generate a nonce with a unique ID "myajax-post-comment-nonce"
    // so that you can check it later when an AJAX request is sent
   // 'security' => wp_create_nonce( 'my-special-string' )
  ));
}
add_action( 'wp_enqueue_scripts', 'qd_ajax_scripts' );




function submit_book(){
	
	//print_r($_POST);
	//exit;
	
	if(isset($_POST['action'])){
		$buyer_name			= $_POST['buy_name'];
		$buyer_address		= $_POST['buy_address'];
		$buyer_email		= $_POST['buy_email'];
		$buyer_mobile		= $_POST['buy_mobile'];
		$buyer_book_name	= $_POST['buy_book_name'];
		$buyer_book_id	= $_POST['buy_book_id'];
		
		$order_information = array(
			'post_type' => 'order_book',
			'post_status' => 'publish'
		);
		$post_id = wp_insert_post( $order_information );
		
		if($post_id != 0){
			
			$update_post =  array( 'ID' => $post_id, 'post_title' => '#'.$post_id);
			wp_update_post($update_post);
			update_post_meta( $post_id, '_buyer_name', $buyer_name);
			update_post_meta( $post_id, '_buyer_address', $buyer_address);
			update_post_meta( $post_id, '_buyer_email', $buyer_email);
			update_post_meta( $post_id, '_buyer_mobile', $buyer_mobile);
			update_post_meta( $post_id, '_buyer_book_name', $buyer_book_name);
			update_post_meta( $post_id, '_buyer_book_id', $buyer_book_id);
			
			
			############################# send email start ##########################
			$admin_email = 'test@gmail.com';
			$admin_email2 = 'test@yahoo.co.in';
			
			$sender_email = $buyer_email;
			$sender_name = $buyer_name;
			
			$subject = 'Buy Book - '.$buyer_book_name;
			
			$headers = "From: " . strip_tags($sender_email) . "\r\n";
			$headers .= "Reply-To: ". strip_tags($sender_email) . "\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
			
			$message = '<html><body>';
			$message .= "<strong>Name: </strong>".$buyer_name;
			$message .= "<br>";
			$message .= "<strong>Address : </strong>".$buyer_address;
			$message .= "<br>";
			$message .= "<strong>Email : </strong>".$buyer_email;
			$message .= "<br>";
			$message .= "<strong>Mobile : </strong>".$buyer_mobile;
			$message .= "<br>";
			$message .= "<strong>Book name : </strong>".$buyer_book_name;
			$message .= "<br>";
			$message .= "</body></html>";				
			
			$mailsent = wp_mail($admin_email,$subject,$message,$headers); 
			$mailsent = wp_mail($admin_email2,$subject,$message,$headers); 
			############################# send email end ############################
			
			$response_array = array( 'status' => 'success', 'message' => 'Your Order added successfully' );
		}
	}
	echo json_encode($response_array);	
	die();
}
add_action('wp_ajax_nopriv_submit_book', 'submit_book');
add_action('wp_ajax_submit_book', 'submit_book');

// shortcode: [buy-book-form]
function buyBookForm( $atts ) {
	// The form
	
	if(isset($_GET['bookid']) && $_GET['bookid'] != ''){
		$bookid		= $_GET['bookid'];
		$bookname	= get_the_title($bookid);
	}
	
	ob_start();
	?>

<div class="row">
<div class="col-md-6 col-xs-12 buy_about_img">	
	
<?php if (has_post_thumbnail( $bookid ) ){ ?>
  <?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( $bookid ), 'single-post-thumbnail' ); ?>
		<img src="<?php echo $image[0]; ?>" />
  <?php }else{ ?>
		<img src="<?php echo get_template_directory_uri(); ?>/images/about_rightimg.png">
<?php } ?>
	
</div>	

<div class="col-md-6 col-xs-12">
	
	<div id="buy-book-form">
		<div id="notic_buy_book" class="alert"></div>
		<form role="form" id="buy_book_form" method="post">
			<div class="frm-field">
				<label for="buy_name">Name <span class="required">*</span></label>
				<input type="text" name="buy_name" id="buy_name" class="isrequired">
			</div>
			<div class="frm-field">
				<label for="buy_address">Address <span class="required">*</span></label>
				<textarea name="buy_address" id="buy_address" class="isrequired"></textarea>
			</div>
			<div class="frm-field">
				<label for="buy_email">Email <span class="required">*</span></label>
				<input type="text" name="buy_email" id="buy_email" class="isrequired">
			</div>
			<div class="frm-field">
				<label for="buy_mobile">Mobile <span class="required">*</span></label>
				<input type="text" name="buy_mobile" id="buy_mobile" class="isrequired">
			</div>
			<div class="frm-field">
				<label for="buy_book_name">Book Name <span class="required">*</span></label>
				<input type="text" name="buy_book_name" id="buy_book_name" class="isrequired" value="<?php echo $bookname; ?>">
			</div>
			
			<div class="hidden_field">
				<input type="hidden" name="action" value="submit_book">
				<input type="hidden" name="buy_book_id" id="buy_book_id" value="<?php echo $bookid; ?>">
			</div>
			<div class="frm-field">
				<input class="submit_book_btn" type="button" value="Done">
				<span id="buy_book_form_loding" class="qd-ajax-loader"></span>
			</div>
		</form>
	</div>
	
</div>
</div>	
	
	<?php
	$html = ob_get_contents();
	ob_end_clean();
	return $html;
}
add_shortcode( 'buy-book', 'buyBookForm' );
?>
