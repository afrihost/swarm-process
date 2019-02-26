<?php
namespace Afrihost\SwarmProcess;

class Configuration
{
    /** @var callable */
    private $completedCallback;

    /** @var bool $enforceProcessTimeouts */
    private $enforceProcessTimeouts = false;

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