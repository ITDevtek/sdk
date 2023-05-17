<?php

namespace devtek\sdk\models;

use devtek\sdk\exceptions\ValidationErrorException;
use devtek\sdk\helpers\Validator;
use Exception;

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
 * @property string|null $region Actual region
 * @property string|null $city Actual city
 * @property integer|null $amount Amount
 * @property integer|null $credit_history Credit history
 * @property string|null $sub_id1 Sub 1
 * @property string|null $sub_id2 Sub 2
 * @property string|null $sub_id3 Sub 3
 * @property string|null $sub_id4 Sub 4
 * @property string|null $sub_id5 Sub 5
 * @property string|null $passport_series Passport series
 * @property string|null $passport_number Passport number
 * @property string|null $passport_issued_date Passport issue date
 * @property string|null $passport_issued Name of organization that issued passport
 * @property string|null $passport_issued_code Passport issue code
 * @property string|null $registration_region Registration region
 * @property string|null $registration_city Registration city
 * @property string|null $registration_index Registration zip
 * @property string|null $registration_street Registration street
 * @property string|null $registration_street_kladr Registration street KLADR code
 * @property string|null $registration_city_kladr Registration city KLADR code
 * @property string|null $registration_region_kladr Registration region KLADR code
 * @property string|null $registration_house Registration house
 * @property string|null $registration_house_kladr Registration house KLADR code
 * @property string|null $registration_housing Registration housing
 * @property string|null $registration_housing_kladr Registration housing KLADR code
 * @property string|null $registration_apartment Registration apartment
 * @property integer|null $deposit Deposit
 * @version 1.0.0
 */
class Lead extends Base
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
        'region',
        'city',
        'amount',
        'credit_history',
        'sub_id1',
        'sub_id2',
        'sub_id3',
        'sub_id4',
        'sub_id5'
    ];

    /**
     * Keyword that will be used to store detailed form data
     */
    protected const DETAILED_KEYWORD = 'detailed';

    /**
     * Detailed form fields list
     */
    protected const DETAILED_FIELDS = [
        'passport_series',
        'passport_number',
        'passport_issued_date',
        'passport_issued',
        'passport_issued_code',
        'registration_region',
        'registration_city',
        'registration_index',
        'registration_street',
        'registration_street_kladr',
        'registration_city_kladr',
        'registration_region_kladr',
        'registration_house',
        'registration_house_kladr',
        'registration_housing',
        'registration_housing_kladr',
        'registration_apartment',
        'deposit'
    ];

    /**
     * Keyword that will be used to store job form data
     */
    protected const JOB_KEYWORD = 'job';

    /**
     * Job form fields list
     */
    protected const JOB_FIELDS = [];

    /**
     * Validation rules
     *
     * Keys are field names and values are arrays with validator names
     *
     * If field has only one validator, array brackets can be skipped
     *
     * @var array[]|string[]
     */
    protected $validationRules = [
        'last_name' => [Validator::CHECK_NAME, Validator::CHECK_RUSSIAN_NAME],
        'first_name' => [Validator::CHECK_NAME, Validator::CHECK_RUSSIAN_NAME],
        'middle_name' => [Validator::CHECK_NAME, Validator::CHECK_RUSSIAN_NAME],
        'birthday' => Validator::DATE,
        'phone' => Validator::PHONE_RUSSIAN,
        'email' => Validator::EMAIL,
        // 'region' => [], // TODO
        // 'city' => [], // TODO
        'amount' => Validator::INTEGER,
        'credit_history' => Validator::CREDIT_HISTORY,
        'sub_id1' => Validator::SUB_ID,
        'sub_id2' => Validator::SUB_ID,
        'sub_id3' => Validator::SUB_ID,
        'sub_id4' => Validator::SUB_ID,
        'sub_id5' => Validator::SUB_ID,
        'passport_series' => Validator::PASSPORT_SERIES,
        'passport_number' => Validator::PASSPORT_NUMBER
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
            if (in_array($name, static::DETAILED_FIELDS)) {
                $keyword = static::DETAILED_KEYWORD;
            } else if (in_array($name, static::JOB_FIELDS)) {
                $keyword = static::JOB_KEYWORD;
            }

            if (!isset($data[$keyword])) {
                $data[$keyword] = [];
            }
            $data[$keyword][$name] = $value;
        }
        return $data;
    }

    /**
     * {@inheritDoc}
     * @return boolean Always `true`. If validation will fail or something else went wrong it will throw an exception
     * @throws Exception If specified field validator not found
     * @throws ValidationErrorException On validation failure
     */
    public function validate(): bool
    {
        foreach ($this->validationRules as $field => $rules) {
            $value = $this->fields[$field] ?? null;
            if (!is_array($rules)) {
                $rules = [$rules];
            }

            foreach ($rules as $rule) {
                $validator = Validator::get($rule);
                if (is_null($validator)) {
                    throw new Exception("Validator \"{$rule}\" not found");
                }

                $error = '';
                $success = $validator($value, $error);
                if (!$success) {
                    throw new ValidationErrorException($field, $error, $value);
                }
            }
        }

        return true;
    }
}
