## What is phpbin?

phpbin is a PHP-based web application that allows you to store text online for a set period of time.

[![Build Status](https://travis-ci.org/jeremystevens/php-bin.svg?branch=master)](https://travis-ci.org/jeremystevens/php-bin)
### Features:

1. Store text online for a set period of time
2. Syntax highlighting
3. Line numbers for debugging purposes 
4. Download to text File
5. Share link on Social Media
6. Quick Print  
7. Report Abuse 
8. Live recent post sidebar 
9. Cron-job to prune expired post. 

### Requirements:

- PHP <= 5.4
- Mysql >= 5.0
- Cron-Job or Scheduled Task

#### Work around 

if you hosting service does not offer cron-jobs / scheduled tasks you can still prune post, 
just not as effectively as running a cronjob, you do this  by including the cronjob script on different pages
when a user hits the page it will run the cronjob script and prune necessary post.

the script is located in /include/cronjob.php  

ex. add   include('include/cronjob.php');    on several pages

### Bug Tracker / Reporting Bugs 

Please feel free to report any bugs you find so I can fix them,
either by submitting them directly on my [bug tracker] (http://bt.jeremystevens.org)
or via email at [bugs@jeremystevens.org](mailto:bugs@jeremystevens.org)

please make sure to be as detailed as possible and if possible send screenshots.

### Support 

some of your questions maybe already answered in my [knowlege base] (http://kb.jeremystevens.org)
or if you have a solution or a work around to a problem please submit it.
 
 
Couldn't figure something out and didn't find the answer in the knowledge base then please submit a ticket on 
my [support page] (http://support.jeremystevens.org) and i will help you as soon as possible 

or you can email me your question at [support@jeremystevens.org](mailto:jeremystevens@gmail.com)

please make sure to be as detailed as possible and if possible send screenshots.


### Current Version V1.0.8

For the current CHANGELOG please visit [CHANGELOG] (https://github.com/jeremystevens/php-bin/wiki/CHANGELOG)

installation instructions
======
      
   1.  create database install database tables located in (/install/sql) 
   2.  edit  /include/config.php 
   3.  edit .htaccess file  
  
