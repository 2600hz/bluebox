#!/bin/bash

fUsage () {
    echo "Usage: $0 -y [--webuser=www] [--softswitch_dir=/usr/local/freeswitch/conf/]"
    exit 1
}

fWelcome() {
    clear
    echo "========================================================"
    echo " ______  _      _     _ _______ ______  _______ _     _ "
    echo "(____  \(_)    (_)   (_|_______|____  \(_______|_)   (_)"
    echo " ____)  )_      _     _ _____   ____)  )_     _   ___   "
    echo "|  __  (| |    | |   | |  ___) |  __  (| |   | | |   |  "
    echo "| |__)  ) |____| |___| | |_____| |__)  ) |___| |/ / \ \ "
    echo "|______/|_______)_____/|_______)______/ \_____/|_|   |_|"
    echo
    echo "      - - - Our free software. Your next VoIP system!"
    echo "========================================================"
}

fConfirmYes() {
    echo -n "$1? "

    if [ ! -z $accept_all ]; then
        echo
        return 1
    fi

    read ans

    case "$ans" in
        n|N|no|NO|No) return 1 ;;

        *) return 0 ;;
    esac
}

fConfirmNo() {
    echo -n "$1? "

    if [ ! -z $accept_all ]; then
        echo
        return 0
    fi

    read ans

    case "$ans" in
        y|Y|yes|YES|Yes) return 0 ;;

        *) return 1 ;;
    esac

    fConfirmNo "$1"
}

