Yii2 Useful Log Targets
===========================

INSTALLATION
------------

```bash
composer require mamatveev/yii2-useful-log-targets --dev
```

Add a component configuration in the application config:

```php
        'log' => [
            'targets' => [
                //sentry log target
                [
                    'class' => 'mamatveev\yii2LogTargets\SentryLogTarget',
                    'levels' => ['error', 'warning'],
                    'dsn' => 'your_sentry_dsn',
                ],
                //elasticSearch log target. connection should be configured 
                [
                    'class' => 'mamatveev\yii2LogTargets\ElasticLogTarget',
                    'levels' => ['info', 'error', 'warning'],
                    'index' => 'oplata-fssp',
                    'type' => 'oplata-fssp-applog',
                    'logVars' => ['_GET', '_POST'],
                    'except' => [
                        'yii\web\HttpException:404',
                        'yii\db\Connection::open',
                    ],
                ]
            ],
        ],
```

