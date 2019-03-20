<?php


use Phinx\Migration\AbstractMigration;

class CreateSlackLinksTable extends AbstractMigration
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
        $table = $this->table('slack_links');
        $table
            ->addColumn('user_id', 'integer',['limit' => 11, 'null' => false])
            ->addColumn('access_token', 'string',['limit' => 200, 'null' => false])
            ->addColumn('scope', 'string',['limit' => 400, 'null' => false])
            ->addColumn('team_name', 'string',['limit' => 45, 'null' => false])
            ->addColumn('team_id', 'string',['limit' => 45, 'null' => false])
            ->addColumn('incoming_webhook', 'string',['limit' => 450, 'null' => true])
            ->addColumn('created', 'datetime',['default' => 'CURRENT_TIMESTAMP'])
            ->create();

        $table = $this->table('slack_links');
        $table
            ->addForeignKey('user_id', 'users', 'id',['delete'=>'CASCADE'])
            ->update();
    }
}
