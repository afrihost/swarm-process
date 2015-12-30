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
composer require afrihost/
```
### Usage
TODO: Fill this out

### Stuck - need help

### Contributing

### Contributor List

### TODO
- [ ] Finalize README.md - this however, should wait until the project code is fleshed out a little bit more