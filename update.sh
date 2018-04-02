#!/bin/sh

# Update a local treeroot node with symlinks for developers

# --- SETINGS: --------------------------------------------------------------------

# PROJECT LOCATION

PROJECT=$(pwd)

# PARENT LOCATION

PARENT=$(dirname "$PROJECT")

# APACHE2 CONFIG LOCATION

CONFIG="/etc/apache2/sites-enabled/000-default.conf"

# USE SYMLINKS

SYMLINKS="0"

# SITE LOCATION EX

SITE="/var/www/html"

BASE_DIR=${SITE}"/treeroot"

# --- STEP 1: ---------------------------------------------------------------------

sudo apt-get install dialog

if [ ! -d ${PARENT}"/arena2" ]; then

	echo ${PARENT}"/arena2 doesn't exist, copy/clone arena2 repo first, aborting update."
	exit 1

fi

if [ ! -d ${PARENT}"/treeroot" ]; then

	echo ${PARENT}"/treeroot doesn't exist, copy/clone treeroot repo first, aborting update."
	exit 1

fi

sudo apt-get update

if [ ! -d ${SITE} ]; then
	
	echo ${SITE}" doesn't exist, aborting update."
	exit 1

fi

if [ ! -d ${SITE}"/treeroot" ]; then

	echo ${SITE}"/treeroot doesn't exist, aborting update."
	exit 1

fi



read -p "Do you want to use symlinks to link from "${PARENT}"/arena2 and "${PARENT}"/treeroot to "${SITE}"/treeroot (y/n) ? " USE_SYMLINKS

if [ "$USE_SYMLINKS" = "y" ]; then
	
	SYMLINKS="yes"
	
	if [ ! -f ${CONFIG} ]; then
	
		echo ${CONFIG}" doesn't exist, aborting update."
		exit 1

	fi
	
	FOLLOW_SYMLINK_FOUND=$(sed -n "/Options FollowSymLinks/p" "$CONFIG")
	
	if [ "$FOLLOW_SYMLINK_FOUND" = "" ]; then
		
		CONFIG_VAR="DocumentRoot $SITE"
		
		CONFIG_EDIT="\n\n\t<Directory $SITE>\n\t\tOptions FollowSymLinks\n\t\tAllowOverride All\n\t</Directory>"
		
		echo ${CONFIG_EDIT}
		
		read -p "
		
To use symlinks the option has to be enabled in the Apache config, do you want to add this under DocumentRoot in $CONFIG (y/n) ? " CONF
		
		if [ "$CONF" = "y" ]; then
		
			sudo sed -i "s|${CONFIG_VAR}|${CONFIG_VAR}${CONFIG_EDIT}|g" $CONFIG
		
		fi
		
	fi
	
fi

# --- STEP 2: --------------------------------------------------------------------

# Take backup of treeroot

read -p "Do you want to take backup of "${SITE}"/treeroot and store it in ${PARENT}/backup.treeroot (y/n) ? " BACKUP

if [ "$BACKUP" = "y" ]; then
	
	echo "Taking backup of "${SITE}"/treeroot"
	
	sudo rsync -ravL ${SITE}/treeroot/subether ${PARENT}/backup.treeroot/ > /dev/null
	sudo rsync -ravL ${SITE}/treeroot/extensions ${PARENT}/backup.treeroot/ > /dev/null
	
	sudo rsync -ravL ${SITE}/treeroot/admin ${PARENT}/backup.treeroot/ > /dev/null
	sudo rsync -ravL ${SITE}/treeroot/friend ${PARENT}/backup.treeroot/ > /dev/null
	sudo rsync -ravL ${SITE}/treeroot/lib ${PARENT}/backup.treeroot/ > /dev/null
	sudo rsync -ravL ${SITE}/treeroot/web ${PARENT}/backup.treeroot/ > /dev/null
	sudo rsync -ravL ${SITE}/treeroot/upload ${PARENT}/backup.treeroot/ > /dev/null
	
fi

# --- STEP 3: --------------------------------------------------------------------

