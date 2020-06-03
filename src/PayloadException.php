<?php
namespace mamatveev\yii2LogTargets;

use yii\log\Logger;

class PayloadException extends \Exception
{
    /**
     * @var array
     */
    protected $payload = [];

    /**
     * @var array
     */
    protected $tag = [];

    /**
     * @var array
     */
    protected $user = [];

    /**
     * @var int
     */
    protected $level = Logger::LEVEL_ERROR;

    /**
     * @param string $message
     * @return PayloadException
     */
    public static function create(string $message)
    {
        return new PayloadException($message);
    }

    /**
     * @return array
     */
    public function getPayload(): array
    {
        return $this->payload;
    }

    /**
     * @param array $payload
     * @return PayloadException
     */
    public function setPayload(array $payload): PayloadException
    {
        $this->payload = $payload;
        return $this;
    }

    /**
     * @return array
     */
    public function getTag(): array
    {
        return $this->tag;
    }

    /**
     * @param array $tag
     * @return PayloadException
     */
    public function setTag(array $tag): PayloadException
    {
        $this->tag = $tag;
        return $this;
    }

    /**
     * @return array
     */
    public function getUser(): array
    {
        return $this->user;
    }

    /**
     * @param array $user
     * @return PayloadException
     */
    public function setUser(array $user): PayloadException
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @param int $level
     * @return $this
     */
    public function setLevel(int $level)
    {
        $this->level = $level;
        return $this;
    }

    /**
     * @return int
     */
    public function getLevel() : int
    {
        return $this->level;
    }


}
