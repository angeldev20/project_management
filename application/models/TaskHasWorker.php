<?php

class TaskHasWorker extends ActiveRecord\Model
{
	static $table_name = 'task_has_workers';

	static $belongs_to = array(
		array( 'user', 'foreign_key' => 'worker_id' ),
		'project_has_task'
	);
}