if [ "$SYMLINKS" = "yes" ]; then
	
	# TODO: Add .gitignore or something to the repo so you don't have to do this for lib/ and subether/upload/
	
	echo "Removing folders and files in "${SITE}"/treeroot that will be symlinked"
	
	sudo rm -rf ${SITE}/treeroot/admin
	sudo rm -rf ${SITE}/treeroot/friend
	sudo rm -rf ${SITE}/treeroot/web
	sudo rm -rf ${SITE}/treeroot/extensions/easyeditor
	sudo rm -rf ${SITE}/treeroot/extensions/editor
	sudo rm -rf ${SITE}/treeroot/extensions/userlogin
	
	sudo rm -rf ${SITE}/treeroot/lib/3rdparty
	sudo rm -rf ${SITE}/treeroot/lib/classes
	#sudo rm -rf ${SITE}/treeroot/lib/enterprise
	sudo rm -rf ${SITE}/treeroot/lib/fonts
	sudo rm -rf ${SITE}/treeroot/lib/functions
	sudo rm -rf ${SITE}/treeroot/lib/help
	sudo rm -rf ${SITE}/treeroot/lib/icons
	sudo rm -rf ${SITE}/treeroot/lib/include
	sudo rm -rf ${SITE}/treeroot/lib/javascript
	sudo rm -rf ${SITE}/treeroot/lib/locale
	sudo rm -rf ${SITE}/treeroot/lib/plugins
	sudo rm -rf ${SITE}/treeroot/lib/skeleton
	sudo rm -rf ${SITE}/treeroot/lib/templates
	sudo rm -rf ${SITE}/treeroot/lib/themes
	
	sudo rm -f ${SITE}/treeroot/lib/core_config.php.example
	sudo rm -f ${SITE}/treeroot/lib/error.html
	sudo rm -f ${SITE}/treeroot/lib/error.jpg
	sudo rm -f ${SITE}/treeroot/lib/htaccess
	sudo rm -f ${SITE}/treeroot/lib/index.html
	sudo rm -f ${SITE}/treeroot/lib/install.php
	sudo rm -f ${SITE}/treeroot/lib/lib.php
	sudo rm -f ${SITE}/treeroot/lib/resources.php
	
	sudo rm -rf ${SITE}/treeroot/upload/template
	
	sudo rm -rf ${SITE}/treeroot/extensions/sbook
	sudo rm -rf ${SITE}/treeroot/extensions/templates
	sudo rm -rf ${SITE}/treeroot/subether/applications
	sudo rm -rf ${SITE}/treeroot/subether/classes
	sudo rm -rf ${SITE}/treeroot/subether/components
	sudo rm -rf ${SITE}/treeroot/subether/css
	sudo rm -rf ${SITE}/treeroot/subether/functions
	sudo rm -rf ${SITE}/treeroot/subether/gfx
	sudo rm -rf ${SITE}/treeroot/subether/include
	sudo rm -rf ${SITE}/treeroot/subether/javascript
	sudo rm -rf ${SITE}/treeroot/subether/locale
	sudo rm -rf ${SITE}/treeroot/subether/modules
	sudo rm -rf ${SITE}/treeroot/subether/plugins
	sudo rm -rf ${SITE}/treeroot/subether/restapi
	sudo rm -rf ${SITE}/treeroot/subether/themes
	sudo rm -rf ${SITE}/treeroot/subether/thirdparty
	
	sudo rm -f ${SITE}/treeroot/subether/install.php
	
fi

# --- STEP 4: --------------------------------------------------------------------

