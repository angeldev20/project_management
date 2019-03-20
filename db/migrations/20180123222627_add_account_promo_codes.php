<?php


use Phinx\Migration\AbstractMigration;

class AddAccountPromoCodes extends AbstractMigration
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

        $table = $this->table('accounts_promo_codes');
        $table->addColumn('referrerAccountUrlPrefix', 'string',['limit' => 45, 'null' => true])
            ->addColumn('referrerUsername', 'string',['limit' => 45, 'null' => true])
            ->addColumn('promo_code', 'string',['limit' => 45, 'null' => false])
            ->addColumn('discount_duration', 'integer',['null' => false])
            ->addColumn('discount_percentage', 'float',['null' => false])
            ->addIndex(['referrerAccountUrlPrefix', 'referrerUsername', 'promo_code'], ['unique' => true])
            ->create();
        $table
            ->addForeignKey('referrerAccountUrlPrefix', 'accounts', 'accountUrlPrefix',['delete'=>'CASCADE', 'update'=>'CASCADE'])
            ->update();

        $table = $this->table('accounts_discounts');
        $table->addColumn('accountUrlPrefix', 'string',['limit' => 45, 'null' => false])
            ->addColumn('accounts_promo_code_id', 'integer',['null' => false])
            ->addColumn('created', 'datetime', ['default' => 'CURRENT_TIMESTAMP','null' => false])
            ->addIndex(['accountUrlPrefix'], ['unique' => true])
            ->addIndex(['accountUrlPrefix','accounts_promo_code_id', 'created'], ['unique' => true])
            ->create();
        $table
            ->addForeignKey('accountUrlPrefix', 'accounts', 'accountUrlPrefix',['delete'=>'CASCADE', 'update'=>'CASCADE'])
            ->addForeignKey('accounts_promo_code_id', 'accounts_promo_codes', 'id',['delete'=>'CASCADE', 'update'=>'CASCADE'])
            ->update();

        $rows = [
            [
                'id'    => 1,
                'promo_code' => "UT18",
                'discount_duration' => 365,
                'discount_percentage'  => 1,
            ],
        ];

        $this->insert('accounts_promo_codes', $rows);

    }
}
