<?php

namespace mamatveev\yii2SentryLogTarget;


class Serializer extends \Raven_Serializer
{
    /**
     * @param mixed $value
     * @return string|bool|double|int|null
     */
    protected function serializeValue($value)
    {
        if (is_null($value) || is_bool($value) || is_float($value) || is_integer($value)) {
            return $value;
        } elseif (is_object($value) || gettype($value) == 'object') {
            return 'Object ' . get_class($value) . "\n Object Data:\n" . print_r($value, true);
        } elseif (is_resource($value)) {
            return 'Resource ' . get_resource_type($value);
        } elseif (is_array($value)) {
            return 'Array of length ' . count($value);
        } else {
            return $this->serializeString($value);
        }
    }

}