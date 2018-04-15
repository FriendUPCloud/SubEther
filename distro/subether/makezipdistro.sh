#!/bin/sh

# Create a distribution

SUBETHER="subether`cat version.txt`"

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
cp logo_white.png release.treeroot/upload/images-master/
cp logo_symbol_white.png release.treeroot/upload/images-master/
cp logo_black.png release.treeroot/upload/images-master/
cp logo_symbol_black.png release.treeroot/upload/images-master/
cp robots.txt release.treeroot/
cp favicon.ico release.treeroot/
cp README.txt release.treeroot/subether/
cp AGPLv3.txt release.treeroot/subether/
cp version.txt release.treeroot/subether/
cp info.txt release.treeroot/subether/
cp verification.txt release.treeroot/subether/
cp defaultdb.sql release.treeroot/subether/
cp arenadefault.sql release.treeroot/subether/
cp standardemail.php release.treeroot/subether/templates/

cp -r ../../../ArenaCM/admin release.treeroot/
cp -r ../../../ArenaCM/lib release.treeroot/
cp -r ../../../ArenaCM/web release.treeroot/
cp -r ../../../ArenaCM/extensions release.treeroot/
cp -r ../../../ArenaCM/friend release.treeroot/
cp ../../../ArenaCM/index.php release.treeroot/
cp ../../../ArenaCM/admin.php release.treeroot/
cp ../../../ArenaCM/config.php.example release.treeroot/
cp ../../../ArenaCM/MPL.txt release.treeroot/
cp ../../../ArenaCM/README release.treeroot/

cp -d ../../../ArenaCM/.htaccess release.treeroot/
ln -s ../../../ArenaCM/lib/htaccess release.treeroot/.htaccess

cd release.treeroot

zip -qr ../../../../${SUBETHER}.zip *
rm -fr ../release.treeroot
