<?php

use yii\db\Migration;

/**
 * Class m210615_063719_alfapay_invoice
 */
class m210706_104323_indexes_alfapay extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createIndex('idx_object', 'alfapay_invoice', ['related_id', 'related_model']);
        $this->createIndex('idx_orderid', 'alfapay_invoice', ['orderId']);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->createIndex('idx_object', 'alfapay_invoice');
        $this->createIndex('idx_orderid', 'alfapay_invoice');
    }
}
