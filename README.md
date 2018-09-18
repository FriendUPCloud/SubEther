SubEther - The Decentralized Network (version 1.0.0)
====================================================

![SubEther Home](treeroot.org/upload/subether-home.png "SubEther Home")

Vision
------

To provide tools needed for the user to gain free and secure access to others directly. Without the
censoring and filtering of centralized third party agents on matters like: Communications,
Information, Knowledge, Trading, Services, Politics and Transport.

SubEther
--------

The term "Sub-Ether" describes a space beyond the ether, the fundamental state of matter. It is the
fountain of consciousness - from where time and space is whirled into existence. As a super
highway of information, the term made perfect sense to describe a universal medium for
communication and creation – a sub reality, creating new universes inside of reality. SubEther is a
medium you connect with and gain access to the unlimited potential of the imagination.

Decentralization
----------------

Instead of everyone’s data being contained on huge central servers owned by large organizations,
local servers (“nodes”) can be set up anywhere in the world. You choose which node to register with
perhaps your local node - and seamlessly connect with the SubEther community world wide.

Freedom
-------

You can be whoever you want to be in SubEther. Unlike on some networks, you don’t have to use
your real identity. You can interact with whomever you choose in whatever way you want using one
or a set of avatars. SubEther is also Free Software for non commercial use, giving you liberty to use
it as you wish.

Privacy
-------

In SubEther you own your own data. You do not sign over any rights to a corporation or other
interests. With SubEther, your friends, your habits, and your content is your business ... not ours! In
addition, you choose who sees what you share, using permissions protected with strong encryption
like SHA256 + RSA1024 + AES256.

Getting started
===============

Prior to installation, check that 'bash', or a compatible shell is installed on your machine.

Just clone this and the ArenaCM repository, run the install.sh script and follow the on screen instructions. 
This script should run on most modern linux distributions. Post to the Developer Community if you run into any problems here.
```
git clone https://github.com/FriendUPCloud/ArenaCM
git clone https://github.com/FriendUPCloud/SubEther
cd SubEther/
sh install.sh
```
We recommend setting up a dedicated user for your SubEther installation. Other then that a standard webhotell with MySQL should also work fine. 

Dependencies
------------

The SubEther installer relies on the following packages to work, and must be present on the machine before starting an installation:

- bash
- sudo

If you encounter an error during the dependencies installation process, please refer to the end of this file for a complete list of the necessary dependencies, and install them manually. Then restart the installer.

Updating SubEther
-----------------

When you want to update SubEther after a git pull to get the newest changes (if you don't have everything allready symlinked), do this:
```
cd SubEther/
sh update.sh
```
SubEther installes default in /var/www/html/ and can be installed in any directory or moved wherever you wish. 
But the install and update script does not support other then default directory yet, 
so you would have to update SubEther manually if you choose another directory.

If you choose to do a manual update there is two current available methods:

1. Symlink from where you have installed ArenaCM and SubEther repo on the machine, for full list of current symlinks look in the update.sh file.
2. Other method is to create a zip file of the updates by using makezipupdate.sh and unzipping the files to your new directory:
```
cd SubEther/distro/subether/
sh makezipupdate.sh
cd ../../../
sudo unzip -q subether[UPDATE_VERSION].zip -d [YOUR_CUSTOM_PATH]/
```
Note: A third method of updating SubEther will be done through a virtual cronscript inside the system, 
much like the wordpress updating system from the backend, for ease of use in coming updates.

If you wish to move SubEther make sure these are correct:
- BaseUrl and BaseDir in the MySQL table called "Sites" (this will be dynamic in coming releases)
- Symlinks from SubEther and arenaCM repo to new path if your using symlinks
- Webserver (Apache2) config, check if the new path is in the config

Config files if manually edits are needed are located in your installed directory:
- /config.php
- /lib/core_config.php

Documentation
-------------

You can find the various developer documentation on any node by going to https://your-subether-node.example/developers/ after installation. 
Some more will be added soon.

Chat room
---------

You will find many of our developers and users on our Discord / IRC channel / chat room. Please choose a unique nick and join using the links below.

Discord: https://discord.gg/HQ93NFG 
IRC: https://developers.friendup.cloud/irc-channel/

Licensing
=========

licenses available from Friend Software Corporation; AGPLv3, Vendor License or Enterprise
License.

Developer Community
===================

We invite everybody to join our developer community node at https://friendup.world.

List of dependencies
====================

This is the list of dependencies.

- bash
- zip
- rsync
- apache2
- mysql-server
- libapache2-mod-php
- ffmpeg
- php5
- php5-mysql
- php5-gd
- php5-curl
- php5-xml