if [ "$SYMLINKS" = "yes" ]; then
	
	# TODO: Add .gitignore or something to the repo so you don't have to do this for lib/ and subether/upload/
	
	echo "Adding symlinks from "${PARENT}"/arena2 to "${SITE}"/treeroot"
	echo "Adding symlinks from "${PARENT}"/treeroot to "${SITE}"/treeroot"
	
	sudo ln -s ${PARENT}/arena2/admin ${SITE}/treeroot/admin
	sudo ln -s ${PARENT}/arena2/friend ${SITE}/treeroot/friend
	sudo ln -s ${PARENT}/arena2/web ${SITE}/treeroot/web
	sudo ln -s ${PARENT}/arena2/extensions/easyeditor ${SITE}/treeroot/extensions/easyeditor
	sudo ln -s ${PARENT}/arena2/extensions/editor ${SITE}/treeroot/extensions/editor
	sudo ln -s ${PARENT}/arena2/extensions/userlogin ${SITE}/treeroot/extensions/userlogin
	
	sudo ln -s ${PARENT}/arena2/lib/3rdparty ${SITE}/treeroot/lib/3rdparty
	sudo ln -s ${PARENT}/arena2/lib/classes ${SITE}/treeroot/lib/classes
	#sudo ln -s ${PARENT}/arena2/lib/enterprise ${SITE}/treeroot/lib/enterprise
	sudo ln -s ${PARENT}/arena2/lib/fonts ${SITE}/treeroot/lib/fonts
	sudo ln -s ${PARENT}/arena2/lib/functions ${SITE}/treeroot/lib/functions
	sudo ln -s ${PARENT}/arena2/lib/help ${SITE}/treeroot/lib/help
	sudo ln -s ${PARENT}/arena2/lib/icons ${SITE}/treeroot/lib/icons
	sudo ln -s ${PARENT}/arena2/lib/include ${SITE}/treeroot/lib/include
	sudo ln -s ${PARENT}/arena2/lib/javascript ${SITE}/treeroot/lib/javascript
	sudo ln -s ${PARENT}/arena2/lib/locale ${SITE}/treeroot/lib/locale
	sudo ln -s ${PARENT}/arena2/lib/plugins ${SITE}/treeroot/lib/plugins
	sudo ln -s ${PARENT}/arena2/lib/skeleton ${SITE}/treeroot/lib/skeleton
	sudo ln -s ${PARENT}/arena2/lib/templates ${SITE}/treeroot/lib/templates
	sudo ln -s ${PARENT}/arena2/lib/themes ${SITE}/treeroot/lib/themes
	
	sudo ln -s ${PARENT}/arena2/lib/core_config.php.example ${SITE}/treeroot/lib/core_config.php.example
	sudo ln -s ${PARENT}/arena2/lib/error.html ${SITE}/treeroot/lib/error.html
	sudo ln -s ${PARENT}/arena2/lib/error.jpg ${SITE}/treeroot/lib/error.jpg
	sudo ln -s ${PARENT}/arena2/lib/htaccess ${SITE}/treeroot/lib/htaccess
	sudo ln -s ${PARENT}/arena2/lib/index.html ${SITE}/treeroot/lib/index.html
	sudo ln -s ${PARENT}/arena2/lib/install.php ${SITE}/treeroot/lib/install.php
	sudo ln -s ${PARENT}/arena2/lib/lib.php ${SITE}/treeroot/lib/lib.php
	sudo ln -s ${PARENT}/arena2/lib/resources.php ${SITE}/treeroot/lib/resources.php
	
	sudo ln -s ${PARENT}/treeroot/upload/template ${SITE}/treeroot/upload/template
	
	sudo ln -s ${PARENT}/treeroot/extensions/sbook ${SITE}/treeroot/extensions/sbook
	sudo ln -s ${PARENT}/treeroot/extensions/templates ${SITE}/treeroot/extensions/templates
	sudo ln -s ${PARENT}/treeroot/subether/applications ${SITE}/treeroot/subether/applications
	sudo ln -s ${PARENT}/treeroot/subether/classes ${SITE}/treeroot/subether/classes
	sudo ln -s ${PARENT}/treeroot/subether/components ${SITE}/treeroot/subether/components
	sudo ln -s ${PARENT}/treeroot/subether/css ${SITE}/treeroot/subether/css
	sudo ln -s ${PARENT}/treeroot/subether/functions ${SITE}/treeroot/subether/functions
	sudo ln -s ${PARENT}/treeroot/subether/gfx ${SITE}/treeroot/subether/gfx
	sudo ln -s ${PARENT}/treeroot/subether/include ${SITE}/treeroot/subether/include
	sudo ln -s ${PARENT}/treeroot/subether/javascript ${SITE}/treeroot/subether/javascript
	sudo ln -s ${PARENT}/treeroot/subether/locale ${SITE}/treeroot/subether/locale
	sudo ln -s ${PARENT}/treeroot/subether/modules ${SITE}/treeroot/subether/modules
	sudo ln -s ${PARENT}/treeroot/subether/plugins ${SITE}/treeroot/subether/plugins
	sudo ln -s ${PARENT}/treeroot/subether/restapi ${SITE}/treeroot/subether/restapi
	sudo ln -s ${PARENT}/treeroot/subether/themes ${SITE}/treeroot/subether/themes
	sudo ln -s ${PARENT}/treeroot/subether/thirdparty ${SITE}/treeroot/subether/thirdparty
	
	sudo ln -s ${PARENT}/treeroot/subether/install.php ${SITE}/treeroot/subether/install.php
	
else
	
	# Do the update for treeroot
	
	echo "Updating "${SITE}"/treeroot"
	
	sudo rsync -rav --exclude='.git/' --exclude=upload ${PARENT}/treeroot/subether/* ${SITE}/treeroot/subether/ > /dev/null
	sudo rsync -rav --exclude='.git/' ${PARENT}/treeroot/extensions/* ${SITE}/treeroot/extensions/ > /dev/null
	
	sudo rsync -rav --exclude='.git/' ${PARENT}/arena2/admin/* ${SITE}/treeroot/admin/ > /dev/null
	sudo rsync -rav --exclude='.git/' ${PARENT}/arena2/friend/* ${SITE}/treeroot/friend/ > /dev/null
	sudo rsync -rav --exclude='.git/' ${PARENT}/arena2/lib/* ${SITE}/treeroot/lib/ > /dev/null
	sudo rsync -rav --exclude='.git/' ${PARENT}/arena2/web/* ${SITE}/treeroot/web/ > /dev/null
	
	echo "Setting privileges"
	
	sudo chmod -R 777 ${SITE}/treeroot/upload/
	sudo chmod -R 777 ${SITE}/treeroot/subether/upload/
	sudo chown -R www-data.www-data ${SITE}/treeroot/
	
fi

# --- STEP 5: --------------------------------------------------------------------

echo "Done\n"

