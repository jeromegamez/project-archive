# Guzzle Fixture Plugin

[![Latest Stable Version](https://poser.pugx.org/jeromegamez/guzzle-plugin-fixture/v/stable.png)](https://packagist.org/packages/jeromegamez/guzzle-plugin-fixture)
[![Code Coverage](https://scrutinizer-ci.com/g/jeromegamez/guzzle-plugin-fixture/badges/coverage.png?s=b4bafd6548448c4979f8369715deeb948ecc839f)](https://scrutinizer-ci.com/g/jeromegamez/guzzle-plugin-fixture/)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/jeromegamez/guzzle-plugin-fixture/badges/quality-score.png?s=c0f8d4ec45ea08ab27c7b934fa03c10a0d9646a2)](https://scrutinizer-ci.com/g/jeromegamez/guzzle-plugin-fixture/)
[![Build Status](https://secure.travis-ci.org/jeromegamez/guzzle-plugin-fixture.png?branch=master)](http://travis-ci.org/jeromegamez/guzzle-plugin-fixture)

A [Guzzle](http://guzzle.readthedocs.org/) plugin for automatic mock response/fixture creation and retrieval.

From the [Guzzle Documentation](http://guzzle.readthedocs.org/en/latest/testing/unit-testing.html#queueing-mock-responses):

> Mock responses can be used to test if requests are being generated correctly and responses and handled correctly by your client.

But creating these mock responses/fixtures can be tedious, if you need many of them.

This Guzzle Fixture Plugin allows you to create the fixtures **with** your unit tests. The first time you execute your
tests, real requests will be made to the remote endpoints and the results will be stored to a defined directory.

The second time you execute your test suite, the contents of the previously generated fixture files will be taken,
without the need for remote connections.

You can then commit the fixtures to your repository and ensure that no remote connection is needed to run your tests.

The versioning of this plugin is geared to the versioning of the main Guzzle project; thus ~3.8 fits Guzzle ~3.8.

## Installation

The recommended way to install the Guzzle Fixture Plugin is with [Composer](http://getcomposer.org).
Composer is a dependency management tool for PHP that allows you to declare the dependencies your
project needs and installs them into your project.

```bash
# Install Composer
curl -sS https://getcomposer.org/installer | php

# Add guzzle-plugin-fixture as a dependency
php composer.phar require jeromegamez/guzzle-plugin-fixture:~3.8
```

After installing, you need to require Composer's autoloader:

```php
require 'vendor/autoload.php';
```

You can find out more on how to install Composer, configure autoloading, and other best-practices
for defining dependencies at [getcomposer.org](http://getcomposer.org).

## Usage

```php
use Guzzle\Http\Client;
use Guzzle\Plugin\Fixture\FixturePlugin;

$fixturePlugin = new FixturePlugin('_fixtures');

$client = new Client();
$client->addSubscriber($fixturePlugin);

// The following request will perform a real request and store the response to
// the fixtures dir
$client->get('http://www.example.com/')->send();

// The following request will get the response from the previously created
// fixture file and not perform a real request
$client->get('http://www.example.com/')->send();
```
