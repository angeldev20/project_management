<?php


use Phinx\Migration\AbstractMigration;

class AddPricingToPlanTypes extends AbstractMigration
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
    //public function change()
    //{
    //
    //}

    /**
     * Migrate Up.
     */
    public function up()
    {
        $rows = [
            [
                'id'    => 1,
                'type' => 'plan_one',
                'name'  => 'Plan One',
                'amount' => 3995,
                'billingFrequencyDays' => 30
            ],
            [
                'id'    => 2,
                'type' => 'plan_two',
                'name'  => 'Plan Two',
                'amount' => 7995,
                'billingFrequencyDays' => 90
            ],
            [
                'id'    => 3,
                'type' => 'plan_three',
                'name'  => 'Plan Three',
                'amount' => 9995,
                'billingFrequencyDays' => 365

            ]
        ];

        // this is a handy shortcut
        $this->insert('plan_types', $rows);
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute('TRUNCATE TABLE plan_types;');
    }

}
