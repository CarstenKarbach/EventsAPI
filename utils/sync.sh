#Script to synchronize local directory to a remote webserver
#Replace username and hostname with the appropriate values for your own configuration
#This script must be executed from the root of the repository as working directory.
#Call this script as: ./sync.sh './' karbach zam2015 
#Or when using a different remote folder ./sync.sh './' karbach zam2015 remote-folder
#
#This script can also be used to only fix the file permissions:
#E.g. by calling it like this: ./synch.sh './'
#Only use the folder parameter for that to work
#

FOLDER=$1
USER=$2
HOST=$3
PORTPART=""

# checking if another remote folder should be used
if [ -z "$4" ]
  then
    REMOTE="~/htdocs/antragssystem/"
else
	REMOTE="$4/"
fi

# checking if another port option is needed, e.g. -e "ssh -p 2244"
if [ -z "$5" ]
  then
    PORTPART="ssh"
else
	PORTPART="$5"
fi

CDIR=`pwd`

if [ "$USER" == "" ] 
then
	echo "Only setting file permissions in folder $FOLDER"
else 
	echo "synchronizing $FOLDER in $CDIR with user $USER @ $HOST:$REMOTE"
fi

find $FOLDER -type d -exec chmod 711 {} +
find $FOLDER -type f -exec chmod 644 {} +
find $FOLDER -type f -name '*.pl' -exec chmod 711 {} +
find $FOLDER -type f -name '*.log' -exec chmod 666 {} +
find $FOLDER -type f -name '*.sh' -exec chmod 711 {} +
find $FOLDER -type f -name '*.shell' -exec chmod 711 {} +
find $FOLDER -type f -name '*.phar' -exec chmod 711 {} +
find $FOLDER -type f -name 'pre-commit' -exec chmod 711 {} +
chmod 777 './data'

COMPOSER_SUCCESS=1

if [ -f "$FOLDER/composer.phar" ]; then
    if command -v php > /dev/null && \
        php "$FOLDER/composer.phar" self-update && \
        php "$FOLDER/composer.phar" install; then
            echo "Running composer seemed to be successful."
    else
        echo "PHP not installed or composer failed, so we cannot install composer dependencies!"
        COMPOSER_SUCCESS=0
        if [ "$USER" != "" ] ; then
            echo "Will try to run composer on target machine after sync..."
        fi
    fi
else
    echo "No composer.phar in source-directory. Assuming old branch? Should not happen, though..."
fi

if [ "$USER" != "" ] 
then
    if [ "$COMPOSER_SUCCESS" == "1" ]; then
        /usr/bin/rsync --exclude=.git --delete -avrp -e "$PORTPART" $FOLDER "$USER"@"$HOST":$REMOTE
    else
        /usr/bin/rsync --exclude=.git --exclude=vendor/ --delete -avrp -e "$PORTPART" $FOLDER "$USER"@"$HOST":$REMOTE
        if $PORTPART "$USER"@"$HOST" "bash -c 'cd $REMOTE;php composer.phar self-update && php composer.phar install; exit "'$'"?'"; then
            echo "Installed composer dependencies on remote machine..."
        else
            echo "ERROR: Installing composer dependencies on remote machine failed!"
        fi
    fi
fi