<?php

namespace devtek\sdk\helpers;

use DateTimeImmutable;

/**
 * Validator helper class
 *
 * @version 1.0.0
 */
class Validator
{
    /**
     * Validator: Check name
     */
    const CHECK_NAME = 'check_name';

    /**
     * Validator: Check russian name
     */
    const CHECK_RUSSIAN_NAME = 'check_russian_name';

    /**
     * Validator: Numeric
     */
    const NUMERIC = 'numeric';

    /**
     * Validator: Integer
     */
    const INTEGER = 'integer';

    /**
     * Validator: Email
     */
    const EMAIL = 'email';

    /**
     * Validator: Russian phone
     */
    const PHONE_RUSSIAN = 'russian_phone';

    /**
     * Validator: Birth date
     */
    const BIRTHDATE = 'birthdate';

    /**
     * Validator: Region
     */
    const REGION = 'region';

    /**
     * Validator: City
     */
    const CITY = 'city';

    /**
     * Validators list
     *
     * Keys are validator names and values are validation functions
     *
     * Each validator function accepts two parameters:
     * 1. `$value` - value to be validated;
     * 2. `$error` - reference to variable which will be contain validaton error.
     *
     * When validator finds error it must stop validation,
     * store error message to `$error` variable and return
     * correspond boolean value with validation result
     *
     * __Example validator function__
     * ```
     * function (mixed $value, &$error): bool {}
     * ```
     *
     * Function must return `true` on successful validation
     * or `false` if validation failed
     *
     * @var callable[]
     */
    protected static $validators = [
        self::CHECK_NAME => function ($value, &$error): bool {
            if (!is_string($value)) {
                return false;
            }

            $length = mb_strlen($value);
            if ($length < 2) {
                $error = 'Value length must be greater or equal 2';
                return false;
            } else if ($length > 255) {
                $error = 'Value length must be less than 255';
                return false;
            }

            return true;
        },
        self::CHECK_RUSSIAN_NAME => function ($value, &$error): bool {
            if (!preg_match('/^[а-яА-ЯёЁ\-\s]+$/uis', $value)) {
                $error = 'Value must contain only russian chars, dashes and spaces';
                return false;
            }

            return true;
        },
        self::NUMERIC => function ($value, &$error): bool {
            if (!is_numeric($value)) {
                $error = 'Value must be numeric';
                return false;
            }

            return true;
        },
        self::INTEGER => function ($value, &$error): bool {
            if (!is_integer($value)) {
                $error = 'Value must be integer';
                return false;
            }

            return true;
        },
        self::EMAIL => function ($value, &$error): bool {
            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $error = 'Invalid email';
                return false;
            }

            return true;
        },
        self::PHONE_RUSSIAN => function ($value, &$error): bool {
            if (!preg_match('/^([+][0-9\s-\(\)]{11,13})*$/uis', $value)) {
                $value = 'Invalid russian phone';
                return false;
            }

            return true;
        },
        self::BIRTHDATE => function ($value, &$error): bool {
            if (DateTimeImmutable::createFromFormat('Y-m-d', $value) === false) {
                $error = 'Invalid birthdate format. Valid format is: "Y-m-d"';
                return false;
            }

            return true;
        }
    ];

    /**
     * Returns validator function
     *
     * @param string $name Validator name
     * @return callable|null Validator function or `null` if validator doesn't exists
     */
    public static function get(string $name): ?callable
    {
        if (!isset(static::$validators[$name])) {
            return null;
        }
        return static::$validators[$name];
    }

    /**
     * Adds validator
     *
     * @param string $name Name
     * @param callable $validator Handler
     * @return boolean `true` if validator successfully added or `false` if not _(this may happen if validator with same name already exists)_
     */
    public static function add(string $name, callable $validator): bool
    {
        if (isset(static::$validators[$name])) {
            return false;
        }

        static::$validators[$name] = $validator;
        return true;
    }

    /**
     * Removes validator
     *
     * @param string $name Name
     * @return boolean `true` if validator removed successfully removed or `false` if not _(this may happen if validator with specified name doesn't exists)_
     */
    public static function remove(string $name): bool
    {
        if (!isset(static::$validators[$name])) {
            return false;
        }

        unset(static::$validators[$name]);
        return true;
    }
}
