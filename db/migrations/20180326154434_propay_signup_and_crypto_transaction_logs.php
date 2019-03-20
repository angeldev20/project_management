<?php


use Phinx\Migration\AbstractMigration;

class PropaySignupAndCryptoTransactionLogs extends AbstractMigration
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
	    $table = $this->table('accounts_users_api_log');
	    $table->addColumn('accountUrlPrefix', 'string',['limit' => 45, 'null' => false])
	        ->addColumn('username', 'string',['limit' => 45, 'null' => false])
	  	    ->addColumn('type', 'string',['limit' => 30, 'null' => true])
		    ->addColumn('request', 'text', ['null' => true])
		    ->addColumn('response', 'text', ['null' => true])
	        ->addIndex(['accountUrlPrefix', 'username', 'type'], ['unique' => false,])
	        ->addForeignKey('accountUrlPrefix', 'accounts', 'accountUrlPrefix',['delete'=>'RESTRICT', 'update'=>'CASCADE'])
	        ->create();
    }
}
