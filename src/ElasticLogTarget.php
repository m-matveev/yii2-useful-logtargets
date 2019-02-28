<?php
namespace mamatveev\yii2LogTargets;


use yii\elasticsearch\ElasticsearchTarget;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\log\Logger;

class ElasticLogTarget extends ElasticsearchTarget
{
    /**
     * @var bool
     */
    public $includeContext = true;

    /**
     * @var bool
     */
    public $cacheContext = true;

    /**
     * @var bool
     */
    public $logContext = false;

    /**
     * Prepares a log message.
     * @param array $message The log message to be formatted.
     * @return string
     */
    public function prepareMessage($message)
    {
        list($msg, $level, $category, $timestamp) = $message;

        $levelName = Logger::getLevelName($level);

        if (!in_array($levelName, ['error', 'warning', 'info'])) {
            $levelName = 'error';
        }

        $data = [
            '@timestamp' => date('c', $timestamp),
            'level' => $levelName,
            'tags' => ['category' => $category],
        ];

        if (is_array($msg)) {
            $data = array_merge_recursive($data, $msg);
        } else {
            $data['message'] = $msg;
        }

        if ($msg instanceof \Throwable) {

            if ($msg instanceof PayloadException) {
                $data['tags'] = array_merge($data['tags'], $msg->getTag());
                $data['extra'] = $msg->getPayload();
            }

            $data['message'] = $msg->getMessage();
            $data['trace'] = $msg->getTraceAsString();
        }

        if ($this->includeContext) {
            $data['context'] = VarDumper::export($this->getContextMessage());
        }

        if (isset($data['extra'])) {
            $data['extra'] = VarDumper::export($data['extra']);
        }

        $message = implode("\n", [
            Json::encode([
                'index' => new \stdClass()
            ]),
            Json::encode($data)
        ]);

        return $message;
    }

}
