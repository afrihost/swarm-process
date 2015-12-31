# Afrihost SwarmProcess
[![Latest Stable Version](https://poser.pugx.org/afrihost/swarm-process/v/stable)](https://packagist.org/packages/afrihost/swarm-process) [![Total Downloads](https://poser.pugx.org/afrihost/swarm-process/downloads)](https://packagist.org/packages/afrihost/swarm-process) [![Latest Unstable Version](https://poser.pugx.org/afrihost/swarm-process/v/unstable)](https://packagist.org/packages/afrihost/swarm-process) [![License](https://poser.pugx.org/afrihost/swarm-process/license)](https://packagist.org/packages/afrihost/swarm-process)

[![Build](https://travis-ci.org/afrihost/swarm-process.svg)](https://travis-ci.org/afrihost/swarm-process.svg)

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
use Monolog\Logger;

$logger = new Logger('swarm_logger'); // You'll need to give it a logger

$swarm = new SwarmProcess($logger);
$swarm->setMaxRunStackSize(20); // default is 10 (cannot make it <= 0)

// Just some mock things for it to do:
for ($i = 0; $i < 10000; $i++) {
    $swarm->pushNativeCommandOnStack('echo "test"');

    // Some examples of how to use it - note the new Process way!
    // $swarm->pushNativeCommandOnStack('ls');
    // $swarm->pushNativeCommandOnStack('sleep 10');
    // $swarm->pushProcessOnStack(new Process('ls'));
}

// Now we tell it to go run all 10k things, but to adhere to the 20 concurrent rule set above:
$swarm->run();
```

Wht the above code it should be quite self-explanatory. I'd like to point out though, that when you call `$swarm->run();` you now have to wait for it to be done with it's 10k cycle before the rest of you rapplication continues. If, however, you want to carry on with other things, that's what `$swarm->tick();` is for...

#### $swarm->tick()

Say you have the scenario where you either want to do other things in your application while you wait for the 10k processes to run in the background - or more commonly might have more things that you want to add (or are merely concerned about the memory consumption of adding a list of 10k or 100k or 10billion things in an array to be run). This is where `$swarm->tick();` is handy.

Under the hood, the `$swarm->run();` method merely starts a while loop and runs `$this->tick()` until it doesn't have any thing more to do. The decision of what to return is: "If there are still commands in the queue of things to run **OR** there are still things being run currently, then return **true**, otherwise return **false**"

For this reason, you could replace the very last bit of code above, the `$swarm->run();` with:

```php
while ($swarm->tick()) {
	// do nothing
}
```

That will do exactly the same as the $swarm->run() command (go ahead, look at the code).

If you want to, say, check your DB to see if there's more things to add to the swarm, then you might do something like this:

```php
while ($swarm->tick()) {
	if ($db->hasMoreStuffToAddDummyFunction()) {
		$swarm->pushProcessOnStack(new Process($db->getSomeCommandToAddToTheQueue()));
	}
}
```

A note on large arrays: When you push a new command/process on to an array, the method of "popping" an element from the **beginning** of the array is the use of `array_shift`. Though in later versions of PHP it's much less prominent, there is still a slight performance knock on large arrays, because of the fact that PHP will have to re-index the array after each `array_shift`. So, if you're dealing with 100s of thousands of entries, and you are having performance issues due to this fact, you'd do good trying the $swarm->tick() method agove and trickle-feeding things into the system.

### Need help?

Open an issue on Github and let's take it from there

### Contributing

- Fork the repo
- Create a branch naming the change you're making
- Run the tests as stated in the .travis.yml file
- If you're ready, send me a **pull request**

If you want to discuss it, I'm happy to chat over an issue on github.com ;)

### TODO

- [x] Finalize README.md - this however, should wait until the project code is fleshed out a little bit more
- [x] Create interactive mode (`->tick()`)
- [ ] Create public method to ask how many things left in the queue, and how many things are currently running