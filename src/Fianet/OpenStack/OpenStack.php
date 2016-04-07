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

namespace Fianet\OpenStack;

class OpenStack extends \OpenCloud\OpenStack
{
    private $region;

    public function __construct($url, array $secret, $region, array $options = array())
    {
        parent::__construct($url, $secret, $options);
        $this->region = $region;
    }

    // Function is private in opencloud, so we need our own here.
    private function updateTokenHeader($token)
    {
        $this->setDefaultOption('headers/X-Auth-Token', (string) $token);
    }

    public function authenticate()
    {
        // OpenStack APIs will return a 401 if an expired X-Auth-Token is sent,
        // so we need to reset the value before authenticating for another one.
        $this->updateTokenHeader('');

        $tempauth = TempAuth::factory($this);
        $response = $tempauth->generateToken($this->region, array());

        $body = json_decode($response);

        $this->setCatalog($body->access->serviceCatalog);
        $this->setTokenObject($tempauth->resource('\OpenCloud\Identity\Resource\Token', $body->access->token));
        $this->setUser($tempauth->resource('\OpenCloud\Identity\Resource\User', $body->access->user));

        if (isset($body->access->token->tenant)) {
            $this->setTenantObject($tempauth->resource('Tenant', $body->access->token->tenant));
        }

        // Set X-Auth-Token HTTP request header
        $this->updateTokenHeader($this->getToken());
    }

}
