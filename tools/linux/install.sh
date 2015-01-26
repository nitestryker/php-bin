#!/bin/sh
: '
   installer.sh 

   @package PHP-Bin
   @author Jeremy Stevens
   @copyright 2014-2015 Jeremy Stevens
   @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
  
   @description  this chages the file permission moves it to /usr/local/bin
    you do not need to use the python extension just type phpbin -args

   ex. phpbin -i mylog.log -n mylog -s text
'
chmod +x "$PWD/"phpbin.py
sudo cp phpbin.py /usr/local/bin
sudo mv /usr/local/bin/phpbin.py /usr/local/bin/phpbin
echo "Finished"