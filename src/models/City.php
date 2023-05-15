<?php

namespace devtek\sdk\models;

/**
 * City model
 *
 * @property-read integer $city_id City ID
 * @property-read string $name Name
 * @property-read integer $region_id Region ID
 * @property-read boolean $city_published Is city published?
 * @property-read float $city_latitude Latitude
 * @property-read float $city_longitude Longitude
 * @property-read string $city_name_en Name in english
 * @property-read string $city_alias Alias
 * @property-read string $city_case Case
 * @property-read string $city_case_slug Case slug
 * @property-read integer $country_id Country ID
 * @property-read integer $utc_offset Time offset from UTC in minutes
 * @version 1.0.0
 */
class City extends Base
{
    /**
     * {@inheritDoc}
     */
    protected $readonly = true;
}
