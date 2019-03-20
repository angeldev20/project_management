<?php


use Phinx\Migration\AbstractMigration;

class AddAccountsPaymentsTable extends AbstractMigration
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
	    $table = $this->table('accounts_payments');
	    $table
		    ->addColumn('payorAccountUrlPrefix', 'string',['limit' => 45, 'null' => false])
		    ->addColumn('payorUsername', 'string',['limit' => 45, 'null' => false])
		    ->addColumn('payeeAccountUrlPrefix', 'string',['limit' => 45, 'null' => false])
		    ->addColumn('payeeUsername', 'string',['limit' => 45, 'null' => false])
		    ->addColumn('invoiceNumber', 'string',['limit' => 20, 'null' => true])
		    ->addColumn('HostedTransactionIdentifier', 'string',['limit' => 100, 'null' => false])
		    ->addColumn('GrossAmt', 'biginteger',['signed' => false, 'null' => false, 'default' => 0])
		    ->addColumn('NetAmt', 'biginteger',['signed' => false, 'null' => false, 'default' => 0])
		    ->addColumn('TransactionId', 'string',['limit' => 40, 'null' => true])
		    ->addColumn('PaymentMethodID', 'string',['limit' => 50, 'null' => true])
		    ->addColumn('created', 'date',['null' => false])
		    ->addIndex(['payorAccountUrlPrefix', 'payorUsername', 'payeeAccountUrlPrefix', 'payeeUsername', 'invoiceNumber', 'HostedTransactionIdentifier', 'TransactionId', 'PaymentMethodID', 'created'], ['unique' => false])
		    ->create();
	    $table = $this->table('accounts_payments');
	    $table
		    ->addForeignKey('payorAccountUrlPrefix', 'accounts', 'accountUrlPrefix',['delete'=>'RESTRICT', 'update'=>'CASCADE'])
		    ->update();

    }
}
