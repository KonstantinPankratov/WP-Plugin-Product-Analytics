<?php
/*
Plugin Name: Analytics
Plugin URI: 
Description: Collects information 
Version: 1.0.1
Author: Konstantin Pankratov
Author URI: http://kopa.pw/
*/

include( plugin_dir_path( __FILE__ ) . '\users.php');

/**
 * Counts product views
 *
 * @param null
 * @return void
 */
function analytics_count_products_views()
{
	if ( !is_singular('product') )
		return;
	

	global $post;

	$key = 'analytics_product_views';

	$post_id = $post->ID;
	$user_id = get_current_user_id();

    $post_count = get_post_meta($post_id, $key, true);
    $user_count = get_user_meta($user_id, $key, true);

    if ($post_count == '') {
        $post_count = 1;
        delete_post_meta($post_id, $key);
        add_post_meta($post_id, $key, $post_count);
    } else {
        $post_count++;
        update_post_meta($post_id, $key, $post_count);
    }

    if ($user_count == '') {
        $user_count = 1;
        delete_user_meta($user_id, $key);
        add_user_meta($user_id, $key, $user_count);
    } else {
        $user_count++;
        update_user_meta($user_id, $key, $user_count);
    }
}

add_action( 'template_redirect', 'analytics_count_products_views', 25 );

/**
 * Counts additions to the wishlist
 *
 * @param int $post_id Product ID
 * @param int $wish_id Wishlist ID
 * @param int $user_id User ID
 * @return void
 */

function analytics_count_wishlists( $post_id, $wish_id, $user_id )
{
	$key = 'analytics_product_wishlists';

    $post_count = get_post_meta($post_id, $key, true);
    $user_count = get_user_meta($user_id, $key, true);

    if ($post_count == '') {
        $post_count = 1;
        delete_post_meta($post_id, $key);
        add_post_meta($post_id, $key, $post_count);
    } else {
        $post_count++;
        update_post_meta($post_id, $key, $post_count);
    }

    if ($user_count == '') {
        $user_count = 1;
        delete_user_meta($user_id, $key);
        add_user_meta($user_id, $key, $user_count);
    } else {
        $user_count++;
        update_user_meta($user_id, $key, $user_count);
    }
}

add_action( 'yith_wcwl_adding_to_wishlist', 'analytics_count_wishlists', 25, 3);

/**
 * Counts clicks on "Check It Out" buttons
 *
 * @param null
 * @return void
 */
function analytics_count_check_it_out()
{
	$post_id = intval( $_POST['product_id'] );
	$user_id = get_current_user_id();

	$key = 'analytics_product_check_it_out';

    $post_count = get_post_meta($post_id, $key, true);
    $user_count = get_user_meta($user_id, $key, true);

    if ($post_count == '') {
        $post_count = 1;
        delete_post_meta($post_id, $key);
        add_post_meta($post_id, $key, $post_count);
    } else {
        $post_count++;
        update_post_meta($post_id, $key, $post_count);
    }

    if ($user_count == '') {
        $user_count = 1;
        delete_user_meta($user_id, $key);
        add_user_meta($user_id, $key, $user_count);
    } else {
        $user_count++;
        update_user_meta($user_id, $key, $user_count);
    }

    wp_die();
}

add_action( 'wp_ajax_analytics_count_check_it_out', 'analytics_count_check_it_out' );

/**
 * Adds JavaScript handler that binds click event to "Check It Out" buttons
 *
 * @param null
 * @return void
 */
function analytics_count_check_it_out_js()
{ ?>
	<script type="text/javascript" >
	jQuery(document).ready(function($) {

		var btns = document.getElementsByClassName('add-to-cart-btn');

			for (var i = 0; i < btns.length; i++)
			{
				btns[i].addEventListener('click', function() {

					event.preventDefault();

					var product_id = this.dataset.product_id;
					var url = this.href;

					var data = {
						'action': 'analytics_count_check_it_out',
						'product_id': product_id,
						'href': url
					};

					jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>", data, function(response) {
						console.log('Got this from the server: ' + response);
						window.open( url, '_blank' );
					});

				}, false);
			}

	});
	</script>
<?php
}

add_action( 'wp_footer', 'analytics_count_check_it_out_js' );

/*
 * Setup
 */


/**
 * Adds admin menu page
 *
 * @param null
 * @return callable
 */
function setup_plugin()
{
	add_menu_page( 'Analytics', 'Analytics', 'export', 'own-analytics', 'output', 'dashicons-chart-line', 2 );
}

add_action('admin_menu', 'setup_plugin');

/**
 * Generates content of admin menu page
 *
 * @param null
 * @return void
 */
function output()
{
	$table = new Table();
	$table->prepare_items();

	echo '<div class="wrap">';
	echo '<h1 class="wp-heading-inline">Analytics</h1>';
	
	$table->display();
	echo '</div>';
}

