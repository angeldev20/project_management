<?php


use Phinx\Migration\AbstractMigration;

class CreateSlackChannelsTable extends AbstractMigration
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
        $table = $this->table('slack_linked_channels');
        $table
            ->addColumn('project_id', 'integer',['limit' => 11, 'null' => false])
            ->addColumn('user_id', 'integer',['limit' => 11, 'null' => false])
            ->addColumn('slack_link_id', 'integer',['limit' => 11, 'null' => false])
            ->addColumn('connection_flag', 'integer',['limit' => 1, 'default' => '0'])
            ->addColumn('channel_name', 'string',['limit' => 200, 'null' => false])
            ->addColumn('created', 'datetime',['default' => 'CURRENT_TIMESTAMP'])
            ->addIndex(['project_id', 'user_id'], ['unique' => true])
            ->create();

        $table = $this->table('slack_linked_channels');
        $table
            ->addForeignKey('user_id', 'users', 'id',['delete'=>'CASCADE'])
            ->addForeignKey('project_id', 'projects', 'id',['delete'=>'CASCADE'])
            ->addForeignKey('slack_link_id', 'slack_links', 'id',['delete'=>'CASCADE'])
            ->update();
    }
}
