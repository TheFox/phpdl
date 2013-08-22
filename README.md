# PHPDL
PHP Downloader (or PHPDL) is a webbased download manager for rapidshare.com links (or similar). You can install PHPDL on your webserver and manage the downloads over the web interface. In the background there is a other script running which is downloading the files.

## Features
- Web interface. Manage your downloads from everywhere.
- Multiuser.
- Traffic statistics.
- Packet speed limit.
- Export packet informations as txt and xml.
- RSDF files support.
- DLC files support.
- RapidShare.com (Free and Pro) support.
- Download files which are not hostet by one-click-hoster.

## Install
1. `git clone git://github.com/TheFox/phpdl.git`
1. `cd phpdl`
1. `./install/install.sh`
1. Run `install/install.php` in your browser.
1. Change the mode for file `lib/config.php` to 644 (rw-r--r--).
1. Change the mode for directory `install` to 755 (rwxr-xr-x).
1. Run `./stackstart` in your shell. `stack.php` must always run.

## Security warning
The passwords for the hosters are stored in the `hoster` table. If you don't want to show other users your one click hoster password, don't enter it in the hosters table!

## Libraries
Created with help from many libraries and frameworks including:

- [jQuery](http://jquery.com/)
	- [jQuery UI](http://jqueryui.com/)
	- [BeautyTips](http://plugins.jquery.com/project/bt)
- [Smarty](http://www.smarty.net/)
