<?php


use Phinx\Migration\AbstractMigration;

class AdjustPlanTypesAgain extends AbstractMigration
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
	    $this->execute('UPDATE plan_types set amount=1195 WHERE id=1;');
	    $this->execute('UPDATE plan_types set amount=11995 WHERE id=2;');
	    $this->execute('UPDATE plan_types set amount=2395 WHERE id=5;');
	    $this->execute('UPDATE plan_types set amount=23995 WHERE id=6;');
	    $table = $this->table('plan_types');
	    $table
		    ->addColumn('parent_id', 'integer',['null' => true])
		    ->update();
	    $this->execute('UPDATE plan_types set parent_id=2 WHERE id=1;');
	    $this->execute('UPDATE plan_types set parent_id=6 WHERE id=5;');
    }
}
