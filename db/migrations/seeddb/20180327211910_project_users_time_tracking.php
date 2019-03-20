<?php


use Phinx\Migration\AbstractMigration;

class ProjectUsersTimeTracking extends AbstractMigration
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
		$table = $this->table( 'projects_users_time_tracking' );
		$table
			->addColumn( 'project_id', 'integer', [ 'limit' => 11, 'null' => false ] )
			->addColumn( 'user_id', 'integer', [ 'limit' => 11, 'null' => false ] )
			->addColumn( 'time_start', 'datetime', [ 'null' => false ] )
			->addColumn( 'time_end', 'datetime', [ 'null' => true ] )
			->addColumn( 'time_spent', 'integer', [ 'limit' => 11, 'null' => true ] )
			->create();

		$table = $this->table( 'projects_users_time_tracking' );
		$table
			->addForeignKey( 'user_id', 'users', 'id' )
			->addForeignKey( 'project_id', 'projects', 'id', [ 'delete' => 'CASCADE' ] )
			->update();
	}
}
