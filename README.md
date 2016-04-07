# fia-net/openstack-tempauth
Very basic support for tempauth authentication for swift using
http://github.com/rackspace/php-opencloud.

[![Latest Stable Version](https://poser.pugx.org/fia-net/openstack-tempauth/v/stable)](https://packagist.org/packages/fia-net/openstack-tempauth) [![Total Downloads](https://poser.pugx.org/fia-net/openstack-tempauth/downloads)](https://packagist.org/packages/fia-net/openstack-tempauth) [![Latest Unstable Version](https://poser.pugx.org/fia-net/openstack-tempauth/v/unstable)](https://packagist.org/packages/fia-net/openstack-tempauth) [![License](https://poser.pugx.org/fia-net/openstack-tempauth/license)](https://packagist.org/packages/fia-net/openstack-tempauth)

## Requirements
* PHP >=5.4
* rackspace/php-opencloud >= 1.14

## Installation
Install through Composer.

```bash
composer require fia-net/openstack-tempauth
```
## How it works
A lightly customized OpenStack class and a TempAuth adapter imitate the
Keystone authentication. It may not be the best solution but it works
for us and does not imply modifying the php-opencloud source tree.

## Usage Example
Just instanciate the \Fianet\OpenStack\OpenStack class in lieu of
\OpenCloud\OpenStack.

See the file example/usage.php file.


## Support
This package is provided "as-is" and comes with no warranty and no
support of any kind. It is **not** considered as production-ready.
