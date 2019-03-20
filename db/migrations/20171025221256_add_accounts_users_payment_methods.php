<?php


use Phinx\Migration\AbstractMigration;

class AddAccountsUsersPaymentMethods extends AbstractMigration
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
        $table = $this->table('accounts_users_payment_methods');
        $table->addColumn('accountUrlPrefix', 'string',['limit' => 45, 'null' => false])
            ->addColumn('username', 'string',['limit' => 45, 'null' => false])
            ->addColumn('PaymentMethodID', 'string',['limit' => 60, 'null' => true])
            ->addColumn('PaymentMethodType', 'string',['limit' => 20, 'null' => true])
            ->addColumn('ExpirationDate', 'string',['limit' => 10, 'null' => true])
            ->addIndex(['accountUrlPrefix', 'username', 'PaymentMethodID'], ['unique' => true,])
            ->addIndex(['accountUrlPrefix', 'username', 'PaymentMethodID', 'PaymentMethodType', 'ExpirationDate'], ['unique' => false,])
            ->addForeignKey('accountUrlPrefix', 'accounts', 'accountUrlPrefix',['delete'=>'RESTRICT', 'update'=>'CASCADE'])
            ->create();
    }
}
