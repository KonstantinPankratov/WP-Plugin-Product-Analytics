<?php

/**
 * Adds extra columns to user table
 *
 * @param array $column Table columns
 * @return array
 */
function new_columns_to_user_table( $column ) {
    $column['views'] = 'Product Views';
    $column['wishlist'] = 'Wishlist Items';
    $column['check_it_out'] = 'Clicks on "Check it out"';
    return $column;
}
add_filter( 'manage_users_columns', 'new_columns_to_user_table' );

/**
 * Row values
 *
 * @param array $val
 * @param string $column_name Name of the column
 * @param int $user_id User ID
 * @return int
 */
function new_rows_to_user_table( $val, $column_name, $user_id ) {
    switch ($column_name) {
        case 'views' :
            return get_user_meta($user_id, 'analytics_product_views', true);
            break;
        case 'wishlist' :
            return get_user_meta($user_id, 'analytics_product_wishlists', true);
            break;
        case 'check_it_out' :
            return get_user_meta($user_id, 'analytics_product_check_it_out', true);
            break;
        default:
    }
    return $val;
}
add_filter( 'manage_users_custom_column', 'new_rows_to_user_table', 10, 3 );