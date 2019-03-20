<?php


use Phinx\Migration\AbstractMigration;

class AddStartAndEndPlansToAccountPayments extends AbstractMigration
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
	    $table->addColumn('updated', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
		    ->addColumn('starting_plan', 'string',['limit' => 45, 'null' => true])
		    ->addColumn('ending_plan', 'string',['limit' => 45, 'null' => true])
		    ->addColumn('plan_change_status', 'integer',['limit' => 3, 'null' => false, 'default' => 0])
		    ->addColumn('user_count', 'integer',['null' => false, 'default' => 0])
		    ->addColumn('change_reason', 'text',['null' => true])
		    ->addForeignKey('starting_plan', 'plan_types', 'type',['delete'=>'RESTRICT', 'update'=>'CASCADE'])
		    ->addForeignKey('ending_plan', 'plan_types', 'type',['delete'=>'RESTRICT', 'update'=>'CASCADE'])
		    ->update();
	    $this->execute("UPDATE account_plans set type='hustle_monthly' WHERE type='solo_monthly';");
	    $this->execute("UPDATE account_plans set type='hustle_annual' WHERE type='solo_annual';");
	    $this->execute("UPDATE account_plans set type='pro_monthly' WHERE type='elite_monthly';");
	    $this->execute("UPDATE account_plans set type='pro_annual' WHERE type='elite_annual';");

    }
}
