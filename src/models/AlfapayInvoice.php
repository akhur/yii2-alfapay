<?php

namespace akhur\alfapay\models;

use pantera\yii2\pay\sberbank\Module;
use Yii;
use yii\base\InvalidParamException;
use yii\db\ActiveRecord;
use yii\helpers\Json;
use function call_user_func;
use function is_array;

/**
 * This is the model class for table "alfapay_invoice".
 *
 * @property int $id
 * @property string $related_id
 * @property string $related_model
 * @property string $orderId
 * @property int $created_at
 * @property int $paid_at
 * @property array|string $data
 * @property string $url
 */
class AlfapayInvoice extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'alfapay_invoice';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'paid_at'], 'integer'],
            [['related_id', 'related_model'], 'required'],
            [['related_id', 'orderId'], 'string'],
            [['data'], 'safe'],
            [['url'], 'string', 'max' => 255],
        ];
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_at = time();
        }

        if (is_array($this->data) === false) {
            $this->data = [];
        }
        $this->data = Json::encode($this->data);
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        $this->data = Json::decode($this->data);
        parent::afterSave($insert, $changedAttributes);
    }

    public function afterFind()
    {
        if ($this->data) {
            $this->data = Json::decode($this->data);
        }
        parent::afterFind();
    }

    /**
     * Добавление оплаты через сбербанк
     * @param integer|null $relatedID Идентификатор заказа
     * @param string|null $suffix Суффикс у номера заказа
     * @param string|null $relatedModel Название модели
     * @param int|null $orderID
     * @param string|null $url
     * @param array $data Массив дополнительные данных
     * @return self
     */
    public static function addAlfabank($relatedID, $suffix, $relatedModel, $orderID, $url, $data = [])
    {
        $model = new self();
        $model->related_id = $relatedID . '-' . $suffix;
        $model->related_model = $relatedModel;
        $model->orderId = $orderID;
        $model->url = $url;
        $model->data = $data;
        $model->save();
        return $model;
    }

    /**
     * @param $relatedID
     * @param $suffix
     * @param $relatedModel
     * @return null
     */
    public static function getOrderID($relatedID, $suffix, $relatedModel)
    {
        $model = self::findOne(['related_id' => $relatedID . '-' . $suffix, 'related_model' => $relatedModel]);
        if ($model) {
            return $model->orderId;
        }

        return null;
    }

    /**
     * Возвращаем orderNumber удалив название модели с конца
     * @return string
     */
    public function getOrderNumber()
    {
        $parts = explode('-', $this->related_id);
        array_pop($parts);

        return implode('-', $parts);
    }
}
