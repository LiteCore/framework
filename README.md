# LiteCore

LiteCore is a lightweight PHP framework that is the base of the e-commerce platform [LiteCart®](http://www.litecart.net/). Developed in PHP, HTML 5, and CSS 3 by founder and property owner [T. Almroth](https://www.github.com/timint).

**[Download Now](https://github.com/litecart/litecore/archive/refs/heads/master.zip)**


## How To Install

Please note running your own website requires some common sense of web knowledge. If this is not your area of expertise, ask a friend or collegue to assist you.

What you need:

  * A storage location on an Apache2 web server running PHP 5.6 or higher. Latest stable PHP release recommended for best performance.
  * A MySQL 5.7+ account.

Here is what you do:

1. Connect to your web host via FTP using your favourite FTP software.

2. Transfer the contents of the folder public_html/ in this archive (yes the contents inside the folder - not the folder itself). Transfer it to your website root directory. Using subdirectories is supported but not recommended.

    Example:

    /var/www/

    /home/username/public_html/

    C:\xampp\htdocs\

Paths are machine specific. Talk to your web host if you are uncertain where this folder is.

3. Create a new MySQL database and import structure.sql and data.sql using your favourite MySQL manager e.g. phpMyAdmin.

4. Go to phpMyAdmin and import the files install/structure.sql and then install/data.sql into your database.

5. Copy the configuration file install/public_html/storage/config.inc.php to your storage/ folder and insert your MySQL credentials.

If everything went well LiteCore should load without errors.


## Support

No support is being provided at this point in time. But some [wiki articles](https://www.litecart.net/wiki/) by the LiteCart community can be useful.