fCheckSELinux() {
    if which getenforce &> /dev/null; then

        res=`getenforce | grep -i enforcing`;

        if [[ ${#res} > 0 ]]; then
            echo
            echo "SELINUX"
            echo "---------------------------------------------------------"
            echo "We have detected that selinux is enabled. This will cause"
            echo "issues as it inhibits the webservers ability to write to"
            echo "the softswitch config."

            if fConfirmYes "Would you like to disable selinux (Y/n)"; then
                echo '# selinuxenabled 0'
                selinuxenabled 0 2>&1

                echo "# sed -i -r 's/^SELINUX=enforcing/SELINUX=disabled/g' /etc/selinux/config"
                sed -i -r 's/^SELINUX=enforcing/SELINUX=disabled/g' /etc/selinux/config 2>&1
            fi
        else
            echo
            echo "SELINUX"
            echo "---------------------------------------------------------"
            echo "The system appears to have SELINUX disabled . . . OK"
        fi

    else
        echo
        echo "SELINUX"
        echo "---------------------------------------------------------"
        echo "Assuming SELINUX is not on this server . . . OK"
    fi
}

fSetWebUser() {
    webuser_guess=nobody

    if which id &> /dev/null; then

        id -g www &>/dev/null
        [ $? -eq 0 ] && webuser_guess=www

        id -g www-data &>/dev/null
        [ $? -eq 0 ] && webuser_guess=www-data

        id -g apache &>/dev/null
        [ $? -eq 0 ] && webuser_guess=apache
    fi

    # This was for MAC, not sure if it is needed
    [ `uname` = 'Darwin' ] && webuser_guess='www'

    [ ! -z $webuser ] && webuser_guess=$webuser

    echo
    echo "WEB USER"
    echo "---------------------------------------------------------"
    echo "We need to verify the web user so we can correctly set   "
    echo "the permissions on certain folder/files."
    echo -n "Web user name [$webuser_guess]? "

    if [ ! -z $accept_all ]; then
        webuser="$webuser_guess"
        echo
    else
        read ans

        if [ -z $ans ]; then
            webuser="$webuser_guess"
        else
            webuser="$ans"
        fi
    fi
}

fUpdateBlueboxPerm() {
    echo
    echo "BLUEBOX PRIVILEGES"
    echo "---------------------------------------------------------"
    echo "Updating the permissions so Blue.box can write to its "
    echo "configuration files."

    [ ! -d bluebox/logs ] && echo "# mkdir -p bluebox/logs" && mkdir -p bluebox/logs 2>&1

    echo "# chgrp -R $webuser bluebox/logs/"
    chgrp -R $webuser bluebox/logs/

    echo "# chmod -R g+w bluebox/logs"
    chmod -R g+w bluebox/logs

    [ ! -d bluebox/cache ] && echo "# mkdir -p bluebox/cache" && mkdir -p bluebox/cache 2>&1

    echo "# chgrp -R $webuser bluebox/cache/"
    chgrp -R $webuser bluebox/cache/

    echo "# chmod -R g+w bluebox/cache"
    chmod -R g+w bluebox/cache

    echo "# chgrp -R $webuser bluebox/config/"
    chgrp -R $webuser bluebox/config/

    echo "# chmod -R g+w bluebox/config/"
    chmod -R g+w bluebox/config/

    echo "# chgrp -R $webuser modules/freeswitch-*/config/freeswitch.php"
    chgrp -R $webuser modules/freeswitch-*/config/freeswitch.php

    echo "# chmod -R g+w modules/freeswitch-*/config/freeswitch.php"
    chmod -R g+w modules/freeswitch-*/config/freeswitch.php

    echo "# chgrp -R $webuser modules/asterisk-*/config/asterisk.php"
    chgrp -R $webuser modules/asterisk-*/config/asterisk.php

    echo "# chmod -R g+w modules/asterisk-*/config/asterisk.php"
    chmod -R g+w modules/asterisk-*/config/asterisk.php

    [ ! -d uploads/ ] && echo "# mkdir uploads/" && mkdir -p uploads/ 2>&1

    echo "# chgrp -R $webuser uploads/"
    chgrp -R $webuser uploads/

    echo "# chmod -R g+w uploads/"
    chmod -R g+w uploads/
}

fFixSoundsPerms() {
    [ -d '/var/lib/asterisk/sounds/' ] && sounddir_guess="/var/lib/asterisk/sounds"

    [ -d '/usr/local/freeswitch/sounds/' ] && sounddir_guess="/usr/local/freeswitch/sounds"

    [ -d '/opt/freeswitch/sounds/' ] && sounddir_guess="/opt/freeswitch/sounds"

    echo
    echo "SOUND FILE PRIVILEGES"
    echo "---------------------------------------------------------"
    echo "We need to verify the path to your sound files."
    echo -n "Sound file dir [$sounddir_guess]? "

    if [ ! -z $accept_all ]; then
       sound_dir="$sounddir_guess"
       echo
    else
        read ans

        if [ -z $ans ]; then
            sound_dir="$sounddir_guess"
        else
            sound_dir="$ans"
        fi
    fi

    [ -z "$sound_dir" -o ! -d "$sound_dir" ] && return 0

    echo "# chgrp -R $webuser $sound_dir"
    chgrp -R $webuser $sound_dir

    echo "# chmod -R g+w $sound_dir"
    chmod -R g+w $sound_dir
}

fFixRecordsPerms() {

    [ -d '/usr/local/freeswitch/recordings/' ] && recorddir_guess="/usr/local/freeswitch/recordings"

    [ -d '/opt/freeswitch/recordings/' ] && recorddir_guess="/opt/freeswitch/recordings"

    echo
	echo "RECORDING FILE PRIVILEGES"
    echo "---------------------------------------------------------"
    echo "We need to verify the path to your recording files."
    echo -n "Record file dir [$recorddir_guess]? "

    if [ ! -z $accept_all ]; then
		record_dir="$recorddir_guess"
       echo
	else
		read ans

        if [ -z $ans ]; then
			record_dir="$recorddir_guess"
        else
			record_dir="$ans"
        fi
	fi

    [ -z "$record_dir" -o ! -d "$record_dir" ] && return 0

    echo "# chgrp -R $webuser $record_dir"
    chgrp -R $webuser $record_dir

    echo "# chmod -R g+w $record_dir"
    chmod -R g+w $record_dir
}

fUpdateSwitchPerm() {
    [ -d '/usr/local/freeswitch/conf/' ] && softswitch_guess="/usr/local/freeswitch/conf"

    [ -d '/opt/freeswitch/conf/' ] && softswitch_guess="/opt/freeswitch/conf"

    [ -d '/etc/asterisk/' ] && softswitch_guess="/etc/asterisk"

    [ ! -z $softswitch_dir ] && softswitch_guess=$softswitch_dir

    echo
    echo "SOFTSWITCH PRIVILEGES"
    echo "---------------------------------------------------------"
    echo "We need to verify the path to your softswitch confs "
    echo "directory so we can update its permissions."
    echo -n "Softswitch conf dir [$softswitch_guess]? "

    if [ ! -z $accept_all ]; then
       softswitch_dir="$softswitch_guess"
       echo
    else
        read ans

        if [ -z $ans ]; then
            softswitch_dir="$softswitch_guess"
        else
            softswitch_dir="$ans"
        fi
    fi

    [ -z "$softswitch_dir" -o ! -d "$softswitch_dir" ] && return 0

    echo "# chgrp -R $webuser $softswitch_dir"
    chgrp -R $webuser $softswitch_dir

    echo "# chmod -R g+w $softswitch_dir"
    chmod -R g+w $softswitch_dir
}

fCopyConfigs() {
    echo "Checking config files"
    [ ! -e 'bluebox/config/config.php' ] && echo "# Copying bluebox/config/config.php" && cp bluebox/config/config.php.dist bluebox/config/config.php 2>&1	
    [ ! -e 'bluebox/config/database.php' ] && echo "# Copying bluebox/config/database.php" && cp bluebox/config/database.php.dist bluebox/config/database.php 2>&1	
    [ ! -e 'bluebox/config/email.php' ] && echo "# Copying bluebox/config/email.php" && cp bluebox/config/email.php.dist bluebox/config/email.php 2>&1	
    [ ! -e 'bluebox/config/locale.php' ] && echo "# Copying bluebox/config/locale.php" && cp bluebox/config/locale.php.dist bluebox/config/locale.php 2>&1	
    [ ! -e 'bluebox/config/session.php' ] && echo "# Copying bluebox/config/session.php" && cp bluebox/config/session.php.dist bluebox/config/session.php 2>&1	
    [ ! -e 'bluebox/config/telephony.php' ] && echo "# Copying bluebox/config/telephony.php" && cp bluebox/config/telephony.php.dist bluebox/config/telephony.php 2>&1	
    [ ! -e 'bluebox/config/upload.php' ] && echo "# Copying bluebox/config/upload.php" && cp bluebox/config/upload.php.dist bluebox/config/upload.php 2>&1	
    [ ! -e 'modules/freeswitch-1.1.1/config/freeswitch.php' ] && echo "# Copying modules/freeswitch-1.1.1/config/freeswitch.php" && cp modules/freeswitch-1.1.1/config/freeswitch.php.dist modules/freeswitch-1.1.1/config/freeswitch.php 2>&1	
    [ ! -e 'modules/asterisk-1.0/config/asterisk.php' ] && echo "# Copying modules/asterisk-1.0/config/asterisk.php" && cp modules/asterisk-1.0/config/asterisk.php.dist modules/asterisk-1.0/config/asterisk.php 2>&1	
}

cd `dirname $0`
while [ -n "$*" ]; do
    case "x$1" in   
        x--webuser=*)
            webuser=`echo "$1"|cut -d= -sf2`
            ;;
        x--softswitch_dir=*)
            softswitch_dir=`echo "$1"|cut -d= -sf2`
            ;;
        x--sound_dir=*)
            sound_dir=`echo "$1"|cut -d= -sf2`
            ;;
        x-y)
            accept_all=1
            ;;
        x--help)
            fUsage
            ;;         
        *)
            fUsage
            ;;
    esac
    shift
done


fWelcome

fCheckSELinux
fCopyConfigs
fSetWebUser
fUpdateBlueboxPerm
fUpdateSwitchPerm
fFixSoundsPerms
fFixRecordsPerms

echo
echo "PLEASE SET UP YOUR DB"
echo "---------------------------------------------------------"
echo "You must now ensure your database has a user configured "
echo "for Blue.box, you will enter the credintials in the next"
echo "phase."
#echo
#echo "In mysql cli you can create a user via:"
#echo "# CREATE USER 'bluebox'@'127.0.0.1' IDENTIFIED BY 'bluebox';"
#echo "# GRANT ALL ON bluebox.* TO 'bluebox'@'127.0.0.1';"
#echo "# FLUSH PRIVILEGES;"
echo
echo "COMPLETE!"
echo "---------------------------------------------------------"
echo "Now point your web browser to your server and the "
echo "Blue.box installer will get your system ready."
echo "Example http://127.0.0.1/"
echo
echo "      Welcome and thank you for using Blue.Box!"

exit 0
