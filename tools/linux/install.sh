#!/bin/sh
chmod +x "$PWD/"phpbin.py
sudo cp phpbin.py /usr/local/bin
sudo mv /usr/local/bin/phpbin.py /usr/local/bin/phpbin
echo "Finished"