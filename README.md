<!--
Created @ 15.10.2010 by TheFox@fox21.at
Translated to Markdown @ 11.03.2012 by TheFox@fox21.at
//-->

# PHP Downloader

## Description
*PHP Downloader* (or *PHPDL*) is a webbased download manager for [rapidshare.com](http://rapidshare.com) links (or similar). You can install PHPDL on your webserver and manage the downloads over the web interface. In the background there is a other script running which is downloading the files.

## Features
* Web interface. Manage your downloads from everywhere.
* Multiuser.
* Traffic statistics.
* Scheduler.
* Hooks.
* Export packet informations as txt and xml.
* **RSDF** files support.
* **DLC** files support.
* RapidShare.com (Free and Pro) files support.
* Download files which are not hostet by one-click-hoster.

## Install
1. Execute `git clone git://github.com/TheFox/phpdl.git phpdl && cd phpdl && ./install/install.sh` in your shell.
1. Run `install/install.php` in your browser.
1. Change the mode for file *lib/config.php* to 644 (rw-r--r--).
1. Change the mode for directory *install* to 755 (rwxr-xr-x).
1. Run `./stackstart` in your terminal. stack.php must always run.

## Security warning
The passwords for the hosters are stored in the *hoster* table in plaintext. **If you don't want to show other users your one click hoster password, don't enter it in the hosters table!**

## Created
by [TheFox] (<http://fox21.at>), with help from many libraries and frameworks including:

1. [jQuery]
	* [jQuery UI]
	* [BeautyTips]
	* [Timepicker]
	* [Gritter]
1. [Smarty]

[TheFox]: http://fox21.at/
[jQuery]: http://jquery.com/
[jQuery UI]: http://jqueryui.com/
[BeautyTips]: http://plugins.jquery.com/project/bt
[Timepicker]: http://jonthornton.github.com/jquery-timepicker/
[Gritter]: https://github.com/jboesch/Gritter
[Smarty]: http://www.smarty.net/

<!-- EOF //-->
