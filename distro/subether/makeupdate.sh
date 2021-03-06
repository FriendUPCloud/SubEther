#!/bin/sh

# Create an update

SUBETHER="subether_update`cat version.txt`"

#php updatefiles.php > log.txt
mkdir release.treeroot

cp -r ../../extensions release.treeroot/
cp -r ../../subether release.treeroot/
cp -r ../../upload release.treeroot/
cp about.html release.treeroot/upload/
cp terms.html release.treeroot/upload/
cp copyright.html release.treeroot/upload/
cp advertising.html release.treeroot/upload/
cp privacy.html release.treeroot/upload/
cp policy.html release.treeroot/upload/
cp creators.html release.treeroot/upload/
cp developers.html release.treeroot/upload/
#cp logo_white.png release.treeroot/upload/images-master/
#cp logo_symbol_white.png release.treeroot/upload/images-master/
#cp logo_black.png release.treeroot/upload/images-master/
#cp logo_symbol_black.png release.treeroot/upload/images-master/
cp robots.txt release.treeroot/
#cp favicon.ico release.treeroot/
cp README.txt release.treeroot/subether/
#cp AGPLv3.txt release.treeroot/subether/
cp version.txt release.treeroot/subether/
cp info.txt release.treeroot/subether/
cp verification.txt release.treeroot/subether/
cp defaultdb.sql release.treeroot/subether/
cp arenadefault.sql release.treeroot/subether/
cp standardemail.php release.treeroot/subether/templates/

cd release.treeroot

tar -czf ../../../../${SUBETHER}.tgz treeroot *
rm -fr ../release.treeroot
