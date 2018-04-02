#!/bin/sh

# PROJECT LOCATION

PROJECT=$(pwd)

SUBETHER="SubEther"
ARENA="ArenaCM"

# PARENT LOCATION

PARENT=$(dirname "$PROJECT")

# DISTRO ENTERPRISE / OPEN SOURCE

DISTRO="treeroot"

# --- STEP 1: ---------------------------------------------------------------------

#git clone https://github.com/FriendUPCloud/ArenaCM
#git clone https://github.com/FriendUPCloud/SubEther

# --- STEP 2: ---------------------------------------------------------------------

if [ ! -d ${PARENT}"/"${ARENA} ]; then

	echo ${PARENT}"/"${ARENA}" doesn't exist, copy/clone "${ARENA}" repo first, aborting installation."
	exit 1

fi

if [ ! -d ${PARENT}"/"${SUBETHER} ]; then

	echo ${PARENT}"/"${SUBETHER}" doesn't exist, copy/clone "${SUBETHER}" repo first, aborting installation."
	exit 1

fi

if [ ! -d ${PARENT}"/"${SUBETHER}"/distro/"${DISTRO} ]; then

	DISTRO="subether"
	
else 
	
	read -p "Enterprise (Treeroot) distro found, install this instead of the Open Source (SubEther) distro (y/n) ? " CHOOSE_DISTRO
	
	if [ "$CHOOSE_DISTRO" = "n" ]; then
		
		DISTRO="subether"
	
	fi
	
fi

if [ ! -d ${PARENT}"/"${SUBETHER}"/distro/"${DISTRO} ]; then
		
	echo ${PARENT}"/"${SUBETHER}"/distro/"${DISTRO}" doesn't exist, contact support, aborting installation."
	exit 1
	
fi

# --- STEP 3: --------------------------------------------------------------------

cd ${PROJECT}/distro/${DISTRO}/ && sh makezipdistro.sh

# --- STEP 4: --------------------------------------------------------------------

echo "cd "${PARENT}" && ls -all"

