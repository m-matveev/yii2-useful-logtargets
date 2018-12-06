<?php
namespace mamatveev\yii2SentryLogTarget;


class SentryClient extends \Raven_Client
{
    /**
     * SentryClient constructor.
     * @param null $options_or_dsn
     * @param array $options
     */
    public function __construct($options_or_dsn = null, array $options = array())
    {
        parent::__construct($options_or_dsn, $options);
        $this->serializer = new Serializer();
    }


    /**
     * @param $data
     */
    public function sanitize(&$data)
    {
        // attempt to sanitize any user provided data
        if (!empty($data['request'])) {
            $data['request'] = $this->serializer->serialize($data['request'], 5);
        }
        if (!empty($data['user'])) {
            $data['user'] = $this->serializer->serialize($data['user'], 3);
        }
        if (!empty($data['extra'])) {
            $data['extra'] = $this->serializer->serialize($data['extra'], 20);
        }
        if (!empty($data['tags'])) {
            foreach ($data['tags'] as $key => $value) {
                $data['tags'][$key] = @(string)$value;
            }
        }
        if (!empty($data['contexts'])) {
            $data['contexts'] = $this->serializer->serialize($data['contexts'], 5);
        }
        if (!empty($data['breadcrumbs'])) {
            $data['breadcrumbs'] = $this->serializer->serialize($data['breadcrumbs'], 5);
        }
    }

}