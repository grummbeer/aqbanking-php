AqBankingPhp
============

A wrapper to use AqBanking CLI from a PHP context

Tested with AqBanking 6 which supports PSD2 conform banking using HBCI / FinTS 3.

Installation
------------

Install the library using [composer version 2][1]:

    composer require mestrona/aqbanking-php
    
[1]: http://getcomposer.org/

see also
--------

https://github.com/mestrona/mbank

Credits
-------

* aqbanking-php originally developed by [@janunger](https://github.com/janunger/)
* Thanks to the AqBanking Team

More Info
---------

* [How to setup AqBanking on a shared hosting](https://serverfault.com/questions/942701/how-to-install-a-binary-package-on-a-shared-hosting-for-example-aqbanking-or-m)

Dev Docs
--------

* [Setup PIN Tan Account in AqBanking (German)](https://www.aquamaniac.de/rdm/projects/aqbanking/wiki/SetupPinTan)

### Coding standards

Run the fixer before push.

```shell
./vendor/bin/ecs --fix
```
