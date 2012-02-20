################################################################
# This Source Code Form is subject to the terms of the Mozilla Public
# License, v. 2.0. If a copy of the MPL was not distributed with this file,
# You can obtain one at http://mozilla.org/MPL/2.0/.
################################################################

#!/bin/sh
# Script: Bluebox Backup Script
# Author: Antonio Sangio

# Script will backup the following directories:

BACKUP_CORE="/var/lib/mysql
/opt/freeswitch/storage/
/var/log/freeswitch/cdr-csv
/opt/freeswitch/conf"

BACKUP_CONFIG=`find /var/www/html/bluebox/ -name "config" | xargs`


# Specify the directory that will hold the backup
BACKDIR="/root/backup"

# Number of Backups to Keep
KEEP="4"

##
## End Configuration
##

# Create Initial Backup
if [ ! -f $BACKDIR/backup.1.tgz ]; then
	tar zcf $BACKDIR/backup.1.tgz $BACKUP_CORE $BACKUP_CONFIG >/dev/null 2>&1
	chmod 600 $BACKDIR/backup.1.tgz
	exit
fi

# Delete Oldest Backup
if [ -f $BACKDIR/backup.$KEEP.tgz ]; then
	rm -f $BACKDIR/backup.$KEEP.tgz
fi

# Perform Backup Cycle
if [ -f $BACKDIR/backup.1.tgz ]; then

	if [ -f $BACKDIR/backup.tmp.tgz ]; then
		rm -f $BACKDIR/backup.tmp.tgz
	fi

	tar zcf $BACKDIR/backup.tmp.tgz $BACKUP_CORE $BACKUP_CONFIG >/dev/null 2>&1
	chmod 600 $BACKDIR/backup.tmp.tgz

	COUNT=`expr $KEEP - 1`

	while [ $COUNT -gt 0 ]; do
		mv -f $BACKDIR/backup.$COUNT.tgz $BACKDIR/backup.`expr
$COUNT + 1`.tgz

		COUNT=`expr $COUNT - 1`
	done

	mv -f $BACKDIR/backup.tmp.tgz $BACKDIR/backup.1.tgz

fi
#############################################################

