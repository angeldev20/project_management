<?php


use Phinx\Migration\AbstractMigration;

class AddPromoCodeJustynGourdin extends AbstractMigration
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
        //JSGAFL18

	    $rows = [
		    [
			    'id'    => 7,
			    'promo_code' => "JSGAFL18",
			    'referrerUsername' => 'justynjen',
			    'discount_duration' => 365,
			    'discount_percentage'  => .2,
		    ],
	    ];

	    $this->insert('accounts_promo_codes', $rows);

    }
}
