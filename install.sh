#!/bin/sh

# Make a local treeroot node with symlinks for developers

# --- SETINGS: --------------------------------------------------------------------

# PROJECT LOCATION

PROJECT=$(pwd)

# PARENT LOCATION

PARENT=$(dirname "$PROJECT")

# LICENSE

LICENSE="AGPLv3"

# DISTRO ENTERPRISE / OPEN SOURCE

DISTRO="treeroot"

# APACHE2 CONFIG LOCATION

CONFIG="/etc/apache2/sites-enabled/000-default.conf"

# USE SYMLINKS

SYMLINKS="0"

# SITE LOCATION EX

SITE="/var/www/html"

BASE_DIR=${SITE}"/treeroot"

# --- STEP 1: ---------------------------------------------------------------------

#git clone git@pal.ideverket.no:/home/git/arena2
#git clone git@pal.ideverket.no:/home/git/treeroot

# --- STEP 2: ---------------------------------------------------------------------

sudo apt-get install dialog

BASE_DIR=$(dialog --backtitle "Installer" --inputbox "\
Please enter the install path for treeroot:" 10 55 "$BASE_DIR" --output-fd 1)
if [ $? = "1" ]; then

    clear
	echo "Aborting installation."
	exit 1
	
fi



if [ "$BASE_DIR" != "$SITE/treeroot" ]; then
	
    SITE=${BASE_DIR}
    
fi

if [ ! -d ${SITE} ]; then
	
	# If can't find apache2 ask to install it
	# If can't find mysql ask to install it
	# If can't find apache2 web config create it and setup default
	# If can't find php5 ask to install it
	# If can't find cUrl doesn't exist ask to install it
	# If can't find ffmpeg doesn't exist ask to install it
	
	echo ${SITE}" doesn't exist, aborting installation."
	exit 1

fi



if [ ! -d ${PARENT}"/arena2" ]; then

	echo ${PARENT}"/arena2 doesn't exist, copy/clone arena2 repo first, aborting installation."
	exit 1

fi

if [ ! -d ${PARENT}"/treeroot" ]; then

	echo ${PARENT}"/treeroot doesn't exist, copy/clone treeroot repo first, aborting installation."
	exit 1

fi

if [ ! -d ${PARENT}"/treeroot/distro/"${DISTRO} ]; then

	LICENSE="AGPLv3"
	
	DISTRO="subether"
	
else 
	
	read -p "Enterprise (Treeroot) distro found, install this instead of the Open Source (SubEther) distro (y/n) ? " CHOOSE_DISTRO
	
	if [ "$CHOOSE_DISTRO" = "n" ]; then
		
		LICENSE="AGPLv3"
		
		DISTRO="subether"
	
	fi
	
fi

if [ ! -d ${PARENT}"/treeroot/distro/"${DISTRO} ]; then
		
	echo ${PARENT}"/treeroot/distro/"${DISTRO}" doesn't exist, contact support, aborting installation."
	exit 1
	
fi


VERSION="`cat "${PROJECT}"/distro/"${DISTRO}"/version.txt`"


