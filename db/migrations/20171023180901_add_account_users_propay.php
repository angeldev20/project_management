<?php


use Phinx\Migration\AbstractMigration;

class AddAccountUsersPropay extends AbstractMigration
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

        //return fields from signup
        //{"AccountNumber":32299999,"Password":"$#GD!ADXv2","SourceEmail":"someuser@somedomain.com","Status":"00","Tier":"Platinum"}

        $table = $this->table('accounts_users_propay');
        $table->addColumn('accountUrlPrefix', 'string',['limit' => 45, 'null' => false])
            ->addColumn('username', 'string',['limit' => 45, 'null' => false])
            ->addColumn('AccountNumber', 'string',['limit' => 21, 'null' => true])
            ->addColumn('Status', 'string',['limit' => 2, 'null' => true])
            ->addColumn('Tier', 'string',['limit' => 40, 'null' => true])
            ->addIndex(['accountUrlPrefix', 'username'], ['unique' => true,])
            ->addForeignKey('accountUrlPrefix', 'accounts', 'accountUrlPrefix',['delete'=>'RESTRICT', 'update'=>'CASCADE'])
            ->create();
    }
}
