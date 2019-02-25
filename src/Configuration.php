<?php
namespace Afrihost\SwarmProcess;

class Configuration
{
    /** @var callable */
    private $completedCallback;

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