<?php

class TaskHasSubtask extends ActiveRecord\Model
{
	static $table_name = 'task_has_subtasks';

	static $belongs_to = array(
		array( 'project_has_task', 'foreign_key' => 'task_id' ),
		array( 'user', 'foreign_key' => 'worker_id' )
	);
}