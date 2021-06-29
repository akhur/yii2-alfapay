Библиотека для приема платежей через интернет для Альфабанк.
===========================================================
Библиотека для приема платежей через интернет для Альфабанк.

Установка с помощью Composer
------------


```
php composer.phar require akhur/yii2-alfapay "dev-master"
```

или добавьте в composer.json

```
"akhur/yii2-alfapay": "dev-master"
```

Подключение компонента
-----

```php
[
    'components' => [
        'alfapay' => [
            'class' => 'akhur0286\alfapay\Merchant',
            'sessionTimeoutSecs' => 60 * 60 * 24 * 7,
            'merchantLogin' => '',
            'merchantPassword' => '',
            'orderModel' => '', //модель таблицы заказов
            'isTest' => false,
            'registerPreAuth' => false,
            'returnUrl' => '/payment/result-payment', //страница обработки
            'failUrl' => '/payment/error-payment', //страница ошибки
        ],
        //..
    ],
];
```

Пример работы библиотеки
-----

```
class PaymentController extends \yii\web\Controller
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'result-payment' => [
                'class' => '\akhur0286\alfapay\actions\BaseAction',
                'callback' => [$this, 'resultCallback'],
            ],
            'error-payment' => [
                'class' => '\akhur0286\alfapay\actions\BaseAction',
                'callback' => [$this, 'failCallback'],
            ],
        ];
    }

    public function resultCallback($orderId)
    {
        /* @var $model AlfapayInvoice */
        $model = AlfapayInvoice::findOne(['orderId' => $orderId]);
        if (is_null($model)) {
            throw new NotFoundHttpException();
        }

        $merchant = \Yii::$app->get('alfapay');
        $result = $merchant->checkStatus($orderId);
        //Проверяем статус оплаты если всё хорошо обновим инвойс и редерекним
        if (isset($result['OrderStatus']) && ($result['OrderStatus'] != $merchant->successStatus)) {
            //обработка при успешной оплате $model->orderNumber номер заказа в вашей системе
            echo 'ok';
        } else {
            $this->redirect($merchant->failUrl.'?orderId=' . $orderId);
        }
    }
    
    public function failCallback($orderId)
    {
        /* @var $model AlfapayInvoice */
        $model = AlfapayInvoice::findOne(['orderId' => $orderId]);
        if (is_null($model)) {
            throw new NotFoundHttpException();
        }
        //вывод страницы ошибки $model->orderNumber номер заказа в вашей системе

        echo 'error payment';
    }
}
```

