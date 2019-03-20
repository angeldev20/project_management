<?php


use Phinx\Migration\AbstractMigration;

class AddIpAddressAndTimestampToPropaySignup extends AbstractMigration
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
        $table = $this->table('accounts_users_propay');
        $table
            ->addColumn('signature','integer',['null' => false])
            ->addColumn('signatureIpAddress','string',['limit' => 15,'null' => false])
            ->addColumn('signatureDateTime','datetime',['null' => false])
            ->update();

        $table = $this->table('account_plans');
        $table
            ->changeColumn('firstBilled', 'datetime',['null' => true])
            ->changeColumn('lastBilled', 'datetime',['null' => true])
            ->update();
    }
}
