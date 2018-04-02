#!/bin/sh

# PROJECT LOCATION

PROJECT=$(pwd)

# PARENT LOCATION

PARENT=$(dirname "$PROJECT")

# DISTRO ENTERPRISE / OPEN SOURCE

DISTRO="treeroot"

# --- STEP 1: ---------------------------------------------------------------------

#git clone git@pal.ideverket.no:/home/git/arena2
#git clone git@pal.ideverket.no:/home/git/treeroot

# --- STEP 2: ---------------------------------------------------------------------

if [ ! -d ${PARENT}"/arena2" ]; then

	echo ${PARENT}"/arena2 doesn't exist, copy/clone arena2 repo first, aborting installation."
	exit 1

fi

if [ ! -d ${PARENT}"/treeroot" ]; then

	echo ${PARENT}"/treeroot doesn't exist, copy/clone treeroot repo first, aborting installation."
	exit 1

fi

if [ ! -d ${PARENT}"/treeroot/distro/"${DISTRO} ]; then

	DISTRO="subether"
	
else 
	
	read -p "Enterprise (Treeroot) distro found, install this instead of the Open Source (SubEther) distro (y/n) ? " CHOOSE_DISTRO
	
	if [ "$CHOOSE_DISTRO" = "n" ]; then
		
		DISTRO="subether"
	
	fi
	
fi

if [ ! -d ${PARENT}"/treeroot/distro/"${DISTRO} ]; then
		
	echo ${PARENT}"/treeroot/distro/"${DISTRO}" doesn't exist, contact support, aborting installation."
	exit 1
	
fi

# --- STEP 3: --------------------------------------------------------------------

cd ${PROJECT}/distro/${DISTRO}/ && sh makezipdistro.sh

# --- STEP 4: --------------------------------------------------------------------

echo "cd "${PARENT}" && ls -all"

