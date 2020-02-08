Mroonga search (module for Omeka S)
===================================

Mroonga search is a module for [Omeka S](https://omeka.org/s/) that enables
CJK-ready full-text search by activating the [Mroonga](https://mroonga.org/)
plugin of MySQL or MariaDB.

Installation
------------

Before installing this module, install and configure to enable Mroonga storage
engine. For example, if you use MariaDB on Debian or Ubuntu machine, install
'mariadb-plugin-mroonga' package. Please read the
[official document](https://mroonga.org/docs/install.html) for further
information.

### From ZIP

See the [release page](https://github.com/fukuchi/Omeka-S-module-mroonga-search/releases)
and download the latest `MroongaSearch.zip` from the list. Then unzip it in the
`modules` directory of Omeka-S, then enable the module from the admin
dashboard. Read the
[user manual of Omeka-S](https://omeka.org/s/docs/user-manual/modules/)
for further information.

### From GitHub

Please do not forget to rename the installed directory from
`Omeka-S-mroonga-search` to `MroongaSearch` in the `modules` directory.

### Re-index

After the installation, you are required to re-index the search index from
the 'Settings' menu of the admin dashboard.


Notes
-----

This module highly depends on the database structure of Omeka-S 2.x. If you are
upgrading Omeka-S from 2.x to 3.x or later, we highly recommend you to
uninstall this module **before upgrading**.


ToDo
----

Currently, this module uses the default N-gram parser. MeCab or any parsers are not supported yet.
