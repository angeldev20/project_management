<?php

class ProjectUserTimeTracking extends ActiveRecord\Model
{
	static $table_name = 'projects_users_time_tracking';
	static $belongs_to = array(
		array( 'project' ),
		array( 'user' )
	);

	public static function isTracking( $project_id, $user_id )
	{
		$tracking = ProjectUserTimeTracking::find( "all", array(
			"conditions" => array(
				"user_id = ? and project_id = ? and time_spent = 0",
				$user_id,
				$project_id
			)
		) );

		return ! empty( $tracking );
	}

	public static function getTotalTimeSpent( $project_id, $user_id )
	{
		$time_spent = 0;
		$tracking   = ProjectUserTimeTracking::find( "all", array(
			"conditions" => array(
				"user_id = ? and project_id = ?",
				$user_id,
				$project_id
			)
		) );
		foreach ( $tracking as $record ) {
			$t = $record->time_spent;
			if ( ! $t ) {
				$t = time() - $record->time_start;
			}
			$time_spent += $t;
		}

		return $time_spent;
	}
}
