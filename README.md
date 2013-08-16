# eZ Publish Legacy Tests

Execute PHPUnit tests for eZ Publish Legacy, optionally with one or more activated
extensions.

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
