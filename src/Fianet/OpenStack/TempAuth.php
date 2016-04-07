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

/**
 * @class TempAuth
 * Swift tempauth authentication mechanism, for use with php-opencloud.
 *
 */
class TempAuth extends \OpenCloud\Identity\Service
{

    /**
     * Factory method which allows for easy service creation
     *
     * @param  ClientInterface $client
     * @return self
     */
    public static function factory(\Guzzle\Http\ClientInterface $client)
    {
        $tempAuth = new self();

        if (($client instanceof \OpenCloud\Common\Base || $client instanceof \OpenCloud\OpenStack) && $client->hasLogger()) {
            $tempAuth->setLogger($client->getLogger());
        }

        $tempAuth->setClient($client);
        $tempAuth->setEndpoint(clone $client->getAuthUrl());

        return $tempAuth;
    }

    /**
     * Generate a new token for a given user.
     *
     * @param   $retion string  The region...
     * @headers $headers array  Additional headers to send (optional)
     * @return  \Guzzle\Http\Message\Response
     */
    public function generateToken($region, array $headers = array())
    {
        $url = $this->getUrl();
        $secret = $this->getClient()->getSecret();

        $headers += array("X-Auth-User" => $secret['username']);
        $headers += array("X-Auth-Key" => $secret['password']);

        $response = $this->getClient()->get($url, $headers, '')->send();

        $tokenId = $response->getHeader('X-Auth-Token');
        $storageUrl = $response->getHeader('X-Storage-Url');

        $token = [
            'id' => $tokenId,
            'issuedAt' => gmdate('c'),
            'expires' => gmdate('c', time() + 86400)
        ];

        $swift = [
            'id' => '',
            'region' => $region,
            'adminUrl' => $storageUrl,
            'internalUrl' => $storageUrl,
            'publicUrl' => $storageUrl,
        ];

        $keystone = [
            'id' => '',
            'region' => $region,
            'adminUrl' => $this->getUrl(),
            'internalUrl' => $this->getUrl(),
            'publicUrl' => $this->getUrl()
        ];

        $user = [
            'id' => $secret['username'],
            'username' => $secret['username'],
            'name' => $secret['username'],
            'role' => ''
        ];

        return $this->serviceCatalogue($swift, $keystone, $user, $token);
    }

    /**
     * Revoke a given token based on its ID
     *
     * @param $tokenId string Token ID
     * @return \Guzzle\Http\Message\Response
     */
    public function revokeToken($tokenId)
    {
        $token = $this->resource('Token');
        $token->setId($tokenId);

        return $token->delete();
    }

    private function serviceCatalogue($swift, $keystone, $user, $token)
    {
        return <<< END_CATALOGUE
{
    "access": {
        "token": {
            "issued_at": "{$token['issuedAt']}",
            "expires": "{$token['expires']}",
            "id": "{$token['id']}"
        },
        "serviceCatalog": [
            {
                "endpoints": [
                    {
                        "adminURL": "{$swift['adminUrl']}",
                        "region": "{$swift['region']}",
                        "internalURL": "{$swift['internalUrl']}",
                        "id": "{$swift['id']}",
                        "publicURL": "{$swift['publicUrl']}"
                    }
                ],
                "endpoints_links": [],
                "type": "object-store",
                "name": "swift"
            },
            {
                "endpoints": [
                    {
                        "adminURL": "{$keystone['adminUrl']}",
                        "region": "{$keystone['region']}",
                        "internalURL": "{$keystone['internalUrl']}",
                        "id": "{$keystone['id']}",
                        "publicURL": "{$keystone['publicUrl']}"
                    }
                ],
                "endpoints_links": [],
                "type": "identity",
                "name": "keystone"
            }
        ],
        "user": {
            "username": "{$user['username']}",
            "roles_links": [],
            "id": "{$user['id']}",
            "roles": [
                {
                    "name": "{$user['role']}"
                }
            ],
            "name": "{$user['name']}"
        }
    }
}
END_CATALOGUE;
    }

}
