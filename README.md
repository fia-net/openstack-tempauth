# fia-net/openstack-tempauth
Very basic support for tempauth authentication for swift using
http://github.com/rackspace/php-opencloud.

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

```php
$client = new \Fianet\OpenStack\OpenStack('http://your-swift-url-here:8080/auth/v1.0', ['username' => 'admin:admin', 'password' => 'my_password'], "my_region");
$service = $client->objectStoreService("swift", "my_region", "publicURL");
```

## Support
This package is provided "as-is" and comes with no warranty and no
support of any kind. It is **not** considered as production-ready.
