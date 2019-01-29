<?php
namespace mamatveev\yii2SentryLogTarget;

use Raven_Client;
use Yii;
use yii\base\InvalidConfigException;
use yii\log\Logger;
use yii\log\Target;


class SentryLogTarget extends Target
{
    /**
     * @var string dsn for sentry access
     */
    public $dsn = '';

    /**
     * @var string
     */
    public $serializer = 'mamatveev\yii2SentryLogTarget\Serializer';

    /**
     * message length limit
     * @var int
     */
    public $messageLimit = 3072;

    /**
     * @var Raven_Client client for working with sentry
     */
    private $client = null;

    /**
     * Initializes the DbTarget component.
     * This method will initialize the [[db]] property to make sure it refers to a valid DB connection.
     * @throws InvalidConfigException if [[db]] is invalid.
     */
    public function init()
    {
        parent::init();
        $this->client = new SentryClient($this->dsn);
        $serializer = new $this->serializer(null, $this->messageLimit);

        if (!$serializer instanceof \Raven_Serializer) {
            throw new \Exception('serializer should be instance of Raven_Serializer');
        }

        $this->client->setSerializer($serializer);

    }

    /*
     * Processes the given log messages.
     * This method will fil\Yii::$app->params['mts_mq_manager_name']ter the given messages with [[levels]] and [[categories]].
     * And if requested, it will also export the filtering result to specific medium (e.g. email).
     * @param array $messages log messages to be processed. See [[Logger::messages]] for the structure
     * of each message.
     * @param boolean $final whether this method is called at the end of the current application
     */
    public function collect($messages, $final)
    {
        $this->messages = array_merge($this->messages, $this->filterMessages($messages, $this->getLevels(), $this->categories, $this->except));
        $count = count($this->messages);
        if ($count > 0 && ($final || $this->exportInterval > 0 && $count >= $this->exportInterval)) {
            $this->export();
            $this->messages = [];
        }
    }

    /**
     * Stores log messages to sentry.
     */
    public function export()
    {
        foreach ($this->messages as $message) {
            list($msg, $level, $category, $timestamp, $traces) = $message;
            $levelName = Logger::getLevelName($level);

            if (!in_array($levelName, ['error', 'warning', 'info'])) {
                $levelName = 'error';
            }

            $data = [
                'timestamp' => gmdate('Y-m-d\TH:i:s\Z', $timestamp),
                'level' => $levelName,
                'tags' => ['category' => $category],
            ];

            if (isset(Yii::$app->user) && !Yii::$app->user->isGuest) {
                $data['user'] = ['id' => \Yii::$app->user->id];
            }

            if (is_array($msg)) {
                $data = array_merge_recursive($data, $msg);
            } else {
                $data['message'] = $msg;
            }

            if ($msg instanceof \Throwable) {
                $data['message'] = $msg->getMessage();

                if ($msg instanceof PayloadException) {
                    $data['tags'] = $msg->getTag();
                    $data['extra'] = $msg->getPayload();

                    if (!isset($data['user'])) {
                        $data['user'] = $msg->getUser();
                    }
                }

                $this->client->captureException($msg, $data);
            } else {
                $this->client->capture($data);
            }

        }
    }
}