<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 2.0
 */
class AC_Column_User_Description extends AC_Column {

	public function __construct() {
		$this->set_type( 'column-user_description' );
		$this->set_label( __( 'Description', 'codepress-admin-columns' ) );
	}

	public function get_raw_value( $user_id ) {
		return get_the_author_meta( 'user_description', $user_id );
	}

	public function register_settings() {
		$this->add_setting( new AC_Settings_Column_WordLimit( $this ) );
	}

}
