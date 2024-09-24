<?php

namespace devtek\sdk\models;

/**
 * Lead model
 *
 * @property-read integer $id Devtek lead ID
 * @property string|null $last_name Last name
 * @property string|null $first_name First name
 * @property string|null $middle_name Middle name
 * @property string|null $birthday Birthday
 * @property string $phone Phone
 * @property string|null $email Email
 * @property string|null $iin IIN
 * @property string|null $region Actual region
 * @property string|null $city Actual city
 * @property integer|null $region_id Actual region ID
 * @property integer|null $city_id Actual city ID
 * @property integer|null $amount Amount
 * @property integer|null $credit_history Credit history
 * @property string|null $channel Channel
 * @property string|null $sub_id1 Sub 1
 * @property string|null $sub_id2 Sub 2
 * @property string|null $sub_id3 Sub 3
 * @property string|null $sub_id4 Sub 4
 * @property string|null $sub_id5 Sub 5
 * @version 1.0.0
 */
class LeadKz extends Base
{
    /**
     * Keyword that will be used to store short form data
     */
    protected const SHORT_KEYWORD = 'main';

    /**
     * Short form fields list
     */
    protected const SHORT_FIELDS = [
        'id',
        'last_name',
        'first_name',
        'middle_name',
        'birthday',
        'phone',
        'email',
        'iin',
        'passport_number',
        'registration_region',
        'registration_city',
        'amount',
        'credit_history',
        'sopd_date',
        'sopd_site',
        'sopd_general',
        'sopd_signed',
        'channel',
        'sub_id1',
        'sub_id2',
        'sub_id3',
        'sub_id4',
        'sub_id5'
    ];

    /**
     * Returns lead data
     *
     * @return array
     */
    public function data(): array
    {
        $data = [];

        foreach ($this->fields as $name => $value) {
            $keyword = static::SHORT_KEYWORD;

            if (in_array($name, ['region', 'region_id'])) {
                $name = 'registration_region';
            } else if (in_array($name, ['city', 'city_id'])) {
                $name = 'registration_city';
            }

            if (!isset($data[$keyword])) {
                $data[$keyword] = [];
            }
            $data[$keyword][$name] = $value;
        }
        return $data;
    }
}
