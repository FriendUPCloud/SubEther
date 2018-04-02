#!/bin/sh

# Update a local treeroot node with symlinks for developers

# --- SETINGS: --------------------------------------------------------------------

# PROJECT LOCATION

PROJECT=$(pwd)

SUBETHER="SubEther"
ARENA="ArenaCM"

# PARENT LOCATION

PARENT=$(dirname "$PROJECT")

# APACHE2 CONFIG LOCATION

CONFIG="/etc/apache2/sites-enabled/000-default.conf"

# USE SYMLINKS

SYMLINKS="0"

# SITE LOCATION EX

SITE="/var/www/html"

BASE_DIR=${SITE}"/"${SUBETHER}

# --- STEP 1: ---------------------------------------------------------------------

sudo apt-get install dialog

if [ ! -d ${PARENT}"/"${ARENA} ]; then

	echo ${PARENT}"/"${ARENA}" doesn't exist, copy/clone "${ARENA}" repo first, aborting update."
	exit 1

fi

if [ ! -d ${PARENT}"/"${SUBETHER} ]; then

	echo ${PARENT}"/"${SUBETHER}" doesn't exist, copy/clone "${SUBETHER}" repo first, aborting update."
	exit 1

fi

sudo apt-get update

if [ ! -d ${SITE} ]; then
	
	echo ${SITE}" doesn't exist, aborting update."
	exit 1

fi

if [ ! -d ${SITE}"/"${SUBETHER} ]; then

	echo ${SITE}"/"${SUBETHER}" doesn't exist, aborting update."
	exit 1

fi



read -p "Do you want to use symlinks to link from "${PARENT}"/"${ARENA}" and "${PARENT}"/"${SUBETHER}" to "${SITE}"/"${SUBETHER}" (y/n) ? " USE_SYMLINKS

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

read -p "Do you want to take backup of "${SITE}"/"${SUBETHER}" and store it in ${PARENT}/backup."${SUBETHER}" (y/n) ? " BACKUP

if [ "$BACKUP" = "y" ]; then
	
	echo "Taking backup of "${SITE}"/"${SUBETHER}
	
	sudo rsync -ravL ${SITE}/${SUBETHER}/subether ${PARENT}/backup.${SUBETHER}/ > /dev/null
	sudo rsync -ravL ${SITE}/${SUBETHER}/extensions ${PARENT}/backup.${SUBETHER}/ > /dev/null
	
	sudo rsync -ravL ${SITE}/${SUBETHER}/admin ${PARENT}/backup.${SUBETHER}/ > /dev/null
	sudo rsync -ravL ${SITE}/${SUBETHER}/friend ${PARENT}/backup.${SUBETHER}/ > /dev/null
	sudo rsync -ravL ${SITE}/${SUBETHER}/lib ${PARENT}/backup.${SUBETHER}/ > /dev/null
	sudo rsync -ravL ${SITE}/${SUBETHER}/web ${PARENT}/backup.${SUBETHER}/ > /dev/null
	sudo rsync -ravL ${SITE}/${SUBETHER}/upload ${PARENT}/backup.${SUBETHER}/ > /dev/null
	
fi

# --- STEP 3: --------------------------------------------------------------------

