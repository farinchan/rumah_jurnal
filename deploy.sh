#!/bin/bash

# Exit immediately if a command exits with a non-zero status
set -e

# FTP server details
FTP_HOST=$1
FTP_USERNAME=$2
FTP_PASSWORD=$3
LOCAL_DIR=$4
REMOTE_DIR=$5

# Print debug information
echo "FTP_HOST: $FTP_HOST"
echo "FTP_USERNAME: $FTP_USERNAME"
echo "LOCAL_DIR: $LOCAL_DIR"
echo "REMOTE_DIR: $REMOTE_DIR"

# Use lftp to mirror the local directory to the remote directory, skipping SSL verification
lftp -d -c "
set ssl:verify-certificate no;
open -u $FTP_USERNAME,$FTP_PASSWORD $FTP_HOST;
mirror -R --verbose --only-newer --parallel=10 $LOCAL_DIR $REMOTE_DIR;
bye;
"
