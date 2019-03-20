<?php

class ProjectChat extends ActiveRecord\Model {
    static $belongs_to = array(
        array('sender', 'class_name' => 'User'),
        array('project')
    );

    public static function get_categories(){
        $categories = Project::find_by_sql("SELECT 
            `category` 
            FROM 
            `projects`
            GROUP BY 
            `category`
        ");

        return $categories;
    }
}
