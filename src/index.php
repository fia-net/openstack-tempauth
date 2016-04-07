<?php
/**
 * fia-net/openstack-tempauth - (c) Fia-Net 2016.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

require '../vendor/autoload.php';

/*
 * This file demonstrates the connection to a SWIFT instance using
 * tempauth authentication on top of rackspace/php-opencloud.
 */

/*
 * swift requires a region name.
 *
 * We can use any non-empty string, provided that the same region name
 * is used when creating the OpenStack object and getting the object
 * store service.
 */
$myRegion = "my_region";

$client = new \Fianet\OpenStack\OpenStack(
    'http://your-swift-url-here:8080/auth/v1.0',
    [
        'username' => 'admin:admin',
        'password' => 'my_password'
    ],
    $myRegion
);

$service = $client->objectStoreService("swift", $myRegion, "publicURL");

// From here, you should be able to use $service just as if you were
// using Keystone.
$container = $service->getContainer('my_container');


// Customize your options according to the php-opencloud documentation.
$options = [];
$list = $container->objectList($options);

// Just dump the objects.
print_r($list);

?>
