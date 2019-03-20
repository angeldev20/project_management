<?php


use Phinx\Migration\AbstractMigration;

class AddNewPromoCodes extends AbstractMigration
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
        $this->execute("UPDATE accounts_promo_codes set discount_duration=90  WHERE id=3;");

        $rows = [
            [
                'id'    => 5,
                'promo_code' => "SPERAUU",
                'discount_duration' => 365,
                'discount_percentage'  => 1,
            ],
            [
                'id'    => 6,
                'promo_code' => "SPERAWM",
                'discount_duration' => 365,
                'discount_percentage'  => 1,
            ],        ];

        $this->insert('accounts_promo_codes', $rows);

    }
}
