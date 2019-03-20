<?php


use Phinx\Migration\AbstractMigration;

class AddCryptoTables extends AbstractMigration
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
        $this->execute("CREATE TABLE `api_key_security` (
            `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
            `encrypted_api_key` VARCHAR(255) NULL DEFAULT NULL,
            `nonce` BIGINT(20) NULL DEFAULT NULL,
            PRIMARY KEY (`id`)
        )
        COLLATE='utf8mb4_general_ci'
        ENGINE=InnoDB;
        ");
        $this->execute("CREATE TABLE `crypto_account` (
            `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
            `amount` DECIMAL(38,19) NULL DEFAULT NULL,
            `asset` VARCHAR(255) NULL DEFAULT NULL,
            `created_date` DATETIME NULL DEFAULT NULL,
            `crypto_exchange_account_id` VARCHAR(255) NULL DEFAULT NULL,
            `crypto_exchange_name` VARCHAR(255) NULL DEFAULT NULL,
            `modified_date` DATETIME NULL DEFAULT NULL,
            `user_id` VARCHAR(255) NULL DEFAULT NULL,
            PRIMARY KEY (`id`)
        )
        COLLATE='utf8mb4_general_ci'
        ENGINE=InnoDB;
        ");
        $this->execute("CREATE TABLE `transaction` (
            `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
            `asset` VARCHAR(255) NULL DEFAULT NULL,
            `asset_amount` DECIMAL(38,19) NULL DEFAULT NULL,
            `client_id` VARCHAR(255) NULL DEFAULT NULL,
            `client_transaction_id` VARCHAR(255) NULL DEFAULT NULL,
            `conversion_rate` DECIMAL(38,19) NULL DEFAULT NULL,
            `crypto_asset_exchange_account_id` VARCHAR(255) NULL DEFAULT NULL,
            `crypto_blockchain_transaction_id` VARCHAR(255) NULL DEFAULT NULL,
            `crypto_blockchain_transaction_url` VARCHAR(255) NULL DEFAULT NULL,
            `crypto_currency_exchange_account_id` VARCHAR(255) NULL DEFAULT NULL,
            `crypto_deposit_address` VARCHAR(255) NULL DEFAULT NULL,
            `crypto_exchange_name` VARCHAR(255) NULL DEFAULT NULL,
            `currency` VARCHAR(255) NULL DEFAULT NULL,
            `currency_amount` DECIMAL(38,19) NULL DEFAULT NULL,
            `currency_exchange_transfer_fee_amount` DECIMAL(38,19) NULL DEFAULT NULL,
            `currency_spera_transfer_fee_amount` DECIMAL(38,19) NULL DEFAULT NULL,
            `transaction_date` DATETIME NULL DEFAULT NULL,
            `transaction_type` VARCHAR(255) NULL DEFAULT NULL,
            `user_id` VARCHAR(255) NULL DEFAULT NULL,
	        PRIMARY KEY (`id`)
        )
        COLLATE='utf8mb4_general_ci'
        ENGINE=InnoDB;
        ");
        $this->execute("CREATE TABLE `user2factor_authentication` (
            `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
            `created_date` DATETIME NULL DEFAULT NULL,
            `secret` VARCHAR(255) NULL DEFAULT NULL,
            `user_id` VARCHAR(255) NULL DEFAULT NULL,
            PRIMARY KEY (`id`)
        )
        COLLATE='utf8mb4_general_ci'
        ENGINE=InnoDB;
        ");
    }

    public function down()
    {
        $this->execute("DROP DATABASE api_key_security;");
        $this->execute("DROP DATABASE crypto_account;");
        $this->execute("DROP DATABASE transaction;");
        $this->execute("DROP DATABASE user2factor_authentication;");
    }
}
