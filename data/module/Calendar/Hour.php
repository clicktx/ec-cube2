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
// $Id: Hour.php,v 1.1 2004/05/24 22:25:42 quipo Exp $
//
/*
 * @package Calendar
 * @version $Id$
 */

/*
 * Allows Calendar include path to be redefined
 * @ignore
 */
if (!defined('CALENDAR_ROOT')) {
    define('CALENDAR_ROOT', 'Calendar'.DIRECTORY_SEPARATOR);
}

/**
 * Load Calendar base class
 */
require_once CALENDAR_ROOT.'Calendar.php';

/**
 * Represents an Hour and builds Minutes
 * <code>
 * require_once 'Calendar'.DIRECTORY_SEPARATOR.'Hour.php';
 * $Hour = & new Calendar_Hour(2003, 10, 21, 15); // Oct 21st 2003, 3pm
 * $Hour->build(); // Build Calendar_Minute objects
 * while ($Minute = & $Hour->fetch()) {
 *     echo $Minute->thisMinute().'<br />';
 * }
 * </code>
 */
class Calendar_Hour extends Calendar
{
    /**
     * Constructs Calendar_Hour
     *
     * @param int year e.g. 2003
     * @param int month e.g. 5
     * @param int day e.g. 11
     * @param int hour e.g. 13
     */
    public function __construct($y, $m, $d, $h)
    {
        parent::__construct($y, $m, $d, $h);
    }

    /**
     * Builds the Minutes in the Hour
     *
     * @param array (optional) Calendar_Minute objects representing selected dates
     *
     * @return bool
     */
    public function build($sDates = [])
    {
        require_once CALENDAR_ROOT.'Minute.php';
        $mIH = $this->cE->getMinutesInHour($this->year, $this->month, $this->day,
            $this->hour);
        for ($i = 0; $i < $mIH; $i++) {
            $this->children[$i] =
                new Calendar_Minute($this->year, $this->month, $this->day,
                    $this->hour, $i);
        }
        if (count($sDates) > 0) {
            $this->setSelection($sDates);
        }

        return true;
    }

    /**
     * Called from build()
     *
     * @param array
     *
     * @return void
     */
    public function setSelection($sDates)
    {
        foreach ($sDates as $sDate) {
            if ($this->year == $sDate->thisYear()
                && $this->month == $sDate->thisMonth()
                && $this->day == $sDate->thisDay()
                && $this->hour == $sDate->thisHour()) {
                $key = (int) $sDate->thisMinute();
                if (isset($this->children[$key])) {
                    $sDate->setSelected();
                    $this->children[$key] = $sDate;
                }
            }
        }
    }
}
