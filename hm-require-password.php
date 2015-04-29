<?php
/**
 * A plugin to require current user password
 * when a user updates their profile.
 */

add_action( 'show_user_profile', 'hm_require_password_current_pw_fields' );
add_action( 'edit_user_profile', 'hm_require_password_current_pw_fields' );

/**
 * Display current password field
 * @param $user
 */
function hm_require_password_current_pw_fields( $user ) {

	$label = __( 'Enter your password to update this account.', 'hm-require-password' );
	$description = sprintf( __( 'if you would like to set a new password for %s, please enter your password here. Otherwise leave this blank.', 'hm-require-password' ), '<strong>' . $user->first_name . '</strong>' );

	if( $user->ID == get_current_user_id() ) {

		$label = __( 'Current Password', 'hm-require-password' );
		$description = __( 'If you would like to set a new password, type your current one here. Otherwise leave this blank.', 'hm-require-password' );

	}

	?>
		<table class="form-table">
			<tbody>
				<tr id="current-password" class="user-description-wrap">
					<th scope="row"><label for="current-pass"><?php echo esc_html( $label ); ?></label></th>
					<td>
						<input type="password" name="current_pass" id="current_pass" class="regular-text" size="16" value="" autocomplete="off" />
						<p class="description"><?php echo esc_html( $description ); ?></p>
					</td>
				</tr>
			</tbody>
		</table>

	<?php

}

add_action ( 'user_profile_update_errors', 'hm_require_password_check_current_pw_fields', 1, 3 );

/**
 * Check if logged in user's current password has been entered and is correct
 *
 * @param $user_id
 *
 * @return bool
 */
function hm_require_password_check_current_pw_fields( $errors, $update, $user ) {

	if( ! isset( $_POST[ 'pass1' ]) || empty( $_POST[ 'pass1' ] ) || ! $update ){

		return;

	}

	if( ! isset( $_POST[ 'current_pass' ] ) || empty ( $_POST['current_pass'] ) ) {

		$errors->add( 'wrong_current_password', __( '<strong>ERROR</strong>: Please enter your current password.' ), array( 'form-field' => 'current_pass' ) );

	} elseif ( ! empty( $_POST[ 'pass1' ] ) ) {

		$logged_user = get_user_by( 'id', get_current_user_id() );

		if ( ! wp_check_password( $_POST['current_pass'], $logged_user->user_pass, $logged_user->ID ) ) {
			$errors->add( 'wrong_current_password', __( '<strong>ERROR</strong>: The current password you gave is incorrect.' ), array( 'form-field' => 'current_pass' ) );
		}

	}

	return;

}
