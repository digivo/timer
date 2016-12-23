<?php

namespace Digivo\Timer;

use Digivo\Timer\Exceptions\TimerException;

trait TimerTrait
{
    /** @var array Variable to hold all the timers we set */
    protected $timers = [];

    /**
     * Start a timer, identified by the $key
     * @param  string $key  The key to identify this timer
     * @return array        Returns the timer array
     */
    public function startTimer($key)
    {
        $this->timers[$key]['start'] = microtime(true);

        return $this->timers[$key];
    }

    /**
     * Stops a timer, identified by the $key
     * @param  string $key        The key to identify this timer
     * @param  bool   $returnTime Whether function should return the elasped time, or the time object
     * @return array|float        Returns the timer array
     */
    public function stopTimer($key, $returnTime = false)
    {
        if (!isset($this->timers[$key]['start'])) {
            throw new TimerException('Timer '.$key.' has not been started');
        }

        if (isset($this->timers[$key]['end'])) {
            throw new TimerException('Timer '.$key.' has already been ended');
        }

        $this->timers[$key]['end'] = microtime(true);
        $this->timers[$key]['time'] = $this->timers[$key]['end']-$this->timers[$key]['start'];

        // Remove any "last" temp keys
        if (isset($this->timers[$key]['last'])) {
            unset($this->timers[$key]['last']);
        }

        if ($returnTime) {
            return $this->timers[$key]['time'];
        }

        return $this->timers[$key];
    }

    /**
     * Splits a timer, identified by the $key
     * @param  string $key        The key to identify this timer
     * @param  string $comment    A comment to identify the split
     * @param  bool   $returnTime Whether function should return the elasped time, or the time object
     * @return array|float        Returns the timer array
     */
    public function splitTimer($key, $comment = null, $returnTime = false)
    {
        if (!isset($this->timers[$key]['start'])) {
            throw new TimerException('Timer '.$key.' has not been started');
        }

        if (isset($this->timers[$key]['end'])) {
            throw new TimerException('Timer '.$key.' has already been stopped');
        }

        // Find the start time we will use for the "lap"
        $last = isset($this->timers[$key]['last']) ? $this->timers[$key]['last'] : $this->timers[$key]['start'];

        // Register the split time
        $split = microtime($key);

        // Calculate the cumulative and "lap" times
        $time = $split-$this->timers[$key]['start'];
        $lap = $split-$last;

        // Init the splits array, if not exists
        if (!isset($this->timers[$key]['splits'])) {
            $this->timers[$key]['splits'] = [];
        }

        $this->timers[$key]['splits'][] = [
            'time' => $time, 
            'lap' => $lap,
            'end' => $split,
            'comment' => $comment
        ];

        // Set the "last" time
        $this->timers[$key]['last'] = $split;

        if ($returnTime) {
            return $time;
        }

        return $this->timers[$key];
    }

    /**
     * Return the timer data. If $key is null, return the object
     * @param  string $key
     * @return array|bool
     */
    public function returnTimer($key = null)
    {
        if (!is_null($key)) {
            if (isset($this->timers[$key])) {
                return $this->timers[$key];
            }

            return false;
        }

        return $this->timers;
    }
}
