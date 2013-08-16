# eZ Publish Legacy Tests

Execute PHPUnit tests for eZ Publish Legacy, optionally with one or more 
activated extensions.

## How it works

With [Phing](http://www.phing.info) a clean eZ Publish installation is
generated and PHPUnit tests are perfomed.

### Details 

- The [eZ Publish legacy](https://github.com/ezsystems/ezpublish-legacy)
  repository and all testable extensions get cloned/pulled into the `cache`
  directory

- The eZ Publish core files and the extensions get copied into the `build`
  directory.

- The extensions are activated and the PHPUnit tests for the whole
  installation are started

## Build status [![Build Status](https://travis-ci.org/jeromegamez/ezpublish-legacy-tests.png)](https://travis-ci.org/jeromegamez/ezpublish-legacy-tests)

When the build is marked as failed, this means that one of the extension's
unit tests did not pass.

Unfortunately, builds can not be triggered automatically as soon as one
of ezsystems' repositories is updated. So the Travis CI build status only
gets updated when triggered through a commit here.

You can always clone this repository and perform the tests on your machine.

## Testable extensions

- ezformtoken (included in eZ Publish LS)
- ezjscore (included in eZ Publish LS)
- ezoe (included in eZ Publish LS)
- ezcomments
- ezprestapiprovider

## Requirements

### Phing

```
pear channel-discover pear.phing.info
pear install phing/phing
```

### PEAR/VersionControl_Git

```
pear install VersionControl_Git-0.4.4
```

### MySQL and a test database

Out of the box, the script expects a MySQL server instance at `127.0.0.1`, a database named `ezpublish_test`,
and user `root` with no password. If you want to override those values, copy the file `config/config.properties` to
`config/config_local.properties` and change the variables.


## Installation

Clone this repository

```
git clone https://github.com/jeromegamez/ezpublish-legacy-tests.git
```

## Usage

### eZ Publish Legacy standalone

```
phing test
```

### eZ Publish Legacy with one extension

```
phing test -Dextensions=ezprestapiprovider
```

### eZ Publish Legacy with multiple extensions

```
phing test -Dextensions=ezprestapiprovider,ezcomments
```

### Test existing build

This is useful if you have a build available, test changes and don't want to rebuild.

```
phing test-existing
```
