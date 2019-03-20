<?php


use Phinx\Migration\AbstractMigration;

class AddAccountPlans extends AbstractMigration
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

        $table = $this->table('plan_types');
        $table->addColumn('type', 'string',['limit' => 45, 'null' => false])
            ->addColumn('name', 'string',['limit' => 45, 'null' => false])
            ->addColumn('amount', 'integer',['signed' => false, 'null' => false])
            ->addColumn('billingFrequencyDays', 'integer',['signed' => false, 'null' => false])
            ->addIndex(['type'], ['unique' => true,])
            ->addIndex(['type', 'name', 'amount', 'billingFrequencyDays'], ['unique' => false,])
            ->create();

        $table = $this->table('account_plans');
        $table->addColumn('accountUrlPrefix', 'string',['limit' => 45, 'null' => false])
            ->addColumn('type', 'string',['limit' => 45, 'null' => false])
            ->addColumn('firstBilled', 'date',['null' => true])
            ->addColumn('lastBilled', 'date',['null' => true])
            ->addIndex(['accountUrlPrefix', 'type'], ['unique' => true,])
            ->addIndex(['accountUrlPrefix', 'type', 'firstBilled', 'lastBilled'], ['unique' => false,])
            ->addForeignKey('accountUrlPrefix', 'accounts', 'accountUrlPrefix',['delete'=>'CASCADE', 'update'=>'CASCADE'])
            ->addForeignKey('type', 'plan_types', 'type',['delete'=>'RESTRICT', 'update'=>'CASCADE'])
            ->create();
    }
}
