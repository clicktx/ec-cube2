<?php

/* vim: set expandtab tabstop=4 shiftwidth=4: */
//
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2002 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/3_0.txt.                                  |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Harry Fuecks <hfuecks@phppatterns.com>                      |
// +----------------------------------------------------------------------+
//
// $Id: Validator.php,v 1.1 2004/05/24 22:25:42 quipo Exp $
//
/*
 * @package Calendar
 * @version $Id$
 */

/*
 * Validation Error Messages
 */
if (!defined('CALENDAR_VALUE_TOOSMALL')) {
    define('CALENDAR_VALUE_TOOSMALL', 'Too small: min = ');
}
if (!defined('CALENDAR_VALUE_TOOLARGE')) {
    define('CALENDAR_VALUE_TOOLARGE', 'Too large: max = ');
}

/**
 * Used to validate any given Calendar date object. Instances of this class
 * can be obtained from any data object using the getValidator method
 *
 * @see Calendar::getValidator()
 */
class Calendar_Validator
{
    /**
     * Instance of the Calendar date object to validate
     *
     * @var object
     */
    public $calendar;

    /**
     * Instance of the Calendar_Engine
     *
     * @var object
     */
    public $cE;

    /**
     * Array of errors for validation failures
     *
     * @var array
     */
    public $errors = [];

    /**
     * Constructs Calendar_Validator
     *
     * @param object subclass of Calendar
     */
    public function __construct(&$calendar)
    {
        $this->calendar = &$calendar;
        $this->cE = &$calendar->getEngine();
    }

    /**
     * Calls all the other isValidXXX() methods in the validator
     *
     * @return bool
     */
    public function isValid()
    {
        $checks = ['isValidYear', 'isValidMonth', 'isValidDay',
            'isValidHour', 'isValidMinute', 'isValidSecond', ];
        $valid = true;
        foreach ($checks as $check) {
            if (!$this->{$check}()) {
                $valid = false;
            }
        }

        return $valid;
    }

    /**
     * Check whether this is a valid year
     *
     * @return bool
     */
    public function isValidYear()
    {
        $y = $this->calendar->thisYear();
        $min = $this->cE->getMinYears();
        if ($min > $y) {
            $this->errors[] = new Calendar_Validation_Error(
                'Year', $y, CALENDAR_VALUE_TOOSMALL.$min);

            return false;
        }
        $max = $this->cE->getMaxYears();
        if ($y > $max) {
            $this->errors[] = new Calendar_Validation_Error(
                'Year', $y, CALENDAR_VALUE_TOOLARGE.$max);

            return false;
        }

        return true;
    }

    /**
     * Check whether this is a valid month
     *
     * @return bool
     */
    public function isValidMonth()
    {
        $m = $this->calendar->thisMonth();
        $min = 1;
        if ($min > $m) {
            $this->errors[] = new Calendar_Validation_Error(
                'Month', $m, CALENDAR_VALUE_TOOSMALL.$min);

            return false;
        }
        $max = $this->cE->getMonthsInYear($this->calendar->thisYear());
        if ($m > $max) {
            $this->errors[] = new Calendar_Validation_Error(
                'Month', $m, CALENDAR_VALUE_TOOLARGE.$max);

            return false;
        }

        return true;
    }

    /**
     * Check whether this is a valid day
     *
     * @return bool
     */
    public function isValidDay()
    {
        $d = $this->calendar->thisDay();
        $min = 1;
        if ($min > $d) {
            $this->errors[] = new Calendar_Validation_Error(
                'Day', $d, CALENDAR_VALUE_TOOSMALL.$min);

            return false;
        }
        $max = $this->cE->getDaysInMonth(
            $this->calendar->thisYear(), $this->calendar->thisMonth());
        if ($d > $max) {
            $this->errors[] = new Calendar_Validation_Error(
                'Day', $d, CALENDAR_VALUE_TOOLARGE.$max);

            return false;
        }

        return true;
    }

    /**
     * Check whether this is a valid hour
     *
     * @return bool
     */
    public function isValidHour()
    {
        $h = $this->calendar->thisHour();
        $min = 0;
        if ($min > $h) {
            $this->errors[] = new Calendar_Validation_Error(
                'Hour', $h, CALENDAR_VALUE_TOOSMALL.$min);

            return false;
        }
        $max = ($this->cE->getHoursInDay($this->calendar->thisDay()) - 1);
        if ($h > $max) {
            $this->errors[] = new Calendar_Validation_Error(
                'Hour', $h, CALENDAR_VALUE_TOOLARGE.$max);

            return false;
        }

        return true;
    }

    /**
     * Check whether this is a valid minute
     *
     * @return bool
     */
    public function isValidMinute()
    {
        $i = $this->calendar->thisMinute();
        $min = 0;
        if ($min > $i) {
            $this->errors[] = new Calendar_Validation_Error(
                'Minute', $i, CALENDAR_VALUE_TOOSMALL.$min);

            return false;
        }
        $max = ($this->cE->getMinutesInHour($this->calendar->thisHour()) - 1);
        if ($i > $max) {
            $this->errors[] = new Calendar_Validation_Error(
                'Minute', $i, CALENDAR_VALUE_TOOLARGE.$max);

            return false;
        }

        return true;
    }

    /**
     * Check whether this is a valid second
     *
     * @return bool
     */
    public function isValidSecond()
    {
        $s = $this->calendar->thisSecond();
        $min = 0;
        if ($min > $s) {
            $this->errors[] = new Calendar_Validation_Error(
                'Second', $s, CALENDAR_VALUE_TOOSMALL.$min);

            return false;
        }
        $max = ($this->cE->getSecondsInMinute($this->calendar->thisMinute()) - 1);
        if ($s > $max) {
            $this->errors[] = new Calendar_Validation_Error(
                'Second', $s, CALENDAR_VALUE_TOOLARGE.$max);

            return false;
        }

        return true;
    }

    /**
     * Iterates over any validation errors
     *
     * @return mixed either Calendar_Validation_Error or false
     */
    public function fetch()
    {
        $error = current($this->errors);
        next($this->errors);
        if ($error) {
            return $error['value'];
        } else {
            reset($this->errors);

            return false;
        }
    }
}

/**
 * For Validation Error messages
 *
 * @see Calendar::fetch()
 */
class Calendar_Validation_Error
{
    /**
     * Date unit (e.g. month,hour,second) which failed test
     *
     * @var string
     */
    public $unit;

    /**
     * Value of unit which failed test
     *
     * @var int
     */
    public $value;

    /**
     * Validation error message
     *
     * @var string
     */
    public $message;

    /**
     * Constructs Calendar_Validation_Error
     *
     * @param string Date unit (e.g. month,hour,second)
     * @param int Value of unit which failed test
     * @param string Validation error message
     */
    public function __construct($unit, $value, $message)
    {
        $this->unit = $unit;
        $this->value = $value;
        $this->message = $message;
    }

    /**
     * Returns the Date unit
     *
     * @return string
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * Returns the value of the unit
     *
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Returns the validation error message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Returns a string containing the unit, value and error message
     *
     * @return string
     */
    public function toString()
    {
        return $this->unit.' = '.$this->value.' ['.$this->message.']';
    }
}
