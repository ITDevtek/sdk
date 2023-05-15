<?php

namespace devtek\sdk;

use devtek\sdk\exceptions\ValidationErrorException;
use devtek\sdk\models\{
    City,
    Lead,
    Region
};
use GuzzleHttp\{
    Client,
    RequestOptions
};
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;

/**
 * Devtek PHP SDK
 *
 * @version 1.0.0
 */
class Devtek
{
    /**
     * URI for sending leads
     */
    const URI_LEAD = 'new-lead';

    /**
     * URI for getting regions list
     */
    const URI_REGIONS = 'get-regions';

    /**
     * URI for getting cities list
     */
    const URI_CITIES = 'get-cities';

    /**
     * Credentials group: Webmaster
     */
    const CREDENTIALS_GROUP_WEBMASTER = 0;

    /**
     * Credentials group: Company
     */
    const CREDENTIALS_GROUP_COMPANY = 1;

    /**
     * Credential: Webmaster ID
     */
    const CREDENTIAL_WEBMASTER_ID = 'webmasterId';

    /**
     * Credential: Webmaster ID
     */
    const CREDENTIAL_WEBMASTER_TOKEN = 'webmasterToken';

    /**
     * Credential: Webmaster ID
     */
    const CREDENTIAL_COMPANY_TOKEN = 'companyToken';

    /**
     * API endpoint
     *
     * @var string
     */
    protected $endpoint = 'https://api.devtek.io';

    /**
     * HTTP client
     *
     * @var Client
     */
    protected $client;

    /**
     * Webmaster ID
     *
     * @var integer
     */
    protected $webmasterId = 0;

    /**
     * Webmaster token
     *
     * @var string
     */
    protected $webmasterToken = '';

    /**
     * Company token
     *
     * @var string
     */
    protected $companyToken = '';

    /**
     * Constructor
     *
     * @param array $clientConfig HTTP client options
     * @return void
     */
    public function __construct(array $clientConfig = [])
    {
        $options = array_merge([
            'base_uri' => $this->endpoint,
            'allow_redirects' => true
        ], $clientConfig);

        $this->client = new Client($options);
    }

    /**
     * Setups credential
     *
     * @param string $credential Credential name _(refers to `CREDENTIAL` constants)_
     * @param mixed $value Credential value
     * @return boolean
     */
    public function setCredential(string $credential, $value): bool
    {
        if (!property_exists($this, $credential)) {
            return false;
        }

        $this->$credential = $value;
        return true;
    }

    /**
     * Returns list of regions
     *
     * @param array $requestOptions Additional options that will be passed to request
     * @return Region[] Regions list or empty array if request was unsuccessful
     */
    public function regions(array $requestOptions = []): array
    {
        $requestOptions[RequestOptions::JSON] = array_merge($requestOptions[RequestOptions::JSON] ?? [], $this->getCredentials(static::CREDENTIALS_GROUP_WEBMASTER));
        $request = new Request('post', static::URI_REGIONS . '/');

        $response = $this->query($request, $requestOptions);
        if (!$response->isSuccess()) {
            return [];
        }

        return array_map(function (array $region) {
            return new Region($region);
        }, $response->getData('regions'));
    }

    /**
     * Returns list of cities
     *
     * @param Region|integer $region Region model or its ID
     * @param array $requestOptions Additional options that will be passed to request
     * @return City[] Cities list or empty array if request was unsuccessful
     */
    public function cities($region = null, array $requestOptions = []): array
    {
        if ($region instanceof Region) {
            $region = $region->region_id;
        }
        if (is_numeric($region)) {
            $requestOptions[RequestOptions::QUERY]['regionId'] = $region;
        }

        $requestOptions['json'] = array_merge($requestOptions[RequestOptions::JSON] ?? [], $this->getCredentials(static::CREDENTIALS_GROUP_WEBMASTER));
        $request = new Request('post', static::URI_CITIES . '/');

        $response = $this->query($request, $requestOptions);
        if (!$response->isSuccess()) {
            return [];
        }

        return array_map(function (array $city) {
            return new City($city);
        }, $response->getData('cities'));
    }

    /**
     * Sends lead
     *
     * @param Lead $lead Lead
     * @param array $requestOptions Additional options that will be passed to request
     * @return integer|null Lead ID on success or `null` on failure
     * @throws ValidationErrorException On lead validation failure
     */
    public function send(Lead $lead, array $requestOptions = []): ?int
    {
        $lead->validate();

        $requestOptions[RequestOptions::JSON] = array_merge(
            $this->getCredentials(static::CREDENTIALS_GROUP_WEBMASTER),
            $lead->data()
        );
        $request = new Request('post', static::URI_LEAD . '/');

        $response = $this->query($request, $requestOptions);
        if (!$response->isSuccess()) {
            return null;
        }
        if (!$response->hasData()) {
            return null;
        }

        return $response->getData('id_lead');
    }

    /**
     * Queries API
     *
     * @param RequestInterface $request Request
     * @param array $requestOptions Request options
     * @return Response Response
     */
    public function query(RequestInterface $request, array $requestOptions = []): Response
    {
        $response = $this->client->send($request, $requestOptions);
        return new Response($response);
    }

    /**
     * Returns credentials
     *
     * @param integer $group Credentials group ID _(refers to `CREDENTIALS_GROUP` constants)_
     * @return array Credentials
     */
    public function getCredentials(int $group): array
    {
        if ($group == static::CREDENTIALS_GROUP_COMPANY) {
            return ['token' => $this->companyToken];
        }

        return [
            'id_webmaster' => $this->webmasterId,
            'token' => $this->webmasterToken
        ];
    }
}
