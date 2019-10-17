<?php
/**
 * Adds a box to the main column on the Post and Page edit screens.
 */
function tc_ocw_carousel_add_meta_box() {

		add_meta_box(
			'ocw_carousel_sectionid',
			__( "Add Link/URL To Image", 'ocw_carousel' ),
			'tc_ocw_carousel_meta_box_callback',
			'ocw_carousel' // name of the post type

		);

}
add_action( 'add_meta_boxes', 'tc_ocw_carousel_add_meta_box' );

/**
 * Prints the box content.
 *
 * @param WP_Post $post The object for the current post/page.
 */
function tc_ocw_carousel_meta_box_callback( $post ) {

	// Add a nonce field so we can check for it later.
	// tc_ocw_carousel_meta_box_nonce will match in three places
	wp_nonce_field( 'tc_imagelink_save_meta_box_data', 'tc_ocw_carousel_meta_box_nonce' );

	/*
	 * Use get_post_meta() to retrieve an existing value
	 * from the database and use the value for the form.
	 */
	$value = get_post_meta( $post->ID, 'tc_imagelink', true );

	echo '<label for="tc_ocw_carousel_url_field">';
	_e( 'URL : ', 'ocw_carousel' );
	echo '</label> ';
	echo '<input type="text" id="tc_ocw_carousel_url_field" name="tc_ocw_carousel_url_field" value="' . esc_attr( $value ) . '" size="85" />';
}

/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function tc_imagelink_save_meta_box_data( $post_id ) {

	/*
	 * We need to verify this came from our screen and with proper authorization,
	 * because the save_post action can be triggered at other times.
	 */

	// Check if our nonce is set.
	if ( ! isset( $_POST['tc_ocw_carousel_meta_box_nonce'] ) ) {
		return;
	}

	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $_POST['tc_ocw_carousel_meta_box_nonce'], 'tc_imagelink_save_meta_box_data' ) ) {
		return;
	}

	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Check the user's permissions.
	if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}

	} else {

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}

	/* OK, it's safe for us to save the data now. */

	// Make sure that it is set.
	if ( ! isset( $_POST['tc_ocw_carousel_url_field'] ) ) {
		return;
	}

	// Sanitize user input.
	$my_data = sanitize_text_field( $_POST['tc_ocw_carousel_url_field'] );

	// Update the meta field in the database.
	update_post_meta( $post_id, 'tc_imagelink', $my_data );
}
add_action( 'save_post', 'tc_imagelink_save_meta_box_data' );
