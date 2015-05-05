#!/usr/bin/python
import time
import commands
import os
import imp

# do not change this!
__author__ = 'Jeremy Stevens'
__license__ = 'GPL'
__version__ = '0.1'
__maintainer__ = 'Jeremy Stevens'
__email__ = 'jeremy@jeremystevens.org'
__status__ = 'Development'

# start the script
os.system('clear');
print "phpbin python tool installer V0.1 \r"
print ""
time.sleep(2)
print "checking dependencies. \r"
time.sleep(3)
try:
	mod1 = imp.find_module('argparse')
        print "argparse found.\r"
        a = True 
except ImportError:
        a = False
        print "module: argparse is not installed,  please install it \r"
time.sleep(3)
try:
	mod2 = imp.find_module('requests')
        b = True
        print "requests found. \r"
except ImportError:
        b = False
	print "module: requests is not installed,  please install it \r"
if (a == 'False' and b == 'False'):
	print "please install missing modules and try again \r"
	exit()
if (a == 'False'):
  print "please install missing module and try again \r"
	exit()
if (b == 'False'):
  print "please install missing module and try again \r"
	exit()                             
  
# everything looks good lets continue
print "all required dependencies found \r"
time.sleep(2)
print "starting installation \r"
time.sleep(2)
print "changing the file permissions \r"
time.sleep(1)
perm = commands.getoutput("chmod +x phpbin.py")
print perm
time.sleep(1)
print "copying to /usr/local/bin \r"
time.sleep(2)
copy = commands.getoutput("sudo cp phpbin.py /usr/local/bin")
print copy
time.sleep(1)
print "renaming file \r"
time.sleep(2)
rename = commands.getoutput("sudo mv /usr/local/bin/phpbin.py /usr/local/bin/phpbin")
time.sleep(2)
print "finished \r"
time.sleep(1)
print "usage: phpbin -args"
