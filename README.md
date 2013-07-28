virtuoso-tools
==============

This package **is for you** if you need to [administer the server](http://docs.openlinksw.com/virtuoso/functions.html#admin "24. Virtuoso Functions Guide"), [bulk-load](http://virtuoso.openlinksw.com/dataspace/doc/dav/wiki/Main/VirtBulkRDFLoader "Virtuoso Open-Source Wiki : Bulk Loading RDF Source Files into one or more Graph IRIs") large amounts of data, etc., but you don't want to mess with Virtuoso/PL syntax.

This package **is not for you** if you just need to store and retrieve data from triplestore. Virtuoso is [SPARQL](http://www.w3.org/TR/2013/REC-sparql11-overview-20130321/ "SPARQL 1.1 Overview")-compliant and you can use [EasyRDF](http://www.easyrdf.org/ "EasyRdf - RDF Library for PHP") or [any other](https://packagist.org/search/?q=sparql) client.

## Requirements

* PHP 5.4+
* [PDO-ODBC](http://docs.php.net/pdo-odbc "PHP: ODBC and DB2 (PDO) - Manual") extension

## Installation

The recommended way to install virtuoso-tools is [through composer](http://getcomposer.org). Just create a `composer.json` file and
run the `php composer.phar install` command to install it:

    {
        "require": {
            "gridsby/virtuoso-tools": "dev-master"
        }
    }

## Usage

Type `./vendor/bin/virtuoso-import list` to get list of supported commands.

Documentation on **usage of tools** and **programmatic usage of library** is coming soon.

## License

virtuoso-tools is licensed under the [MIT license](LICENSE).
