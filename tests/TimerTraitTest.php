<?php

use PHPUnit\Framework\TestCase;
use Digivo\Timer\TimerTrait;
use Digivo\Timer\Exceptions\TimerException;

class TimerTraitTest extends TestCase
{
    use TimerTrait;

    public function test_timer_starts()
    {
        $this->startTimer('test');

        $timers = $this->returnTimer();
        
        $this->assertEquals(count($timers), 1);
        $this->assertArrayHasKey('test', $timers);
        $this->assertArrayHasKey('start', $timers['test']);
    }

    public function test_timer_stops()
    {
        $this->startTimer('test');
        usleep(500);
        $this->stopTimer('test');

        $timers = $this->returnTimer();
        
        $this->assertArrayHasKey('start', $timers['test']);
        $this->assertArrayHasKey('end', $timers['test']);
        $this->assertGreaterThan($timers['test']['start'], $timers['test']['end']);
    }

    public function test_timer_splits()
    {
        $this->startTimer('test');
        usleep(250);
        $this->splitTimer('test', 'Comment 1');
        usleep(250);
        $this->splitTimer('test', 'Comment 2');
        usleep(250);
        $this->stopTimer('test');

        $timers = $this->returnTimer();
        
        $this->assertArrayHasKey('splits', $timers['test']);
        $this->assertEquals(count($timers['test']['splits']), 2);
        $this->assertGreaterThan($timers['test']['splits'][0]['end'], $timers['test']['splits'][1]['end']);
        $this->assertEquals($timers['test']['splits'][0]['comment'], 'Comment 1');
    }

    public function test_retrieve_single_timer()
    {
        $this->startTimer('test');
        usleep(500);
        $this->stopTimer('test');

        $timer = $this->returnTimer('test');
        
        $this->assertArrayHasKey('end', $timer);
    }

    public function test_nested_timers()
    {
        $this->startTimer('test');
        usleep(500);
        $this->startTimer('test2');
        usleep(200);
        $this->stopTimer('test2');
        usleep(100);
        $this->stopTimer('test');

        $timers = $this->returnTimer();
        
        $this->assertEquals(count($timers), 2);
        $this->assertGreaterThan($timers['test']['start'], $timers['test2']['start']);
        $this->assertGreaterThan($timers['test2']['end'], $timers['test']['end']);
    }

    public function test_cannot_stop_timer_twice()
    {
        $this->expectException(TimerException::class);

        $this->startTimer('test');
        $this->stopTimer('test');
        $this->stopTimer('test');
    }

    public function test_cannot_stop_stopped_timer()
    {
        $this->expectException(TimerException::class);

        $this->startTimer('test');
        $this->stopTimer('test');
        $this->splitTimer('test', 'Comment');
    }

    public function test_cannot_stop_unitialized_timer()
    {
        $this->expectException(TimerException::class);

        $this->stopTimer('test');
    }

    public function test_cannot_split_unitialized_timer()
    {
        $this->expectException(TimerException::class);

        $this->splitTimer('test', 'Comment');
    }
}
