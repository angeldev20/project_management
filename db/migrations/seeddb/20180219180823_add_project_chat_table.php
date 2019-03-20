<?php


use Phinx\Migration\AbstractMigration;

class AddProjectChatTable extends AbstractMigration
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
        $table = $this->table('project_chats');
        $table
            ->addColumn('project_id', 'integer',['limit' => 11, 'null' => false])
            ->addColumn('chat_message', 'string',['limit' => 400, 'null' => true])
            ->addColumn('sender_id', 'integer',['limit' => 11, 'null' => true])
            ->addColumn('sent_result', 'integer',['limit' => 4, 'null' => false, 'default' => 0]) // 0: didn't sent, 1: sent to slack,
            ->addColumn('from_external', 'integer',['limit' => 4, 'null' => false, 'default' => 0]) // 0: manual, 1: slack,
            ->addColumn('slack_id', 'string',['limit' => 45, 'null' => true])
            ->addColumn('team_id', 'string',['limit' => 45, 'null' => true])
            ->addColumn('channel_id', 'integer',['limit' => 11, 'null' => true])
            ->addColumn('created', 'datetime',['default' => 'CURRENT_TIMESTAMP'])
            ->create();

        $table = $this->table('project_chats');
        $table
            ->addForeignKey('sender_id', 'users', 'id')
            ->addForeignKey('project_id', 'projects', 'id',['delete'=>'CASCADE'])
            ->update();
    }
}
