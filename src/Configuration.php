<?php
namespace Afrihost\SwarmProcess;

class Configuration
{
    /** @var callable */
    private $completedCallback;

    /** @var bool $enforceProcessTimeouts */
    private $enforceProcessTimeouts = false;

    /** @var int $tickLoopDelayMicroseconds */
    private $tickLoopDelayMicroseconds = 0;

    /**
     * @return int
     */
    public function getTickLoopDelayMicroseconds()
    {
        return $this->tickLoopDelayMicroseconds;
    }

    /**
     * @param int $tickLoopDelayMicroseconds
     * @return Configuration
     */
    public function setTickLoopDelayMicroseconds($tickLoopDelayMicroseconds)
    {
        $this->tickLoopDelayMicroseconds = $tickLoopDelayMicroseconds;
        return $this;
    }

    /**
     * @return bool
     */
    public function isEnforceProcessTimeouts()
    {
        return $this->enforceProcessTimeouts;
    }

    /**
     * @param bool $enforceProcessTimeouts
     * @return Configuration
     */
    public function setEnforceProcessTimeouts($enforceProcessTimeouts)
    {
        $this->enforceProcessTimeouts = $enforceProcessTimeouts;
        return $this;
    }

    /**
     * @return callable
     */
    public function getCompletedCallback()
    {
        return $this->completedCallback;
    }

    /**
     * @param callable $completedCallback
     * @return Configuration
     * @throws \Exception
     */
    public function setCompletedCallback($completedCallback)
    {
        if (!is_callable($completedCallback)) {
            throw new \Exception('Must provide callable as callback');
        }

        $this->completedCallback = $completedCallback;
        return $this;
    }

}