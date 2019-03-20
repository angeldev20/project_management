<?php


use Phinx\Migration\AbstractMigration;

class AddGlobalCurrenciesTable extends AbstractMigration
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

        $table = $this->table('currencies');
        $table->addColumn('symbol', 'string',['limit' => 25, 'null' => true])
            ->addColumn('name', 'string',['limit' => 45, 'null' => false])
            ->addColumn('code', 'string',['limit' => 3, 'null' => false])
            ->addColumn('icon', 'string',['limit' => 25, 'null' => true])
            ->addColumn('url', 'string',['limit' => 80, 'null' => true])
            ->addColumn('active', 'integer',['limit' => 1, 'null' => false, 'default' => 1])
            ->addIndex(['code'], ['unique' => true,])
            ->addIndex(['code','name','symbol','active'], ['unique' => false,])
            ->create();

        $rows = [
            [
                'id'    => 1,
                'symbol' => '$',
                'name'  => 'US Dollars',
                'code' => 'USD',
            ]
        ];

        // this is a handy shortcut
        $this->insert('currencies', $rows);

    }

    public function down() {
        $response = $this->execute('DROP TABLE currencies');
    }
}
