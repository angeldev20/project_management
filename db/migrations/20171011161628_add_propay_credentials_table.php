<?php


use Phinx\Migration\AbstractMigration;

class AddPropayCredentialsTable extends AbstractMigration
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
        /**
         *
        biginteger
        binary
        boolean
        date
        datetime
        decimal
        float
        integer
        string
        text
        time
        timestamp
        uuid
         *
         * define("PROTECT_PAY_API_BASE_URL", "https://api.propay.com/ProtectPay");
        define("PROTECT_PAY_HOSTED_TRANSACTION_BASE_URL", "https://protectpay.propay.com");
        define("PROTECT_PAY_BILLER_ID", "2022435248186914");
        define("PROTECT_PAY_AUTH_TOKEN", "44a47c68-2ede-41f1-b6a5-e522e809d52d");
        define("PROTECT_PAY_MERCHANT_PROFILE_ID", "715035");
        define("PROTECT_PAY_COMMISSION_DISBURSEMENT_CREDENTIAL","937783CCE9F149429A51B7B488E3A0");
        define("PROTECT_PAY_PAYER_ACCOUNT_ID","5641654971686317");

         */

        $table = $this->table('account_propay');
        $table->addColumn('accountUrlPrefix', 'string',['limit' => 45, 'null' => false])
            ->addColumn('protect_pay_api_base_url','text',['limit' => 1024,'null' => true])
            ->addColumn('protect_pay_hosted_transaction_base_url','text',['limit' => 1024,'null' => true])
            ->addColumn('protect_pay_biller_id','text',['limit' => 1024,'null' => true])
            ->addColumn('protect_pay_auth_token','text',['limit' => 1024,'null' => true])
            ->addColumn('protect_pay_merchant_profile_id','text',['limit' => 1024,'null' => true])
            ->addColumn('protect_pay_commission_disbursement_credential','text',['limit' => 1024,'null' => true])
            ->addColumn('protect_pay_payer_account_id','text',['limit' => 1024,'null' => true])
            //TODO: the following custom fields will have thier names changed or be eliminated when we have everything we need, providing for now.
            ->addColumn('custom1','text',['limit' => 1024,'null' => true])
            ->addColumn('custom2','text',['limit' => 1024,'null' => true])
            ->addColumn('custom3','text',['limit' => 1024,'null' => true])
            ->addColumn('custom4','text',['limit' => 1024,'null' => true])
            ->addColumn('custom5','text',['limit' => 1024,'null' => true])
            ->addColumn('custom6','text',['limit' => 1024,'null' => true])
            ->addColumn('custom7','text',['limit' => 1024,'null' => true])
            ->addColumn('custom8','text',['limit' => 1024,'null' => true])
            ->addColumn('custom9','text',['limit' => 1024,'null' => true])
            ->addIndex(['accountUrlPrefix'], ['unique' => true,])
            ->addForeignKey('accountUrlPrefix', 'accounts', 'accountUrlPrefix',['delete'=>'CASCADE', 'update'=>'CASCADE'])
            ->create();

        $table = $this->table('users_propay');
        $table->addColumn('username', 'string',['limit' => 45, 'null' => false])
            ->addColumn('protect_pay_api_base_url','text',['limit' => 1024,'null' => true])
            ->addColumn('protect_pay_hosted_transaction_base_url','text',['limit' => 1024,'null' => true])
            ->addColumn('protect_pay_biller_id','text',['limit' => 1024,'null' => true])
            ->addColumn('protect_pay_auth_token','text',['limit' => 1024,'null' => true])
            ->addColumn('protect_pay_merchant_profile_id','text',['limit' => 1024,'null' => true])
            ->addColumn('protect_pay_commission_disbursement_credential','text',['limit' => 1024,'null' => true])
            ->addColumn('protect_pay_payer_account_id','text',['limit' => 1024,'null' => true])
            //TODO: the following custom fields will have thier names changed or be eliminated when we have everything we need, providing for now.
            ->addColumn('custom1','text',['limit' => 1024,'null' => true])
            ->addColumn('custom2','text',['limit' => 1024,'null' => true])
            ->addColumn('custom3','text',['limit' => 1024,'null' => true])
            ->addColumn('custom4','text',['limit' => 1024,'null' => true])
            ->addColumn('custom5','text',['limit' => 1024,'null' => true])
            ->addColumn('custom6','text',['limit' => 1024,'null' => true])
            ->addColumn('custom7','text',['limit' => 1024,'null' => true])
            ->addColumn('custom8','text',['limit' => 1024,'null' => true])
            ->addColumn('custom9','text',['limit' => 1024,'null' => true])
            ->addIndex(['username'], ['unique' => true,])
            ->create();
    }
}
