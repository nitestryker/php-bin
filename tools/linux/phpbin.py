
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
parser.add_argument('-i','--input', help='Input file name',required=True)
parser.add_argument('-n','--name', help='paste name', required=False)
parser.add_argument('-s','--syntax', help='paste syntax', required=False)
parser.add_argument('-e','--expire', help='paste expire', required=False)
parser.add_argument('-x','--exposure', help='paste exposure', required=False)
args = parser.parse_args()

# open the file
with open(args.input, "r") as fo:
    # read the file and put it in a var
    file_content = fo.read()

# check if vars are set and strip whitespace and if not set use default values.
if args.name:
    name = args.name.strip()
else:
    # if no title name it "Untitled"
    name = "untitled"

file_content = file_content.strip()

if args.syntax:
    syntax = args.syntax.strip()
else:
    # if not set then set the default to plain text
    syntax = "text"

if args.expire:
    expire = args.expire.strip()
else:
    # if not set then set the default to 10 mins 
    expire = "1"

if args.exposure:
    exposure = args.exposure.strip()
else:
    # if not set then set the default to "public"
    exposure = "Public"
  
# payload
payload = {'name': name, 'syntax': syntax, 'expi': expire, 'expo': exposure, 'text': file_content}

# webrequest with TLS/SSL support
try:
    r = requests.post("https://yourdomain.com/pb/api/pb_api.php", data=payload, verify=True)
    status = r.status_code
    if status == 200:
        print("successful")
    else:
        print(f"Error: HTTP Status {status}")
except Exception as e:
    print(f"Error: {e}")