if (!class_exists('WP_List_Table'))
{
   require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Generates table with analytics
 */
class Table extends WP_List_Table
{
	/**
	 * Table settings
	 *
	 * @param null
	 * @return void
	 */
	function __construct()
	{
		parent::__construct( array(
			'singular'=> 'wp_list_text_link',
			'plural' => 'wp_list_test_links',
			'ajax'   => false
		) );
	}

	/**
	 * Extra table nav
	 *
	 * @param string $which Location of extra table navigation
	 * @return void
	 */
	function extra_tablenav( $which )
	{
		if ( $which == "bottom" ){
			echo '<p class="submit">';
				echo '<a href="'. $_SERVER['REQUEST_URI'] .'&action=export_to_csv&_wpnonce='. wp_create_nonce( 'export_to_csv' ) .'" class="button Secondary">';
					_e('Export CSV', 'OwnTheme');
				echo '</a>';
			echo '</p>';
		}
	}

	/**
	 * Table columns
	 *
	 * @param null
	 * @return void
	 */
	function get_columns()
	{
		return $columns= array(
			'ID'            => __('ID'),
			'post_title'    => __('Title'),
			'post_category' => __('Category'),
			'post_date'     => __('Date'),
			'post_sku'      => __('SKU'),
			'views'         => __('Views count'),
			'wishlist'      => __('Wishlist count'),
			'check_it_out'  => __('Check it out clicks'),
		);
	}

	/**
	 * Sets sortable columns
	 *
	 * @param null
	 * @return void
	 */
	public function get_sortable_columns()
	{
		return $sortable = array(
			'ID' => array( 'ID', false ),
			'post_title' => array( 'post_title', false ),
			'post_date' => array( 'post_date', false ),
		);
	}

	/**
	 * Defines default row values
	 *
	 * @param null
	 * @return void
	 */
	function column_default( $item, $column_name )
	{

		$product = wc_get_product( $item->ID );

		switch( $column_name ) {
			case 'ID':
			case 'post_title':
			case 'post_date':
				return $item->$column_name;
			case 'post_category':
				return wp_get_post_terms($item->ID, 'product_cat')[0]->name;
			case 'post_sku':
				return $product->sku;
			case 'views':
				$key = 'analytics_product_views';
    			return get_post_meta($item->ID, $key, true);
    		case 'wishlist':
				$key = 'analytics_product_wishlists';
    			return get_post_meta($item->ID, $key, true);	
    		case 'check_it_out':
				$key = 'analytics_product_check_it_out';
    			return get_post_meta($item->ID, $key, true);	
			default:
				return print_r( $item, true ) ;
		}
	}

	/**
	 * Retrieve posts data from DB
	 *
	 * @param null
	 * @return void
	 */
	function prepare_items()
	{
		global $wpdb, $_wp_column_headers;
		$screen = get_current_screen();

		$prefix = $wpdb->prefix;

		$query = "SELECT * FROM {$prefix}posts WHERE post_type = 'product' AND post_status = 'publish'";

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$query .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$query .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		} else {
			$query .= ' ORDER BY ID DESC';
		}

		$page_number = 1;
		$perpage = 15;

		/* -- Pagination parameters -- */
		$totalitems = $wpdb->query($query);
		$paged = !empty($_GET["paged"]) ? $_GET["paged"] : '';

		if ( empty($paged) || !is_numeric($paged) || $paged <= 0 )
			$paged=1;

		$totalpages = ceil( $totalitems / $perpage );

		if ( !empty($paged) && !empty($perpage)) {
			$offset = ($paged - 1) * $perpage;
			$query .= ' LIMIT '. (int) $offset .','. (int)$perpage;
		}

		$this->set_pagination_args( array(
			"total_items" => $totalitems,
			"total_pages" => $totalpages,
			"per_page" => $perpage,
		));

		/* -- Register the Columns -- */
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);

		/* -- Fetch the items -- */
		$this->items = $wpdb->get_results($query);
	}
}

/**
 * Export analytics to CSV
 *
 * @param null
 * @return void
 */
function export_analytics_to_csv()
{
	if( !is_admin() )
		return false;

	$nonce = isset( $_GET['_wpnonce'] ) ? $_GET['_wpnonce'] : '';
	if ( !wp_verify_nonce($nonce, 'export_to_csv') )
		die('Forbidden!');

	ob_start();
	$filename = 'analytics-' . date('d.m.Y H:i:s') . '.csv';

	$header_row = array(
		'ID',
		'Title',
		'Category',
		'Date',
		'SKU',
		'Views count',
		'Wishlist count',
		'Check it out clicks'
	);

	global $wpdb;
	$prefix = $wpdb->prefix;

	$query = "SELECT * FROM {$prefix}posts WHERE post_type = 'product' AND post_status = 'publish'";

	if ( !empty($_REQUEST['orderby']) ) {
		$query .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
		$query .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
	} else {
		$query .= ' ORDER BY ID DESC';
	}

    $items = $wpdb->get_results( $query, 'ARRAY_A' );

    $data_rows = array();

	foreach ( $items as $item )
	{
		$row = array(
			$item['ID'],
			$item['post_title'],
			$item['post_title'],
			wp_get_post_terms($item['ID'], 'product_cat')[0]->name,
			$item->sku,
    		get_post_meta($item['ID'], 'analytics_product_views', true),
    		get_post_meta($item['ID'], 'analytics_product_wishlists', true),
    		get_post_meta($item['ID'], 'analytics_product_check_it_out', true)
        );
        $data_rows[] = $row;
    }

    $fh = fopen( 'php://output', 'w' );
    fprintf( $fh, chr(0xEF) . chr(0xBB) . chr(0xBF) );
    header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
    header( 'Content-Description: File Transfer' );
    header( 'Content-type: text/csv' );
    header( "Content-Disposition: attachment; filename={$filename}" );
    header( 'Expires: 0' );
    header( 'Pragma: public' );

    fputcsv( $fh, $header_row );

    foreach ( $data_rows as $data_row )
    {
        fputcsv( $fh, $data_row );
    }

    fclose( $fh );
    ob_end_flush();
    exit();
}

if ( isset($_GET['action'] ) && $_GET['action'] == 'export_to_csv' )
{
	add_action( 'admin_init', 'export_analytics_to_csv');
}
