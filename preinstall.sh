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

    [ ! -d upload/ ] && echo "# mkdir upload/" && mkdir -p upload/ 2>&1

    echo "# chgrp -R $webuser upload/"
    chgrp -R $webuser upload/

    echo "# chmod -R g+w upload/"
    chmod -R g+w upload/
}

fUpdateSwitchPerm() {
    softswitch_guess="/tmp"

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

    echo "# chgrp -R $webuser $softswitch_dir/*"
    chgrp -R $webuser $softswitch_dir/*

    echo "# chmod -R g+w $softswitch_dir/*"
    chmod -R g+w $softswitch_dir/*
}

fUpdateOdbcPerm() {
    echo
    echo "ODBC PRIVILEGES"
    echo "---------------------------------------------------------"
    echo "Updating the permissions so Blue.box can write to "
    echo "odbc.ini files."

    if fConfirmYes "Would you like to update odbc.ini permissions (Y/n)"; then

        echo "# chgrp $webuser /etc/odbc.ini"
        chgrp $webuser /etc/odbc.ini

        echo "# chmod a+w /etc/odbc.ini"
        chmod a+w /etc/odbc.ini
    fi
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
fSetWebUser
fUpdateBlueboxPerm
fUpdateSwitchPerm

[ -f '/etc/odbc.ini' ] && fUpdateOdbcPerm

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
