#!/bin/bash

webuser='nobody'

# BlueBox Startup
echo "Prepping for a typical BlueBox install. Your mileage may vary. It\'s OK if some things fail..."

# SELinux
echo '  Disabling SE Linux, if installed...'
selinuxenabled 0

# Change permissions
echo '  Changing folder permissions to allow read/write...'
mkdir -p bluebox/logs
chgrp -R $webuser bluebox/logs/
chmod -R a+w bluebox/logs
mkdir -p bluebox/cache
chgrp -R $webuser bluebox/cache/
chmod -R a+w bluebox/cache/
chgrp -R $webuser bluebox/config/
chmod -R a+w bluebox/config/
chgrp -R $webuser modules/freeswitch/config/
chmod -R a+w modules/freeswitch/config/
chgrp -R $webuser modules/asterisk/config/
chmod -R a+w modules/asterisk/config/
mkdir -p upload/
chgrp -R $webuser upload/
chmod -R a+w upload/

# Make FS conf directory writable
if [ -d '/usr/local/freeswitch/' ]; then
	echo '  Making FreeSWITCH configs writable...'
	chgrp -R $webuser /usr/local/freeswitch/conf/
	chmod -R a+w /usr/local/freeswitch/conf/*
fi

# Make Asterisk conf directory writable
if [ -d '/etc/asterisk/' ]; then
	echo '  Making Asterisk configs writable...'
	chgrp -R $webuser /etc/asterisk/*
	chmod -R g+w /etc/asterisk/*
fi

# Make ODBC files writable
echo '  Making ODBC configs writable...'
chgrp $webuser /etc/odbc.ini
chmod a+w /etc/odbc.ini

echo 
echo 'Done!'

