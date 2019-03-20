#!/bin/bash
# Grabs and kill a process from the pidlist that has the word myapp
# !!! this is the base stop script so that you can
# !!! install the crypto stop, the crypto stop file will be installed manually on
# !!! each server in an undisclosed location and be used to stop the service
pid=`ps aux | grep SperaCryptoService | awk '{print $2}'`
kill -9 $pid