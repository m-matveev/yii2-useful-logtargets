<?php

namespace mamatveev\yii2LogTargets;


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

    public function sanitize_encoding($data)
    {
        if (is_string($data)) {
            return mb_convert_encoding($data, 'UTF-8');
        } elseif (is_array($data)) {
            $ret = [];
            foreach ($data as $i => $d) $ret[$i] = $this->sanitize_encoding($d);
            return $ret;
        } elseif (is_object($data)) {
            foreach ($data as $i => $d) $data->$i = $this->sanitize_encoding($d);
            return $data;
        } else {
            return $data;
        }
    }

    public function encode(&$data)
    {
        $data = $this->sanitize_encoding($data);
        return parent::encode($data);
    }
}
