<?php

namespace devtek\sdk;

use devtek\sdk\exceptions\ApiErrorException;
use devtek\sdk\models\{
    City,
    Lead,
    Region
};
use GuzzleHttp\{
    Client,
    RequestOptions
};
use GuzzleHttp\Exception\{
    ClientException,
    ServerException
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
     * Regions list fetched from API
     *
     * @var Region[]
     */
    protected $regionsList = [];

    /**
     * Cities list fetched from API
     *
     * @var City[]
     */
    protected $citiesList = [];

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
        if (!empty($this->regionsList)) {
            return $this->regionsList;
        }

        $requestOptions[RequestOptions::JSON] = array_merge($requestOptions[RequestOptions::JSON] ?? [], $this->getCredentials(static::CREDENTIALS_GROUP_WEBMASTER));
        $request = new Request('post', static::URI_REGIONS . '/');

        $response = $this->query($request, $requestOptions);
        if (!$response->isSuccess()) {
            return [];
        }

        $this->regionsList = array_map(function (array $region): Region {
            return new Region($region);
        }, $response->getData('regions'));
        return $this->regionsList;
    }

    /**
     * Returns list of cities
     *
     * @param Region|integer|null $region Region model or its ID
     * @param array $requestOptions Additional options that will be passed to request
     * @return City[] Cities list or empty array if request was unsuccessful
     */
    public function cities($region = null, array $requestOptions = []): array
    {
        if ($region instanceof Region) {
            $region = $region->region_id;
        }

        $requestOptions['json'] = array_merge($requestOptions[RequestOptions::JSON] ?? [], $this->getCredentials(static::CREDENTIALS_GROUP_WEBMASTER));
        $request = new Request('post', static::URI_CITIES . '/');

        if (empty($this->citiesList)) {
            $response = $this->query($request, $requestOptions);
            if (!$response->isSuccess()) {
                return [];
            }

            $this->citiesList = array_map(function (array $city): City {
                return new City($city);
            }, $response->getData('cities'));
        }

        if ($region) {
            return array_filter($this->citiesList, function (City $city) use ($region): bool {
                return $city->region_id == $region;
            });
        }
        return $this->citiesList;
    }

    /**
     * Sends lead
     *
     * @param Lead $lead Lead
     * @param array $requestOptions Additional options that will be passed to request
     * @return integer|null Lead ID on success or `null` on failure
     */
    public function send(Lead $lead, array $requestOptions = []): ?int
    {
        if ($lead->filled('region') && !$lead->filled('region_id')) {
            $lead->region_id = $this->findRegion($lead->region);
        }
        if ($lead->filled('city') && !$lead->filled('city_id')) {
            $lead->city_id = $this->findCity($lead->city, $lead->region_id);
        }

        $requestOptions[RequestOptions::JSON] = array_merge(
            $this->getCredentials(static::CREDENTIALS_GROUP_WEBMASTER),
            ['data' => $lead->data()]
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
     * Returns region with specific ID
     *
     * @param integer $id Region ID
     * @return Region|null Region or `null` if region with specified ID doesn't exist
     */
    public function getRegion(int $id): ?Region
    {
        $regions = $this->regions();
        foreach ($regions as $region) {
            if ($region->region_id == $id) {
                return $region;
            }
        }

        return null;
    }

    /**
     * Returns city with specific ID
     *
     * @param integer $id City ID
     * @return City|null City or `null` if city with specified ID doesn't exist
     */
    public function getCity(int $id): ?City
    {
        $cities = $this->cities();
        foreach ($cities as $city) {
            if ($city->city_id == $id) {
                return $city;
            }
        }

        return null;
    }

    /**
     * Looks for region and return its ID if found
     *
     * This method works with regions list from API
     *
     * @param string $name Region name
     * @return Region|null Region ID or `null` if region not found
     */
    public function findRegion(string $name): ?Region
    {
        $name = mb_strtolower($name);
        $result = null;

        $maxPercent = 0;
        $regions = $this->regions();
        foreach ($regions as $region) {
            $regionName = mb_strtolower($region->name);
            if ($name === $regionName) {
                $result = $region;
                break;
            }

            mb_similar_text($name, $regionName, $percent);
            if ($percent > $maxPercent) {
                $maxPercent = $percent;
                $result = $region;
            }
        }

        return $result;
    }

    /**
     * Looks for city and return its ID if found
     *
     * This method work with cities list from API
     *
     * @param string $name City name
     * @param Region|integer|null $region Region
     * @return City|null City or `null` if city not found
     */
    public function findCity(string $name, $region = null): ?City
    {
        $name = mb_strtolower($name);
        $result = null;

        $maxPercent = 0;
        $cities = $this->cities($region);
        foreach ($cities as $city) {
            $cityName = mb_strtolower($city->name);
            if ($name === $cityName) {
                $result = $city;
                break;
            }

            mb_similar_text($name, $cityName, $percent);
            if ($percent > $maxPercent) {
                $maxPercent = $percent;
                $result = $city;
            }
        }

        return $result;
    }

    /**
     * Queries API
     *
     * @param RequestInterface $request Request
     * @param array $requestOptions Request options
     * @return Response Response
     * @throws ApiErrorException On API error
     */
    public function query(RequestInterface $request, array $requestOptions = []): Response
    {
        $hasError = false;

        try {
            $response = $this->client->send($request, $requestOptions);
        } catch (ClientException $e) {
            $response = $e->getResponse();
            $hasError = true;
        } catch (ServerException $e) {
            $response = $e->getResponse();
            $hasError = true;
        }

        $response = new Response($response);
        if ($hasError) {
            $errors = $response->getErrors();
            if (is_array($errors)) {
                $error = array_shift($errors);
            }

            $message = '';
            if (isset($error['message'])) {
                $message = $error['message'];
            } else {
                $message = array_shift($error);
            }

            if (empty($message)) {
                $message = 'Unknown error';
            }
            throw new ApiErrorException($message, $response->original->getStatusCode());
        }

        return $response;
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
