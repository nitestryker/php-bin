( this was a Collaboration Project I am  now the only one maintaining the code)

## What is phpbin?

phpbin is a PHP-based web application that allows you to store text online for a set period of time.

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

### Known issues in V1.0.9

1. Paste Archive when  number of paste exceed the page limit it starts are new     page. the paste on the next page will not appear this is a bug. and will be fixed soon.
   

#### Work around 

if you hosting service does not offer cron-jobs / scheduled tasks you can still prune post, 
just not as effectively as running a cronjob, you do this  by including the cronjob script on different pages
when a user hits the page it will run the cronjob script and prune necessary post.

the script is located in /include/cronjob.php  

ex. add   include('include/cronjob.php');    on several pages

### Bug Tracker / Reporting Bugs 

Please feel free to report any bugs you find so I can fix them,
either by submitting them directly on my [bug tracker] (https://www.hostedredmine.com/projects/phpbin/)
or via email at [nitestryker@gmail.com](mailto:nitestryker@gmail.com)

please make sure to be as detailed as possible and if possible send screenshots.


### Current Version V1.0.9

For the current CHANGELOG please visit [CHANGELOG] (https://www.hostedredmine.com/projects/phpbin/wiki)

installation instructions
======
      
   1.  create database install database tables located in (/install/sql) 
   2.  edit  /include/config.php 
   3.  edit .htaccess file  
  