if [ "$SYMLINKS" = "yes" ]; then
	
	# TODO: Add .gitignore or something to the repo so you don't have to do this for lib/ and subether/upload/
	
	echo "Removing folders and files in "${SITE}"/"${SUBETHER}" that will be symlinked"
	
	sudo rm -rf ${SITE}/${SUBETHER}/admin
	sudo rm -rf ${SITE}/${SUBETHER}/friend
	sudo rm -rf ${SITE}/${SUBETHER}/web
	sudo rm -rf ${SITE}/${SUBETHER}/extensions/easyeditor
	sudo rm -rf ${SITE}/${SUBETHER}/extensions/editor
	sudo rm -rf ${SITE}/${SUBETHER}/extensions/userlogin
	
	sudo rm -rf ${SITE}/${SUBETHER}/lib/3rdparty
	sudo rm -rf ${SITE}/${SUBETHER}/lib/classes
	#sudo rm -rf ${SITE}/${SUBETHER}/lib/enterprise
	sudo rm -rf ${SITE}/${SUBETHER}/lib/fonts
	sudo rm -rf ${SITE}/${SUBETHER}/lib/functions
	sudo rm -rf ${SITE}/${SUBETHER}/lib/help
	sudo rm -rf ${SITE}/${SUBETHER}/lib/icons
	sudo rm -rf ${SITE}/${SUBETHER}/lib/include
	sudo rm -rf ${SITE}/${SUBETHER}/lib/javascript
	sudo rm -rf ${SITE}/${SUBETHER}/lib/locale
	sudo rm -rf ${SITE}/${SUBETHER}/lib/plugins
	sudo rm -rf ${SITE}/${SUBETHER}/lib/skeleton
	sudo rm -rf ${SITE}/${SUBETHER}/lib/templates
	sudo rm -rf ${SITE}/${SUBETHER}/lib/themes
	
	sudo rm -f ${SITE}/${SUBETHER}/lib/core_config.php.example
	sudo rm -f ${SITE}/${SUBETHER}/lib/error.html
	sudo rm -f ${SITE}/${SUBETHER}/lib/error.jpg
	sudo rm -f ${SITE}/${SUBETHER}/lib/htaccess
	sudo rm -f ${SITE}/${SUBETHER}/lib/index.html
	sudo rm -f ${SITE}/${SUBETHER}/lib/install.php
	sudo rm -f ${SITE}/${SUBETHER}/lib/lib.php
	sudo rm -f ${SITE}/${SUBETHER}/lib/resources.php
	
	sudo rm -rf ${SITE}/${SUBETHER}/upload/template
	
	sudo rm -rf ${SITE}/${SUBETHER}/extensions/sbook
	sudo rm -rf ${SITE}/${SUBETHER}/extensions/templates
	sudo rm -rf ${SITE}/${SUBETHER}/subether/applications
	sudo rm -rf ${SITE}/${SUBETHER}/subether/classes
	sudo rm -rf ${SITE}/${SUBETHER}/subether/components
	sudo rm -rf ${SITE}/${SUBETHER}/subether/css
	sudo rm -rf ${SITE}/${SUBETHER}/subether/functions
	sudo rm -rf ${SITE}/${SUBETHER}/subether/gfx
	sudo rm -rf ${SITE}/${SUBETHER}/subether/include
	sudo rm -rf ${SITE}/${SUBETHER}/subether/javascript
	sudo rm -rf ${SITE}/${SUBETHER}/subether/locale
	sudo rm -rf ${SITE}/${SUBETHER}/subether/modules
	sudo rm -rf ${SITE}/${SUBETHER}/subether/plugins
	sudo rm -rf ${SITE}/${SUBETHER}/subether/restapi
	sudo rm -rf ${SITE}/${SUBETHER}/subether/themes
	sudo rm -rf ${SITE}/${SUBETHER}/subether/thirdparty
	
	sudo rm -f ${SITE}/${SUBETHER}/subether/install.php
	
fi

# --- STEP 4: --------------------------------------------------------------------

