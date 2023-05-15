<?php

namespace devtek\sdk;

use Psr\Http\Message\ResponseInterface;

/**
 * Response
 *
 * @version 1.0.0
 */
class Response
{
    /**
     * Response
     *
     * @var ResponseInterface
     */
    public $original;

    /**
     * Decoded JSON response content
     *
     * @var array
     */
    public $content = [];

    /**
     * Constructor
     *
     * @param ResponseInterface $response Response
     */
    public function __construct(ResponseInterface $response)
    {
        $this->original = $response;
        $this->content = json_decode($this->original->getBody()->getContents(), true);
    }

    /**
     * Checks if response is success
     *
     * @return boolean `true` if response is success or `false` if not
     */
    public function isSuccess(): bool
    {
        if (!isset($this->content['result'])) {
            return false;
        }
        return $this->content['result'] !== 'bad';
    }

    /**
     * Checks if response has data
     *
     * @return boolean `true` if response has data or `false` if not
     */
    public function hasData(): bool
    {
        if (!isset($this->content['data'])) {
            return false;
        }
        return !empty($this->content['data']);
    }

    /**
     * Returns data item
     *
     * @param string|null $name
     * @return mixed|null Value or `null` if specified item doesn't exists
     */
    public function getData(?string $name = null)
    {
        if (!$this->hasData()) {
            return null;
        }

        $data = $this->content['data'];
        if (is_null($name)) {
            return $data;
        }

        if (!isset($data[$name])) {
            return null;
        }
        return $data[$name];
    }

    /**
     * Checks if response has errors
     *
     * @return boolean `true` if response has errors or `false` if not
     */
    public function hasErrors(): bool
    {
        if (!isset($this->content['errors'])) {
            return false;
        }
        return !empty($this->content['errors']);
    }

    /**
     * Returns response errors
     *
     * @return array Errors or empty array when no errors
     */
    public function getErrors(): array
    {
        if (!$this->hasErrors()) {
            return [];
        }

        return $this->content['errors'];
    }
}
