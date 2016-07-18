# Project archive

Discontinued open source software not having an own repository anymore. The git history has been preserved, though.

- [**eZ Publish Legacy Tests**](ezpublish-legacy-tests)<br />
  Execute PHPUnit tests for eZ Publish Legacy, optionally with one or more activated extensions.
- [**Guzzle 3.x Fixture Plugin**](guzzle-plugin-fixture)<br />
  A [Guzzle](http://guzzle.readthedocs.org/) plugin for automatic mock response/fixture creation and retrieval.

## Import steps

```bash
$ git remote add -f tmp git@github.com:<organisation>/<name>.git
$ git merge --allow-unrelated-histories -s ours --no-commit tmp/master
$ git read-tree --prefix=<name>/ -u tmp/master
```
