# SwarmProcess
[![Latest Stable Version](https://poser.pugx.org/afrihost/swarm-process/v/stable)](https://packagist.org/packages/afrihost/swarm-process) [![Total Downloads](https://poser.pugx.org/afrihost/swarm-process/downloads)](https://packagist.org/packages/afrihost/swarm-process) [![Latest Unstable Version](https://poser.pugx.org/afrihost/swarm-process/v/unstable)](https://packagist.org/packages/afrihost/swarm-process) [![License](https://poser.pugx.org/afrihost/swarm-process/license)](https://packagist.org/packages/afrihost/swarm-process)
[![Build Status](https://travis-ci.org/afrihost/swarm-process.svg?branch=master)](https://travis-ci.org/afrihost/swarm-process)

### What and Why

A process handler that runs concurrent processes keeping maximum in mind, and re-using slots until the list of processes are done. 

**Example:** You have 50 jobs to run, as fast as you can possibly get to them. One solution is to have a script run all 50 at the same time. Granted this might work with 10, or 50, or even 100 jobs on a powerful enough server - but what if you have 10 000 jobs to run? You can't run 10 000 jobs at the same time. If all those jobs have to have a DB connection, you'll run out with most databases at around the 150 mark, and even then the jobs you're doing might place too much straign on the DB.

**Solution:** Only run a maximum number of concurrent jobs at a time - but don't just run the say 10 jobs and then do another 10 once the first group is done - run 10 and the moment a "slot" becomes free, run the 11th job, then the 12th, eventually the 10000th job - this means there's less wasted "wait time".

##### Why have a "concurrent cron" solution written in PHP?
The answer is simple: Most of your code is in PHP, so why have your job manager written in something like Python or Node or whatever the flavor of the month is? Doing it in a language you already know (assuming this is PHP and this is why you're here) helps with troubleshooting and tweaking.

### Installation
Should be as simple as composer-install and then you're off able to use it:
```shell
composer require afrihost/swarm-process
```
### Usage
There are two ways of using this, have it do the whole run or interactive mode using tick.

#### $swarm->run()
```php
use Afrihost\SwarmProcess\SwarmProcess;

$swarm = new SwarmProcess(); // you may provide a Psr/Logger here if you want to
$swarm->setMaxRunStackSize(20); // default is 10 (cannot make it <= 0)

// Just some mock things for it to do:
for ($i = 0; $i < 10000; $i++) {
    $swarm->pushNativeCommandOnQueue('echo "test"');

    // Some examples of how to use it - note the new Process way!
    // $swarm->pushNativeCommandOnQueue('ls');
    // $swarm->pushNativeCommandOnQueue('sleep 10');
    // $swarm->pushProcessOnQueue(new Process('ls'));
}

// Now we tell it to go run all 10k things, but to adhere to the 20 concurrent rule set above:
$swarm->run();
```

The above code it should be quite self-explanatory. I'd like to point out though, that when you call `$swarm->run();` you now have to wait for it to be done with it's 10k cycle before the rest of you rapplication continues. If, however, you want to carry on with other things, that's what `$swarm->tick();` is for...

#### $swarm->tick()

Say you have the scenario where you either want to do other things in your application while you wait for the 10k processes to run in the background - or more commonly might have more things that you want to add (or are merely concerned about the memory consumption of adding a list of 10k or 100k or 10billion things in an array to be run). This is where `$swarm->tick();` is handy.

Under the hood, the `$swarm->run();` method merely starts a while loop and runs `$this->tick()` until it doesn't have any thing more to do. The decision of what to return is: "If there are still commands in the queue of things to run **OR** there are still things being run currently, then return **true**, otherwise return **false**"

For this reason, you could replace the very last bit of code above, the `$swarm->run();` with:

```php
do {
	// do nothing
} while ($swarm->tick());
```

That will do exactly the same as the $swarm->run() function.

If you want to, say, check your DB to see if there's more things to add to the swarm, then you might do something like this:

```php
do {
	if ($db->hasMoreStuffToAddDummyFunction()) {
		$swarm->pushProcessOnQueue(new Process($db->getSomeCommandToAddToTheQueue()));
	}
} while ($swarm->tick());
```

A note on large arrays: When you push a new command/process on to an array, the method of "popping" an element from the **beginning** of the array is the use of `array_shift`. Though in later versions of PHP it's much less prominent, there is still a slight performance knock on large arrays, because of the fact that PHP will have to re-index the array after each `array_shift`. So, if you're dealing with 100s of thousands of entries, and you are having performance issues due to this fact, you'd do good trying the $swarm->tick() method agove and trickle-feeding things into the system.

#### Closure / Callback

As of version 1.1 we now provide two callback parameters for the `->run()` method.

The first callable parameter is used by you to add any more work to the queue while it's running. Think of it as what is inside the do-while in the above-mentioned example.

The second callable parameter is used to override the ending of the loop. For example you might not want the loop to end if the queue is empty, but only after say 5 minutes of inactivity. This you can then put in the second callback. Internally the logic is: "If either tick() returns true **or** the callback returns true, the loop still continues!"

Here's an example of how that would look:

```php
$swarm->run(
    function() {
        // do a check to see if we should have more commands added to the queue
        return new Process('sleep 5');
    },
    function () {
        // check if the loop should still continue, if so return true
        return true;
    }
);
```

#### Completion Callback

As of version 1.2 you will be able to provide a callback to be called upon completion of each process. The aim ere is to use it to ascertain what the exitCode was, for example. A use case would be to reschedule the process in the case of failure, or to log te failure for a human to look into.

Here's how you would use it:

```php
$swarmProcess = new SwarmProcess($logger);
$swarmProcess->setCompletedCallback(function(Symfony\Component\Process\Process $process) {
    // do something with the $process returned, checking it's exit code, and perhaps putting it back on the stack
});
```

#### Examples:

You may also look at the examples provided in the `examples` folder. Run them using:

```shell
php examples/simple-run.php
php examples/simple-run-process.php
php examples/simple-tick.php
php examples/simple-run-with-callbacks.php
```

### Need help?

Open an issue on Github and let's take it from there

### Contributing

- Fork the repo
- Clone your repo locally
- `composer install`
- Run the tets to make sure all is well: `./vendor/bin/phpunit`
- Create a branch naming the change you're making
- Do your thing :)
- Run the tests as stated in the .travis.yml file (`./vendor/bin/phpunit`)
- If you're ready: commit and push to your repo, then send me a **pull request**

If you want to discuss it, I'm happy to chat over an issue on github.com ;)

### TODO

- [x] Finalize README.md - this however, should wait until the project code is fleshed out a little bit more
- [x] Create interactive mode (`->tick()`)
- [ ] Create public method to ask how many things left in the queue, and how many things are currently running
- [x] Create closure callbacks for `->run()` to give more control to the user without having to write their own while loop
