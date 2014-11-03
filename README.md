This is a simple repackaging of [Baikal](https://github.com/netgusto/Baikal)
locally. It requires PHP and the PHP modules needed by Baikal.

The addressbooks and calendars 'test' and 'test{1-10}' exist.

`reset.sh` resets the database to the original state.

`install.sh` implies `reset.sh` and also downloads Baikal if the repo is
missing.

`php.sh` is a standalone server.
