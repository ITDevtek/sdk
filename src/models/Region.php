<?php

namespace devtek\sdk\models;

/**
 * Region model
 *
 * @property-read integer $region_id Region ID
 * @property-read integer $code Country internal region code _(equals `0` if not applicable)_
 * @property-read string $name Region name
 * @property-read boolean $region_published Is region published?
 * @property-read integer $id_country Country ID
 * @property-read string $slug Slug
 * @property-read integer $capital_id ID of region capital
 * @property-read integer $utc_offset Time offset from UTC in minutes
 * @version 1.0.0
 */
class Region extends Base
{
    /**
     * {@inheritDoc}
     */
    protected $readonly = true;
}
