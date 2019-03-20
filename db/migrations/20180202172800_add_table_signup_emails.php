<?php


use Phinx\Migration\AbstractMigration;

class AddTableSignupEmails extends AbstractMigration
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
        $rows = [
            [
                'id'    => 2,
                'promo_code' => "SLCPSTU18",
                'discount_duration' => 180,
                'discount_percentage'  => 1,
            ],
            [
                'id'    => 3,
                'promo_code' => "SLCPFRL18",
                'discount_duration' => 60,
                'discount_percentage'  => 1,
            ],
            [
                'id'    => 4,
                'promo_code' => "SPRS18",
                'discount_duration' => 90,
                'discount_percentage'  => 1,
            ],
        ];

        $this->insert('accounts_promo_codes', $rows);

        $table = $this->table('accounts_signup_emails');
        $table->addColumn('email', 'string',['limit' => 45, 'null' => true])
            ->addColumn('firstname', 'string',['limit' => 25, 'null' => true])
            ->addColumn('lastname', 'string',['limit' => 25, 'null' => true])
            ->addColumn('promoCode', 'string',['limit' => 45, 'null' => true])
            ->addColumn('planType', 'string',['limit' => 45, 'null' => true])
            ->addColumn('source_ip', 'string',['limit' => 16, 'null' => true])
            ->addColumn('created', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addIndex(['email','firstname', 'lastname','promoCode','planType', 'source_ip', 'created'], ['unique' => false])
            ->create();
    }
}
