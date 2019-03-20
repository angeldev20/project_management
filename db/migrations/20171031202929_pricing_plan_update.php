<?php


use Phinx\Migration\AbstractMigration;

class PricingPlanUpdate extends AbstractMigration
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
    public function up()
    {
        $this->execute('DELETE from plan_types WHERE id<6;');
        $rows = [
            [
                'id'    => 1,
                'type' => 'hustle_monthly',
                'name'  => 'Hustle Monthly',
                'amount' => 500,
                'billingFrequencyDays' => 30
            ],
            [
                'id'    => 2,
                'type' => 'hustle_annual',
                'name'  => 'Hustle Annual',
                'amount' => 5000,
                'billingFrequencyDays' => 365
            ],
            [
                'id'    => 3,
                'type' => 'solo_monthly',
                'name'  => 'Solo Monthly',
                'amount' => 5000,
                'billingFrequencyDays' => 30
            ],
            [
                'id'    => 4,
                'type' => 'solo_annual',
                'name'  => 'Solo Annual',
                'amount' => 50000,
                'billingFrequencyDays' => 365
            ],
            [
                'id'    => 5,
                'type' => 'pro_monthly',
                'name'  => 'Pro Monthly',
                'amount' => 15000,
                'billingFrequencyDays' => 30
            ],
            [
                'id'    => 6,
                'type' => 'pro_annual',
                'name'  => 'Pro Annual',
                'amount' => 125000,
                'billingFrequencyDays' => 365
            ],
            [
                'id'    => 7,
                'type' => 'elite_monthly',
                'name'  => 'Elite Monthly',
                'amount' => 25000,
                'billingFrequencyDays' => 30
            ],
            [
                'id'    => 8,
                'type' => 'elite_annual',
                'name'  => 'Elite Annual',
                'amount' => 250000,
                'billingFrequencyDays' => 365

            ]
        ];

        $this->insert('plan_types', $rows);
    }

    public function down()
    {
        $this->execute('TRUNCATE TABLE plan_types;');
    }
}
