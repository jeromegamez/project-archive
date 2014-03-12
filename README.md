# Guzzle Fixture Plugin

[![Latest Stable Version](https://poser.pugx.org/jeromegamez/guzzle-plugin-fixture/v/stable.png)](https://packagist.org/packages/jeromegamez/guzzle-plugin-fixture)
[![Code Coverage](https://scrutinizer-ci.com/g/jeromegamez/guzzle-plugin-fixture/badges/coverage.png?s=b4bafd6548448c4979f8369715deeb948ecc839f)](https://scrutinizer-ci.com/g/jeromegamez/guzzle-plugin-fixture/)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/jeromegamez/guzzle-plugin-fixture/badges/quality-score.png?s=c0f8d4ec45ea08ab27c7b934fa03c10a0d9646a2)](https://scrutinizer-ci.com/g/jeromegamez/guzzle-plugin-fixture/)
[![Build Status](https://secure.travis-ci.org/jeromegamez/guzzle-plugin-fixture.png?branch=master)](http://travis-ci.org/jeromegamez/guzzle-plugin-fixture)

A [Guzzle](http://guzzle.readthedocs.org/) plugin for automatic fixture creation and retrieval.

## Installation

The recommended way to install the Guzzle Mock Plugin is with [Composer](http://getcomposer.org).
Composer is a dependency management tool for PHP that allows you to declare the dependencies your
project needs and installs them into your project.

```bash
# Install Composer
curl -sS https://getcomposer.org/installer | php

# Add guzzle-mock-plugin as a dependency
php composer.phar require jeromegamez/guzzle-mock-plugin:~3.8
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
