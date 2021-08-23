Mroonga search (module for Omeka S)
===================================

Mroonga search is a module for [Omeka S](https://omeka.org/s/) that enables
CJK-ready full-text search by activating the [Mroonga](https://mroonga.org/)
plugin of MySQL or MariaDB.

The default installation of the full-text search feature of the Omeka-S is not
CJK (Chinese, Japanese, Korean) ready because of the limiation of the database
engine (MySQL or MariaDB). The Mroonga plugin extend the database to achieve
CJK-ready search. This module simply activates this plugin by modifying the
table information that internally used by Omeka-S.


Installation
------------

### Preparation

First of all, **backup the database**. This module modifies the table schema,
and that may cause unrecoverable failure.

Before installing this module, install and configure the Mroonga plugin to
enable Mroonga storage engine. For example, if you use MariaDB on Debian or
Ubuntu machine, install 'mariadb-plugin-mroonga' package. Please read the
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

Please do not forget to rename the directory from `Omeka-S-mroonga-search` to
`MroongaSearch` in the `modules` directory.

### Configuration

No configuration is needed. Once installed, the database will be updated,
enabling full-text search.

### Uninstall

Simply uninstall this module to remove Mroonga settings from your database.
No additional work is needed.


Notes
-----

This module highly depends on the database structure of Omeka-S 3.x. If you are
upgrading Omeka-S from 3.x to 4.x or later, we highly recommend you to
uninstall this module **before upgrading**.

We have not heavily tested Mroonga engine with large sized data yet. For
advanced full-text search, we recommend you to check the
[Solr module](https://omeka.org/s/modules/Solr/).

Currently, this module uses the default N-gram parser. MeCab or any parsers are not supported yet.

### Technical note

MroongaSearch module changes the storage engine of the fulltext\_search table of your Omeka S instance from InnoDB to Mroonga. This enables CJK-friendly fast full-text search, while it increases the size of the database.


TODOs
-----

* Enabling synonyms


Licensing information
---------------------

Copyright (c) 2020, 2021 Kentaro Fukuchi

This module is released under the MIT License. See the `LICENSE` file for the
details.
