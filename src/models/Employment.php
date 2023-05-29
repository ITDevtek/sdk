<?php

namespace devtek\sdk\models;

use devtek\sdk\interfaces\Listable;

/**
 * Job employment model
 *
 * @version 1.0.0
 */
class Employment extends Base implements Listable
{
    /**
     * Employment type: Employment contract
     */
    const TYPE_CONTRACT = 1;

    /**
     * Employment type: Business owner
     */
    const TYPE_BUSINESS_OWNER = 2;

    /**
     * Employment type: Individual business
     */
    const TYPE_INDIVIDUAL_BUSINESS = 3;

    /**
     * Employment type: Contract of employment
     */
    const TYPE_EMPLOYMENT_CONTRACT = 4;

    /**
     * Employment type: Government worker
     */
    const TYPE_GOVERNMENT_WORKER = 5;

    /**
     * Employment type: Unemployed
     */
    const TYPE_UNEMPLOYED = 6;

    /**
     * Employment type: Student
     */
    const TYPE_STUDENT = 7;

    /**
     * Employment type: Pensioner
     */
    const TYPE_PENSIONER = 8;

    /**
     * Employment type: Other source of income
     */
    const TYPE_OTHER_INCOME = 9;

    /**
     * {@inheritDoc}
     */
    protected $readonly = true;

    /**
     * Returns employment types list
     *
     * @return string[]
     */
    public static function list(): array
    {
        return [
            static::TYPE_CONTRACT => 'Employment contract',
            static::TYPE_BUSINESS_OWNER => 'Business owner',
            static::TYPE_INDIVIDUAL_BUSINESS => 'Individual business',
            static::TYPE_EMPLOYMENT_CONTRACT => 'Contract of employment',
            static::TYPE_GOVERNMENT_WORKER => 'Government worker',
            static::TYPE_UNEMPLOYED => 'Unemployed',
            static::TYPE_STUDENT => 'Student',
            static::TYPE_PENSIONER => 'Pensioner',
            static::TYPE_OTHER_INCOME => 'Other source of income'
        ];
    }
}
