# Timer Trait
Provides start, end, lap and elapsed timing.

## Usage
```php
<?php

namespace App\Controller;

use Digivo\Timer\TimerTrait;

class FunController
{
    use TimerTrait;

    public function run()
    {
        $this->startTimer('app');

        sleep(2);

        $this->splitTimer('app', 'Woke up for a while');

        sleep(1);

        $this->splitTimer('app', 'Another little nap');        

        sleep(3);

        $this->stopTimer('app');

        var_export($this->returnTimer('app'));
    }
}

```

```
array (
  'app' => array (
    'start' => 1482484048.9344,
    'splits' => array (
      0 => array (
        'time' => 2.0002,
        'lap' => 2.0002,
        'end' => 1482484050.9346,
        'comment' => 'Woke up for a while',
      ),
      1 => array (
        'time' => 3.0005,
        'lap' => 1.0003,
        'end' => 1482484051.9349,
        'comment' => 'Another little nap',
      ),
    ),
    'end' => 1482484054.9351,
    'time' => 6.0007,
  ),
)
```
