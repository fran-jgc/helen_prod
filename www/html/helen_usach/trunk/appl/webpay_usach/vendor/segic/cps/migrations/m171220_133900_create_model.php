<?php

use yii\db\Migration;

/**
 * Class m171220_133900_create_model
 */
class m171220_133900_create_model extends Migration {

    /**
     * @inheritdoc
     */
    public function safeUp() {
        $this->execute("
            CREATE TABLE IF NOT EXISTS `pagosweb` (
                `num_trans` int(50) NOT NULL,
                `monto` int(11) DEFAULT NULL,
                `fec_trans` datetime DEFAULT NULL,
                `est_pago` char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
                `user_id` int(11) DEFAULT NULL,
                `ip_cliente` varchar(25) COLLATE utf8_spanish2_ci DEFAULT NULL
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
            
            ALTER TABLE `pagosweb` ADD UNIQUE(`num_trans`);
        ");
    }

    /**
     * @inheritdoc
     */
    public function safeDown() {
        echo "m171220_133900_create_model cannot be reverted.\n";

        return false;
    }

}