# check system
if cat /etc/*-release | grep ^ID | grep ubuntu; then

    echo "Ubuntu distro found"
    
    if cat /etc/*-release | grep ^VERSION_ID | grep 14; then
            echo "version 14"
    		INSTALL_SCRIPT_NUMBER=1
	elif cat /etc/*-release | grep ^VERSION_ID | grep 15; then
            echo "version 15"
            INSTALL_SCRIPT_NUMBER=1
	elif cat /etc/*-release | grep ^VERSION_ID | grep 16; then
            echo "version 16"
            INSTALL_SCRIPT_NUMBER=2
	elif cat /etc/*-release | grep ^VERSION_ID | grep 17; then
            echo "version 17"
            INSTALL_SCRIPT_NUMBER=2
	elif cat /etc/*-release | grep ^VERSION_ID | grep 18; then
            echo "version 18"
            INSTALL_SCRIPT_NUMBER=2
	else
            echo "version other"
            INSTALL_SCRIPT_NUMBER=0
    fi
elif cat /etc/*-release | grep ^ID | grep debian; then

	echo "Debian distro found"
	
	if cat /etc/*-release | grep ^VERSION_ID | grep 8; then
		echo "version 8"
		INSTALL_SCRIPT_NUMBER=1
	elif cat /etc/*-release | grep ^VERSION_ID | grep 9; then
		echo "version 9"
		INSTALL_SCRIPT_NUMBER=2
	else
		echo "version other"
		INSTALL_SCRIPT_NUMBER=0
	fi
elif cat /etc/*-release | grep ^ID | grep mint; then

    echo "Mint distro found"
    
    if cat /etc/*-release | grep ^VERSION_ID | grep 16; then
            echo "version 16"
            INSTALL_SCRIPT_NUMBER=1
    elif cat /etc/*-release | grep ^VERSION_ID | grep 17; then
            echo "version 17"
            INSTALL_SCRIPT_NUMBER=1
	elif cat /etc/*-release | grep ^VERSION_ID | grep 18; then
            echo "version 18"
            INSTALL_SCRIPT_NUMBER=2
	else
            echo "version other"
            INSTALL_SCRIPT_NUMBER=0
    fi
else
    INSTALL_SCRIPT_NUMBER=-1
fi



if [ "$INSTALL_SCRIPT_NUMBER" -eq "1" ];then

    sudo apt-get install zip apache2 php5 mysql-server libapache2-mod-php php5-mysql php5-gd php5-curl php5-xml ffmpeg rsync

elif [ "$INSTALL_SCRIPT_NUMBER" -eq "2" ];then

    sudo apt-get install zip apache2 php mysql-server libapache2-mod-php php-mysql php-gd php-curl php-xml ffmpeg rsync 

else

    dialog --backtitle "Treeroot installer" --msgbox "Supported linux version not found!\n\n\
Write to us: developer@friendos.com" 8 40
    clear
    exit 1

fi

#TODO: Make sure upload files also get copied as expected ....

sudo apt-get update

#TODO: Make sure it's possible to use the same installer for later updates

if [ -d ${BASE_DIR}"/subether" ]; then

	# Take backup of treeroot
	
	read -p "Do you want to take backup of "${BASE_DIR}" and store it in ${PARENT}/backup.treeroot (y/n) ? " BACKUP
	
	if [ "$BACKUP" = "y" ]; then
		
		echo "Taking backup of "${BASE_DIR}
		
		sudo rsync -ravL ${BASE_DIR}/subether ${PARENT}/backup.treeroot/ > /dev/null
		sudo rsync -ravL ${BASE_DIR}/extensions ${PARENT}/backup.treeroot/ > /dev/null
		
		sudo rsync -ravL ${BASE_DIR}/admin ${PARENT}/backup.treeroot/ > /dev/null
		sudo rsync -ravL ${BASE_DIR}/friend ${PARENT}/backup.treeroot/ > /dev/null
		sudo rsync -ravL ${BASE_DIR}/lib ${PARENT}/backup.treeroot/ > /dev/null
		sudo rsync -ravL ${BASE_DIR}/web ${PARENT}/backup.treeroot/ > /dev/null
		sudo rsync -ravL ${BASE_DIR}/upload ${PARENT}/backup.treeroot/ > /dev/null
		
	fi
	
	#echo ${BASE_DIR}" exist, aborting installation."
	#exit 1
	
fi

cd ${PROJECT}/distro/${DISTRO}/ && sh makezipdistro.sh

if [ ! -f ${PARENT}"/"${DISTRO}${VERSION}".zip" ]; then

	echo ${PARENT}"/"${DISTRO}${VERSION}".zip doesn't exist, aborting installation."
	exit 1

fi



read -p "Do you want to use symlinks to link from "${PARENT}"/arena2 and "${PARENT}"/treeroot to "${BASE_DIR}" (y/n) ? " USE_SYMLINKS

if [ "$USE_SYMLINKS" = "y" ]; then
	
	SYMLINKS="yes"
	
	if [ ! -f ${CONFIG} ]; then
		
		echo ${CONFIG}" doesn't exist, aborting installation."
		exit 1
		
	fi
	
	FOLLOW_SYMLINK_FOUND=$(sed -n "/Options FollowSymLinks/p" "$CONFIG")
	
	if [ "$FOLLOW_SYMLINK_FOUND" = "" ]; then
		
		CONFIG_VAR="DocumentRoot $BASE_DIR"
		
		CONFIG_EDIT="\n\n\t<Directory $BASE_DIR>\n\t\tOptions FollowSymLinks\n\t\tAllowOverride All\n\t</Directory>"
		
		echo ${CONFIG_EDIT}
		
		read -p "
		
To use symlinks the option has to be enabled in the Apache config, do you want to add this under DocumentRoot in $CONFIG (y/n) ? " CONF
		
		if [ "$CONF" = "y" ]; then
		
			sudo sed -i "s|${CONFIG_VAR}|${CONFIG_VAR}${CONFIG_EDIT}|g" $CONFIG
		
		fi
		
	fi
	
fi



echo "Unziping "${PARENT}"/"${DISTRO}${VERSION}".zip to "${BASE_DIR}

sudo unzip -q ${PARENT}/${DISTRO}${VERSION}.zip -d ${BASE_DIR}/

# --- STEP 3: --------------------------------------------------------------------

echo "Setting privileges"

sudo chmod -R 777 ${BASE_DIR}/upload/
sudo chmod -R 777 ${BASE_DIR}/subether/upload/
sudo chown -R www-data.www-data ${BASE_DIR}/

# --- STEP 4: --------------------------------------------------------------------

if [ "$SYMLINKS" = "yes" ]; then
	
	# TODO: Add .gitignore or something to the repo so you don't have to do this for lib/ and subether/upload/
	
	echo "Removing folders and files in "${BASE_DIR}" that will be symlinked"
	
	sudo rm -rf ${BASE_DIR}/admin
	sudo rm -rf ${BASE_DIR}/friend
	sudo rm -rf ${BASE_DIR}/web
	sudo rm -rf ${BASE_DIR}/extensions/easyeditor
	sudo rm -rf ${BASE_DIR}/extensions/editor
	sudo rm -rf ${BASE_DIR}/extensions/userlogin
	
	sudo rm -rf ${BASE_DIR}/lib/3rdparty
	sudo rm -rf ${BASE_DIR}/lib/classes
	#sudo rm -rf ${BASE_DIR}/lib/enterprise
	sudo rm -rf ${BASE_DIR}/lib/fonts
	sudo rm -rf ${BASE_DIR}/lib/functions
	sudo rm -rf ${BASE_DIR}/lib/help
	sudo rm -rf ${BASE_DIR}/lib/icons
	sudo rm -rf ${BASE_DIR}/lib/include
	sudo rm -rf ${BASE_DIR}/lib/javascript
	sudo rm -rf ${BASE_DIR}/lib/locale
	sudo rm -rf ${BASE_DIR}/lib/plugins
	sudo rm -rf ${BASE_DIR}/lib/skeleton
	sudo rm -rf ${BASE_DIR}/lib/templates
	sudo rm -rf ${BASE_DIR}/lib/themes
	
	sudo rm -f ${BASE_DIR}/lib/core_config.php.example
	sudo rm -f ${BASE_DIR}/lib/error.html
	sudo rm -f ${BASE_DIR}/lib/error.jpg
	sudo rm -f ${BASE_DIR}/lib/htaccess
	sudo rm -f ${BASE_DIR}/lib/index.html
	sudo rm -f ${BASE_DIR}/lib/install.php
	sudo rm -f ${BASE_DIR}/lib/lib.php
	sudo rm -f ${BASE_DIR}/lib/resources.php
	
	sudo rm -rf ${BASE_DIR}/upload/template
	
	sudo rm -rf ${BASE_DIR}/extensions/sbook
	sudo rm -rf ${BASE_DIR}/extensions/templates
	sudo rm -rf ${BASE_DIR}/subether/applications
	sudo rm -rf ${BASE_DIR}/subether/classes
	sudo rm -rf ${BASE_DIR}/subether/components
	sudo rm -rf ${BASE_DIR}/subether/css
	sudo rm -rf ${BASE_DIR}/subether/functions
	sudo rm -rf ${BASE_DIR}/subether/gfx
	sudo rm -rf ${BASE_DIR}/subether/include
	sudo rm -rf ${BASE_DIR}/subether/javascript
	sudo rm -rf ${BASE_DIR}/subether/locale
	sudo rm -rf ${BASE_DIR}/subether/modules
	sudo rm -rf ${BASE_DIR}/subether/plugins
	sudo rm -rf ${BASE_DIR}/subether/restapi
	sudo rm -rf ${BASE_DIR}/subether/themes
	sudo rm -rf ${BASE_DIR}/subether/thirdparty
	
	sudo rm -f ${BASE_DIR}/MPL.txt
	sudo rm -f ${BASE_DIR}/README
	
	sudo rm -f ${BASE_DIR}/config.php.example
	sudo rm -f ${BASE_DIR}/.htaccess
	sudo rm -f ${BASE_DIR}/robots.txt
	
	sudo rm -f ${BASE_DIR}/subether/install.php
	sudo rm -f ${BASE_DIR}/subether/templates/standardemail.php
	
	sudo rm -f ${BASE_DIR}/upload/about.html
	sudo rm -f ${BASE_DIR}/upload/terms.html
	sudo rm -f ${BASE_DIR}/upload/copyright.html
	sudo rm -f ${BASE_DIR}/upload/advertising.html
	sudo rm -f ${BASE_DIR}/upload/privacy.html
	sudo rm -f ${BASE_DIR}/upload/policy.html
	sudo rm -f ${BASE_DIR}/upload/creators.html
	sudo rm -f ${BASE_DIR}/upload/developers.html
	
	#sudo rm -f ${BASE_DIR}/upload/images-master/logo_white.png
	#sudo rm -f ${BASE_DIR}/upload/images-master/logo_symbol_white.png
	#sudo rm -f ${BASE_DIR}/upload/images-master/logo_black.png
	#sudo rm -f ${BASE_DIR}/upload/images-master/logo_symbol_black.png
	
	sudo rm -f ${BASE_DIR}/subether/arenadefault.sql
	sudo rm -f ${BASE_DIR}/subether/defaultdb.sql
	sudo rm -f ${BASE_DIR}/subether/info.txt
	sudo rm -f ${BASE_DIR}/subether/version.txt
	sudo rm -f ${BASE_DIR}/subether/verification.txt
	sudo rm -f ${BASE_DIR}/subether/README.txt
	sudo rm -f ${BASE_DIR}/subether/AGPLv3.txt
	
fi

# --- STEP 5: --------------------------------------------------------------------

if [ "$SYMLINKS" = "yes" ]; then
	
	# TODO: Add .gitignore or something to the repo so you don't have to do this for lib/ and subether/upload/
	
	echo "Adding symlinks from "${PARENT}"/arena2 to "${BASE_DIR}
	echo "Adding symlinks from "${PARENT}"/treeroot to "${BASE_DIR}
	
	sudo ln -s ${PARENT}/arena2/admin ${BASE_DIR}/admin
	sudo ln -s ${PARENT}/arena2/friend ${BASE_DIR}/friend
	sudo ln -s ${PARENT}/arena2/web ${BASE_DIR}/web
	sudo ln -s ${PARENT}/arena2/extensions/easyeditor ${BASE_DIR}/extensions/easyeditor
	sudo ln -s ${PARENT}/arena2/extensions/editor ${BASE_DIR}/extensions/editor
	sudo ln -s ${PARENT}/arena2/extensions/userlogin ${BASE_DIR}/extensions/userlogin
	
	sudo ln -s ${PARENT}/arena2/lib/3rdparty ${BASE_DIR}/lib/3rdparty
	sudo ln -s ${PARENT}/arena2/lib/classes ${BASE_DIR}/lib/classes
	#sudo ln -s ${PARENT}/arena2/lib/enterprise ${BASE_DIR}/lib/enterprise
	sudo ln -s ${PARENT}/arena2/lib/fonts ${BASE_DIR}/lib/fonts
	sudo ln -s ${PARENT}/arena2/lib/functions ${BASE_DIR}/lib/functions
	sudo ln -s ${PARENT}/arena2/lib/help ${BASE_DIR}/lib/help
	sudo ln -s ${PARENT}/arena2/lib/icons ${BASE_DIR}/lib/icons
	sudo ln -s ${PARENT}/arena2/lib/include ${BASE_DIR}/lib/include
	sudo ln -s ${PARENT}/arena2/lib/javascript ${BASE_DIR}/lib/javascript
	sudo ln -s ${PARENT}/arena2/lib/locale ${BASE_DIR}/lib/locale
	sudo ln -s ${PARENT}/arena2/lib/plugins ${BASE_DIR}/lib/plugins
	sudo ln -s ${PARENT}/arena2/lib/skeleton ${BASE_DIR}/lib/skeleton
	sudo ln -s ${PARENT}/arena2/lib/templates ${BASE_DIR}/lib/templates
	sudo ln -s ${PARENT}/arena2/lib/themes ${BASE_DIR}/lib/themes
	
	sudo ln -s ${PARENT}/arena2/lib/core_config.php.example ${BASE_DIR}/lib/core_config.php.example
	sudo ln -s ${PARENT}/arena2/lib/error.html ${BASE_DIR}/lib/error.html
	sudo ln -s ${PARENT}/arena2/lib/error.jpg ${BASE_DIR}/lib/error.jpg
	sudo ln -s ${PARENT}/arena2/lib/htaccess ${BASE_DIR}/lib/htaccess
	sudo ln -s ${PARENT}/arena2/lib/index.html ${BASE_DIR}/lib/index.html
	sudo ln -s ${PARENT}/arena2/lib/install.php ${BASE_DIR}/lib/install.php
	sudo ln -s ${PARENT}/arena2/lib/lib.php ${BASE_DIR}/lib/lib.php
	sudo ln -s ${PARENT}/arena2/lib/resources.php ${BASE_DIR}/lib/resources.php
	
	sudo ln -s ${PARENT}/arena2/MPL.txt ${BASE_DIR}/MPL.txt
	sudo ln -s ${PARENT}/arena2/README ${BASE_DIR}/README
	
	sudo ln -s ${PARENT}/arena2/config.php.example ${BASE_DIR}/config.php.example
	sudo ln -s ${PARENT}/arena2/lib/htaccess ${BASE_DIR}/.htaccess
	
	sudo ln -s ${PARENT}/treeroot/upload/template ${BASE_DIR}/upload/template
	
	sudo ln -s ${PARENT}/treeroot/extensions/sbook ${BASE_DIR}/extensions/sbook
	sudo ln -s ${PARENT}/treeroot/extensions/templates ${BASE_DIR}/extensions/templates
	sudo ln -s ${PARENT}/treeroot/subether/applications ${BASE_DIR}/subether/applications
	sudo ln -s ${PARENT}/treeroot/subether/classes ${BASE_DIR}/subether/classes
	sudo ln -s ${PARENT}/treeroot/subether/components ${BASE_DIR}/subether/components
	sudo ln -s ${PARENT}/treeroot/subether/css ${BASE_DIR}/subether/css
	sudo ln -s ${PARENT}/treeroot/subether/functions ${BASE_DIR}/subether/functions
	sudo ln -s ${PARENT}/treeroot/subether/gfx ${BASE_DIR}/subether/gfx
	sudo ln -s ${PARENT}/treeroot/subether/include ${BASE_DIR}/subether/include
	sudo ln -s ${PARENT}/treeroot/subether/javascript ${BASE_DIR}/subether/javascript
	sudo ln -s ${PARENT}/treeroot/subether/locale ${BASE_DIR}/subether/locale
	sudo ln -s ${PARENT}/treeroot/subether/modules ${BASE_DIR}/subether/modules
	sudo ln -s ${PARENT}/treeroot/subether/plugins ${BASE_DIR}/subether/plugins
	sudo ln -s ${PARENT}/treeroot/subether/restapi ${BASE_DIR}/subether/restapi
	sudo ln -s ${PARENT}/treeroot/subether/themes ${BASE_DIR}/subether/themes
	sudo ln -s ${PARENT}/treeroot/subether/thirdparty ${BASE_DIR}/subether/thirdparty
	
	sudo ln -s ${PARENT}/treeroot/subether/install.php ${BASE_DIR}/subether/install.php
	sudo ln -s ${PARENT}/treeroot/distro/${DISTRO}/standardemail.php ${BASE_DIR}/subether/templates/standardemail.php
	
	sudo ln -s ${PARENT}/treeroot/distro/${DISTRO}/about.html ${BASE_DIR}/upload/about.html
	sudo ln -s ${PARENT}/treeroot/distro/${DISTRO}/terms.html ${BASE_DIR}/upload/terms.html
	sudo ln -s ${PARENT}/treeroot/distro/${DISTRO}/copyright.html ${BASE_DIR}/upload/copyright.html
	sudo ln -s ${PARENT}/treeroot/distro/${DISTRO}/advertising.html ${BASE_DIR}/upload/advertising.html
	sudo ln -s ${PARENT}/treeroot/distro/${DISTRO}/privacy.html ${BASE_DIR}/upload/privacy.html
	sudo ln -s ${PARENT}/treeroot/distro/${DISTRO}/policy.html ${BASE_DIR}/upload/policy.html
	sudo ln -s ${PARENT}/treeroot/distro/${DISTRO}/creators.html ${BASE_DIR}/upload/creators.html
	sudo ln -s ${PARENT}/treeroot/distro/${DISTRO}/developers.html ${BASE_DIR}/upload/developers.html
	
	#sudo ln -s ${PARENT}/treeroot/distro/${DISTRO}/logo_white.png ${BASE_DIR}/upload/images-master/logo_white.png
	#sudo ln -s ${PARENT}/treeroot/distro/${DISTRO}/logo_symbol_white.png ${BASE_DIR}/upload/images-master/logo_symbol_white.png
	#sudo ln -s ${PARENT}/treeroot/distro/${DISTRO}/logo_black.png ${BASE_DIR}/upload/images-master/logo_black.png
	#sudo ln -s ${PARENT}/treeroot/distro/${DISTRO}/logo_symbol_black.png ${BASE_DIR}/upload/images-master/logo_symbol_black.png
	
	sudo ln -s ${PARENT}/treeroot/distro/${DISTRO}/robots.txt ${BASE_DIR}/robots.txt
	sudo ln -s ${PARENT}/treeroot/distro/${DISTRO}/arenadefault.sql ${BASE_DIR}/subether/arenadefault.sql
	sudo ln -s ${PARENT}/treeroot/distro/${DISTRO}/defaultdb.sql ${BASE_DIR}/subether/defaultdb.sql
	sudo ln -s ${PARENT}/treeroot/distro/${DISTRO}/info.txt ${BASE_DIR}/subether/info.txt
	sudo ln -s ${PARENT}/treeroot/distro/${DISTRO}/version.txt ${BASE_DIR}/subether/version.txt
	sudo ln -s ${PARENT}/treeroot/distro/${DISTRO}/verification.txt ${BASE_DIR}/subether/verification.txt
	sudo ln -s ${PARENT}/treeroot/distro/${DISTRO}/README.txt ${BASE_DIR}/subether/README.txt
	sudo ln -s ${PARENT}/treeroot/distro/${DISTRO}/${LICENSE}.txt ${BASE_DIR}/subether/${LICENSE}.txt
	
fi

# --- STEP 6: --------------------------------------------------------------------

echo "Adding allow rewite addon for apache2"

sudo a2enmod rewrite

# --- STEP 7: --------------------------------------------------------------------

sudo service apache2 restart

xdg-open http://localhost/treeroot/

