<?php


use Phinx\Migration\AbstractMigration;

class UserTeamInvitations extends AbstractMigration
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
		$table = $this->table( 'user_invitations' );
		$table
			->addColumn( 'guid', 'string', [ 'limit' => 10, 'null' => false ] )
			->addColumn( 'email', 'string', [ 'limit' => 255, 'null' => false ] )
			->addColumn( 'timestamp', 'datetime', [ 'default' => 'CURRENT_TIMESTAMP' ] )
			->addColumn( 'has_registered', 'integer', [ 'limit' => 1, 'null' => true, 'default' => 0 ] )
			->create();
	}
}
