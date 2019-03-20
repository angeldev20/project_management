<?php


use Phinx\Migration\AbstractMigration;

class AddSlackUserInfo extends AbstractMigration
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
            ->addColumn('team_url', 'string',['limit' => 450, 'null' => true])
            ->addColumn('slack_user', 'string',['limit' => 45, 'null' => true])
            ->addColumn('slack_id', 'string',['limit' => 45, 'null' => true])
            ->addIndex(['slack_id', 'team_id'], ['unique' => true])
            ->update();
    }
}
