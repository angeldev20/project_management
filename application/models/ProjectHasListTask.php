<?php
/**
 * Created by PhpStorm.
 * User: damon
 * Date: 4/17/18
 * Time: 3:43 PM
 */
class ProjectHasListTask extends ActiveRecord\Model {
	static $table_name = 'project_has_list_tasks';

	static $belongs_to = array(
		array( 'project' ),
		array( 'project_has_task_list' ),
	);
}