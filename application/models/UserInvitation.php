<?php

class UserInvitation extends ActiveRecord\Model
{
	static $table_name = 'user_invitations';

	public static function generate_guid()
	{
		$exists = true;
		$guid   = null;

		while ( $exists ) {
			$guid   = random_string( 'alnum', 5 );
			$exists = UserInvitation::find( "all", array(
				"conditions" => array(
					"guid = ?",
					$guid
				)
			) );
			$exists = ! empty( $exists );
		}

		return $guid;
	}

	public static function guid_exists( $guid )
	{
		$exists = UserInvitation::find( "all", array(
			"conditions" => array(
				"guid = ? AND has_registered = 0",
				$guid
			)
		) );

		return ! empty( $exists );
	}
}