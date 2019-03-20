<?php


use Phinx\Migration\AbstractMigration;

class AddCryptoPayments extends AbstractMigration
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
	    /**
	     * status 0=unpaid, 1=paid, 2=fulfilled
	     */
	    $table = $this->table('accounts_crypto_payments');
	    $table
		    ->addColumn('source_type', 'string',['limit' => 20, 'null' => true])
		    ->addColumn('aup_transaction_id', 'integer',['null' => true])
		    ->addColumn('accountUrlPrefix', 'string',['limit' => 17, 'null' => false])
		    ->addColumn('username', 'string',['limit' => 30, 'null' => true])
		    ->addColumn('invoice_id', 'integer',['null' => true])
		    ->addColumn('destination_currency', 'string',['limit' => 3, 'null' => false])
		    ->addColumn('currency_amount', 'string',['limit' => '23','null' => true, 'default' => '0.00000000'])
		    ->addColumn('status', 'integer',['null' => 'false', 'default' => 0])
		    ->addColumn('created', 'datetime',['default' => 'CURRENT_TIMESTAMP'])
		    ->addColumn('updated', 'datetime',['default' => 'CURRENT_TIMESTAMP'])

		    ->addIndex(['source_type', 'aup_transaction_id', 'accountUrlPrefix', 'username', 'invoice_id', 'destination_currency', 'status', 'created', 'updated'], ['unique' => false])
		    ->addIndex(['aup_transaction_id', 'accountUrlPrefix', 'invoice_id'], ['unique' => false])
		    ->create();
	    $table = $this->table('accounts_crypto_payments');
	    $table
		    ->addForeignKey('accountUrlPrefix', 'accounts', 'accountUrlPrefix',['delete'=>'CASCADE', 'update'=>'CASCADE'])
		    ->addForeignKey('aup_transaction_id', 'accounts_users_propay_transactions', 'id',['delete'=>'CASCADE', 'update'=>'CASCADE'])
		    ->update();
    }
}
