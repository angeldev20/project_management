<?php
/**
 * Created by PhpStorm.
 * User: damon
 * Date: 11/28/17
 * Time: 4:50 PM
 */

class projecttasks
{

    /** @var  string */
    private $_databaseName;

    /** @var  CI_DB_mysql_driver */
    private $_primaryDatabase;

    /**
     * projecttasks constructor.
     * @param null|array $init_array
     */
    public function __construct($init_array = NULL)
    {
        if (!is_null($init_array)) {
            if (isset($init_array['databaseName'])) $this->_databaseName = $init_array['databaseName'];
            if (isset($init_array['primaryDatabase'])) $this->_primaryDatabase = $init_array['primaryDatabase'];
        }
    }

    /**
     * @param string $databaseName
     * @return $this
     */
    public function setDatabaseName($databaseName) {
        $this->_databaseName = $databaseName;
        return $this;
    }

    /**
     * @param CI_DB_mysql_driver $primaryDatabase
     * @return $this
     */
    public function setPrimaryDatabase(CI_DB_mysql_driver $primaryDatabase) {
        $this->_primaryDatabase = $primaryDatabase;
        return $this;
    }

    public function getProjects() {
        $sql = "SELECT 
                        p.id as id,
                        p.name as ProjectName
                FROM projects AS p;";
        /** @var  CI_DB_mysql_result $result */
        $result = $this->_primaryDatabase->query($sql, []);
        $resultArray = $result->result();
        return $resultArray;
    }

    /**
     * Get a list of joined projects, task and users names with start and due dates
     * @return bool|mixed
     */
    public function getProjectTasks() {
        $fieldNames = [];
        $sql = "SELECT 
                        p.name as ProjectName,
                        pt.name as TaskName, 
                        u.username, 
                        pt.`start_date`,
                        pt.`due_date`,
                        pt.status 
                FROM projects AS p 
                LEFT JOIN project_has_tasks as pt on pt.project_id = p.id 
                LEFT JOIN users as u on pt.user_id=u.id;";
        /** @var  CI_DB_mysql_result $result */
        $result = $this->_primaryDatabase->query($sql, []);
        $resultArray = $result->result();
        if ($result->num_rows() > 0) {
            foreach ((array) $resultArray[0] as $fieldName => $fieldValue) $fieldNames[] = $fieldName;
            $resultArray = array_merge([(object) $fieldNames], $resultArray);
        }

        return $resultArray;
    }

    /**
     * Returns the task list but only for projects whose id is listed
     * @param $projectIdList
     * @return array|mixed
     */
    public function getProjectTasksForSelectProjects($projectIdList) {
        if (count($projectIdList) < 1) return [];
        $fieldNames = [];
        $sql = "SELECT 
                        p.category as category,  
                        p.name as ProjectName,
                        pt.name as TaskName, 
                        u.username, 
                        pt.`start_date`,
                        pt.`due_date`,
                        pt.status 
                FROM projects AS p 
                LEFT JOIN project_has_tasks as pt on pt.project_id = p.id 
                LEFT JOIN users as u on pt.user_id=u.id
                WHERE p.id IN (" . implode(",", $projectIdList) . ")
                ORDER BY p.category, p.name;";
        /** @var  CI_DB_mysql_result $result */
        $result = $this->_primaryDatabase->query($sql, []);
        $resultArray = $result->result();
        if ($result->num_rows() > 0) {
            foreach ((array) $resultArray[0] as $fieldName => $fieldValue) $fieldNames[] = $fieldName;
            $resultArray = array_merge([(object) $fieldNames], $resultArray);
        }
        return $resultArray;
    }

    /**
     * @param array $dbResult
     * @return string
     */
    public function formatCsv(array $dbResult) {
        $csv='';
        if (is_array($dbResult)) {
            foreach ($dbResult as $resultItem) {
                $csv .= implode(',', (array) $resultItem) . "\r\n";
            }
        }
        return $csv;
    }

    /**
     * @param array $dbResult
     * @return string
     */
    public function formatTsv(array $dbResult) {
        $tsv='';
        if (is_array($dbResult)) {
            foreach ($dbResult as $resultItem) {
                $tsv .= implode("\t", (array) $resultItem) . "\r\n";
            }
        }
        return $tsv;
    }

}
//select p.name as ProjectName,pt.name as TaskName, u.username, pt.`start_date`,pt.`due_date` from projects as p LEFT JOIN project_has_tasks as pt on pt.project_id = p.id LEFT JOIN users as u on pt.user_id=u.id;
