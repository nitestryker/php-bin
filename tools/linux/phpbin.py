#!/usr/bin/python     
import argparse
import requests

# headers do not change this!
__author__ = 'Jeremy Stevens'
__license__ = 'GPL'
__version__ = '1.0.0'
__maintainer__ = 'Jeremy Stevens'
__email__ = 'jeremy@jeremystevens.org'
__status__ = 'Development'

# Args list 
parser = argparse.ArgumentParser(description='phpbinit',usage='%(prog)s [-insex]')
parser.add_argument('-i','--input', help='Input file name',required=False)
parser.add_argument('-n','--name', help='paste name', required=False)
parser.add_argument('-s','--syntax', help='paste syntax', required=False)
parser.add_argument('-e','--expire', help='paste expire', required=False)
parser.add_argument('-x','--exposure', help='paste exposure', required=False)
args = parser.parse_args()

# open the file
fo = open(args.input, "r+")
# read the file and put it in a var
str = fo.read();

# check if vars are set before removing whitespace to prevent error
if args.name:
	name = args.name.strip()
str = str.strip()
if args.syntax:
	syntax = args.syntax.strip()
if args.expire:
	expire = args.expire.strip()
if args.exposure:
	exposure = args.exposure.strip()

#payload
payload = {'name': name, 'syntax': syntax, 'expi': expire, 'expo': exposure, 'text': str}
# webrequest 
r = requests.post("http://yourdomain.com/pb/api/pb_api.php", data=payload)
status = r.status_code
if status == 200:
	print "successful"
else:
	print "Error Please try again"
