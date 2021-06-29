<?php

use yii\db\Migration;

/**
 * Class m210615_063719_alfapay_invoice
 */
class m210615_063719_alfapay_invoice extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('alfapay_invoice', [
            'id' => $this->primaryKey(),
            'related_id' => $this->string()->null(),
            'related_model' => $this->string()->null(),
            'created_at' => $this->integer(),
            'paid_at' => $this->integer(),
            'data' => $this->text(),
            'url' => $this->text(),
            'orderId' => $this->string()->null(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('alfapay_invoice');
    }
}
