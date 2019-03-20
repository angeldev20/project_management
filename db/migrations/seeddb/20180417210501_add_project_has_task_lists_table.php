<?php


use Phinx\Migration\AbstractMigration;

class AddProjectHasTaskListsTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
	    $table = $this->table( 'project_has_task_lists' );
	    $table->addColumn('project_id', 'integer',['limit' => 11, 'null' => false])
	          ->addColumn('name','string', [ 'limit' => 80, 'null' => true ] )
	          ->create();
	    $table = $this->table( 'project_has_list_tasks' );
	    $table->addColumn('project_id', 'integer',['limit' => 11, 'null' => false])
	          ->addColumn('task_id', 'integer',['limit' => 10, 'null' => false])
		    ->addColumn( 'task_list_id', 'integer',['limit' => 10, 'null' => false])
		    ->create();
    }
}