if [ "$SYMLINKS" = "yes" ]; then
	
	# TODO: Add .gitignore or something to the repo so you don't have to do this for lib/ and subether/upload/
	
	echo "Adding symlinks from "${PARENT}"/"${ARENA}" to "${SITE}"/"${SUBETHER}
	echo "Adding symlinks from "${PARENT}"/"${SUBETHER}" to "${SITE}"/"${SUBETHER}
	
	sudo ln -s ${PARENT}/${ARENA}/admin ${SITE}/${SUBETHER}/admin
	sudo ln -s ${PARENT}/${ARENA}/friend ${SITE}/${SUBETHER}/friend
	sudo ln -s ${PARENT}/${ARENA}/web ${SITE}/${SUBETHER}/web
	sudo ln -s ${PARENT}/${ARENA}/extensions/easyeditor ${SITE}/${SUBETHER}/extensions/easyeditor
	sudo ln -s ${PARENT}/${ARENA}/extensions/editor ${SITE}/${SUBETHER}/extensions/editor
	sudo ln -s ${PARENT}/${ARENA}/extensions/userlogin ${SITE}/${SUBETHER}/extensions/userlogin
	
	sudo ln -s ${PARENT}/${ARENA}/lib/3rdparty ${SITE}/${SUBETHER}/lib/3rdparty
	sudo ln -s ${PARENT}/${ARENA}/lib/classes ${SITE}/${SUBETHER}/lib/classes
	#sudo ln -s ${PARENT}/${ARENA}/lib/enterprise ${SITE}/${SUBETHER}/lib/enterprise
	sudo ln -s ${PARENT}/${ARENA}/lib/fonts ${SITE}/${SUBETHER}/lib/fonts
	sudo ln -s ${PARENT}/${ARENA}/lib/functions ${SITE}/${SUBETHER}/lib/functions
	sudo ln -s ${PARENT}/${ARENA}/lib/help ${SITE}/${SUBETHER}/lib/help
	sudo ln -s ${PARENT}/${ARENA}/lib/icons ${SITE}/${SUBETHER}/lib/icons
	sudo ln -s ${PARENT}/${ARENA}/lib/include ${SITE}/${SUBETHER}/lib/include
	sudo ln -s ${PARENT}/${ARENA}/lib/javascript ${SITE}/${SUBETHER}/lib/javascript
	sudo ln -s ${PARENT}/${ARENA}/lib/locale ${SITE}/${SUBETHER}/lib/locale
	sudo ln -s ${PARENT}/${ARENA}/lib/plugins ${SITE}/${SUBETHER}/lib/plugins
	sudo ln -s ${PARENT}/${ARENA}/lib/skeleton ${SITE}/${SUBETHER}/lib/skeleton
	sudo ln -s ${PARENT}/${ARENA}/lib/templates ${SITE}/${SUBETHER}/lib/templates
	sudo ln -s ${PARENT}/${ARENA}/lib/themes ${SITE}/${SUBETHER}/lib/themes
	
	sudo ln -s ${PARENT}/${ARENA}/lib/core_config.php.example ${SITE}/${SUBETHER}/lib/core_config.php.example
	sudo ln -s ${PARENT}/${ARENA}/lib/error.html ${SITE}/${SUBETHER}/lib/error.html
	sudo ln -s ${PARENT}/${ARENA}/lib/error.jpg ${SITE}/${SUBETHER}/lib/error.jpg
	sudo ln -s ${PARENT}/${ARENA}/lib/htaccess ${SITE}/${SUBETHER}/lib/htaccess
	sudo ln -s ${PARENT}/${ARENA}/lib/index.html ${SITE}/${SUBETHER}/lib/index.html
	sudo ln -s ${PARENT}/${ARENA}/lib/install.php ${SITE}/${SUBETHER}/lib/install.php
	sudo ln -s ${PARENT}/${ARENA}/lib/lib.php ${SITE}/${SUBETHER}/lib/lib.php
	sudo ln -s ${PARENT}/${ARENA}/lib/resources.php ${SITE}/${SUBETHER}/lib/resources.php
	
	sudo ln -s ${PARENT}/${SUBETHER}/upload/template ${SITE}/${SUBETHER}/upload/template
	
	sudo ln -s ${PARENT}/${SUBETHER}/extensions/sbook ${SITE}/${SUBETHER}/extensions/sbook
	sudo ln -s ${PARENT}/${SUBETHER}/extensions/templates ${SITE}/${SUBETHER}/extensions/templates
	sudo ln -s ${PARENT}/${SUBETHER}/subether/applications ${SITE}/${SUBETHER}/subether/applications
	sudo ln -s ${PARENT}/${SUBETHER}/subether/classes ${SITE}/${SUBETHER}/subether/classes
	sudo ln -s ${PARENT}/${SUBETHER}/subether/components ${SITE}/${SUBETHER}/subether/components
	sudo ln -s ${PARENT}/${SUBETHER}/subether/css ${SITE}/${SUBETHER}/subether/css
	sudo ln -s ${PARENT}/${SUBETHER}/subether/functions ${SITE}/${SUBETHER}/subether/functions
	sudo ln -s ${PARENT}/${SUBETHER}/subether/gfx ${SITE}/${SUBETHER}/subether/gfx
	sudo ln -s ${PARENT}/${SUBETHER}/subether/include ${SITE}/${SUBETHER}/subether/include
	sudo ln -s ${PARENT}/${SUBETHER}/subether/javascript ${SITE}/${SUBETHER}/subether/javascript
	sudo ln -s ${PARENT}/${SUBETHER}/subether/locale ${SITE}/${SUBETHER}/subether/locale
	sudo ln -s ${PARENT}/${SUBETHER}/subether/modules ${SITE}/${SUBETHER}/subether/modules
	sudo ln -s ${PARENT}/${SUBETHER}/subether/plugins ${SITE}/${SUBETHER}/subether/plugins
	sudo ln -s ${PARENT}/${SUBETHER}/subether/restapi ${SITE}/${SUBETHER}/subether/restapi
	sudo ln -s ${PARENT}/${SUBETHER}/subether/themes ${SITE}/${SUBETHER}/subether/themes
	sudo ln -s ${PARENT}/${SUBETHER}/subether/thirdparty ${SITE}/${SUBETHER}/subether/thirdparty
	
	sudo ln -s ${PARENT}/${SUBETHER}/subether/install.php ${SITE}/${SUBETHER}/subether/install.php
	
else
	
	# Do the update for treeroot
	
	echo "Updating "${SITE}"/treeroot"
	
	sudo rsync -rav --exclude='.git/' --exclude=upload ${PARENT}/${SUBETHER}/subether/* ${SITE}/${SUBETHER}/subether/ > /dev/null
	sudo rsync -rav --exclude='.git/' ${PARENT}/${SUBETHER}/extensions/* ${SITE}/${SUBETHER}/extensions/ > /dev/null
	
	sudo rsync -rav --exclude='.git/' ${PARENT}/${ARENA}/admin/* ${SITE}/${SUBETHER}/admin/ > /dev/null
	sudo rsync -rav --exclude='.git/' ${PARENT}/${ARENA}/friend/* ${SITE}/${SUBETHER}/friend/ > /dev/null
	sudo rsync -rav --exclude='.git/' ${PARENT}/${ARENA}/lib/* ${SITE}/${SUBETHER}/lib/ > /dev/null
	sudo rsync -rav --exclude='.git/' ${PARENT}/${ARENA}/web/* ${SITE}/${SUBETHER}/web/ > /dev/null
	
	echo "Setting privileges"
	
	sudo chmod -R 777 ${SITE}/${SUBETHER}/upload/
	sudo chmod -R 777 ${SITE}/${SUBETHER}/subether/upload/
	sudo chown -R www-data.www-data ${SITE}/${SUBETHER}/
	
fi

# --- STEP 5: --------------------------------------------------------------------

echo "Done\n"

