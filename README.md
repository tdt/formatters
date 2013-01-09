# formatters

Formats PHP arrays, PHP objects and RDF triples in a lot of formats. We serve all.

   Caveat lector: it loads all objects in memory and does not support streaming. This means the only acceptable objects/arrays/triplegraphs are not much more than 1MB.

## Request for pull requests

Is a formatter lacking? Does a formatter contain a mistake? Don't hesitate to fork and file pull requests!

## install

Include the formatters in your project by creating a composer.json file with a requirement: "tdt/formatters"=>"dev-master". Then do:

``` bash
$ curl -s http://getcomposer.org/installer | php
$ php composer.phar install
```

## usage

Check the example directory