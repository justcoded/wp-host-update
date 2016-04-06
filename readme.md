# WordPress Database migration (string replacement) script
---
When you move WordPress to another host you usually need to update database with new URLs inside a lot of pages. Such URLs can be placed inside serialized strings, BLOBs or objects. 
This is a tool to simplify string replace operations across the whole WordPress database or specific tables. 

#### Script usage

* Download latest stable tag.
* Find the file `/build/wp-host-update.php` and copy it to your folder with WordPress installation.
* **Backup your database!**
* Open in your browser `http://domain.com/optional/path/to/wordpress/wp-host-update.php` (replace with your WordPress URL at the beginning)
* Follow the instructions in your browser.
* If everything was fine - remove script from production server!

(In case of fail - use your backup to restore the database and try one more time)

#### Repository structure

Repository has 2 parts:

1. The script source files inside `/app/` folder. These super simple MVC/OOP scripts which process page requests and views render.
2. In the root folder you can find `builder.php` file. This one is used to collect all "app" into one single script and compress it a bit to minimize file size.
