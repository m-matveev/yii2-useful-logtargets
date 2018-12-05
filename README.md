Yii2 Sentry Log Target
===========================

INSTALLATION
------------

```bash
composer require mamatveev/yii2-sentry-logtarget --dev
```

Add a component configuration in the application config:

```php
        'log' => [
            'targets' => [
                [
                    'class' => 'common\services\log\SentryLogTarget',
                    'levels' => ['error', 'warning'],
                    'dsn' => 'your_sentry_dsn',
                ],
            ],
        ],
```