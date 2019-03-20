<?php if ( ! defined( 'BASEPATH' ) ) {
	exit( 'No direct script access allowed' );
}

/**
 * @param $content
 * @param $user_id
 *
 * @return bool|array
 * @internal param $mentions
 *
 */
function has_mentioned( $content, $user_id = false )
{
	$users = User::find( 'all', array( 'conditions' => array( 'status=?', 'active' ) ) );

	$mentioned_users = [];
	foreach ( $users as $user ) {
		$has_mentioned = strpos( $content, '@' . $user->username );

		if($has_mentioned === 0)
			$has_mentioned = true;

		if ( $has_mentioned ) {
            $mentioned_users[] = $user;
		}
	}

	return (count($mentioned_users) == 0 ) ? false : $mentioned_users;
}

/**
 * @param $guid
 *
 * @param bool $prefix
 *
 * @return bool
 */
function has_guid_registered( $guid, $prefix = false )
{
	return Mention::exists( array( 'conditions' => array( 'guid=?', $guid ) ) );
}

/**
 * @param bool $prefix
 *
 * @return mixed|string
 */
function generate_mention_guid( $prefix = false )
{
	$length           = 10;
	$characters       = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen( $characters );
	$randomString     = '';

	for ( $i = 0; $i < $length; $i ++ ) {
		$randomString .= $characters[ rand( 0, $charactersLength - 1 ) ];
	}

	return has_guid_registered( $randomString, $prefix ) ? generate_mention_guid( $prefix ) : $randomString;
}