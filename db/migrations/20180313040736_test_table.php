<?php


use Phinx\Migration\AbstractMigration;

class TestTable extends AbstractMigration
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
        $table = $this->table('test_table');
        $table
            ->addColumn('team_id', 'string',['limit' => 100, 'null' => false])
            ->addColumn('channel_id', 'string',['limit' => 100, 'null' => false])
            ->addColumn('channel_name', 'string',['limit' => 100, 'null' => false])
            ->addColumn('slack_user_id', 'string',['limit' => 100, 'null' => false])
            ->addColumn('slack_user_name', 'string',['limit' => 100, 'null' => false])
            ->addColumn('platform_id', 'integer',['limit' => 11, 'null' => false])          //slack account id ( with sub-domain name)
            ->addColumn('created', 'datetime',['default' => 'CURRENT_TIMESTAMP'])

            ->addIndex(['team_id', 'slack_user_id', 'channel_id'], ['unique' => true])
            ->create();
    }
}
