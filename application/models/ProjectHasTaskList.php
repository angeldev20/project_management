<?php
/**
 * Created by PhpStorm.
 * User: damon
 * Date: 4/17/18
 * Time: 3:39 PM
 */
class ProjectHasTaskList extends ActiveRecord\Model {
	static $table_name = 'project_has_task_lists';

	static $belongs_to = array(
		array( 'projects'),
	);
}