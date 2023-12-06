<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace common\tests\models;
namespace common\helpers;

use Datetime;
use yii\db\Exception;

/**
 * Description of Datetime
 *
 * @author pc-luci
 */
class MyDatetime extends Datetime {

    /**
     * format time to date string
     * @param type $time
     * @param string $format default = 'Y-m-d H:i:s'
     * @return string
     * @throws Exception
     */
    public static function formatDateTime($time, $format = 'Y-m-d H:i:s') {
        if (!$time || !is_integer($time)) {
            throw new Exception(501, 'Invalid data');
        }

        return date($format, $time);
    }

    /**
     * Format time elapsed to string
     * @param integer $ptime
     * @param boolean $toward false: past <br/> true:future
     * @return string
     */
    public static function timeElapsedString($ptime, $toward = false) {
        if ($toward) {
            $etime = $ptime - time();
        } else {
            $etime = time() - $ptime;
        }

        if ($etime < 1) {
            return '0 giây';
        }

        $a = array(12 * 30 * 24 * 60 * 60 => 'năm',
            30 * 24 * 60 * 60 => 'tháng',
            24 * 60 * 60 => 'ngày',
            60 * 60 => 'giờ',
            60 => 'phút',
            1 => 'giây'
        );

        foreach ($a as $secs => $str) {
            $d = $etime / $secs;
            if ($d >= 1) {
                $r = round($d);
                return $r . ' ' . $str;
            }
        }
    }

    static function numberElapsedString($number) {
        $a = array(12 * 30 * 24 * 60 * 60 => 'năm',
            30 * 24 * 60 * 60 => 'tháng',
            24 * 60 * 60 => 'ngày',
            60 * 60 => 'giờ',
            60 => 'phút',
            1 => 'giây'
        );
        if ($number < 1) {
            return '0 giây';
        }
        foreach ($a as $secs => $str) {
            $d = $number / $secs;
            if ($d >= 1) {
                $r = round($d);
                return $r . ' ' . $str;
            }
        }
    }

    /**
     * Get Start datetime of $endDate
     * @param type $startDate
     * @return type
     */
    public static function getStartDate($startDate) {
        $date = new DateTime($startDate);
        $date->setTime(00, 00, 00);
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * Get end datetime of $endDate
     * @param type $endDate
     * @return type
     */
    public static function getEndDate($endDate) {
        $date = new DateTime($endDate);
        $date->setTime(23, 59, 59);
        return $date->format('Y-m-d H:i:s');
    }

    /**
     *
     * @param type $startDate
     * @param type $endDate
     * @return boolean
     */
    public static function isToday($startDate, $endDate) {
        $today = date("Y-m-d H:i:s", time());
        $startDate = CUtils::getStartDate($startDate);
        $endDate = CUtils::getEndDate($endDate);
        if ($today >= $startDate && $today <= $endDate) {
            return true;
        } else
            return false;
    }

    /**
     *
     * @param type $datetoformat
     * @param type $format
     * @return type
     */
    public static function getDateViaFormat($datetoformat, $format = 'd/m/Y') {
        $date = new \DateTime($datetoformat);
        //$date->setTime(00, 00, 00);
        return $date->format($format);
    }

    public static function format_time_to_hour($t, $f = ':') { // t = seconds, f = separator
        return sprintf("%02d%s%02d%s%02d", floor($t / 3600), $f, ($t / 60) % 60, $f, $t % 60);
    }

    public static function format_time_to_hour_minute($t, $f = ':') {
        $t = intval($t);
        if ($t > 0) {
            $seconds = $t % 60;
            if ($seconds < 60) {
                $t = $t + (60 - $seconds);
            }
            // t = seconds, f = separator
            return sprintf("%02d%s%02d", floor($t / 3600), $f, ($t / 60) % 60);
        }
        return "00{$f}00";
    }

    public static function format_time_to_hour_day($t, $f = ':') {

        if ($t > 0) {
            // t = seconds, f = separator
            return sprintf("%02d", ceil($t / 86400));
        }
        return "0";
    }

    /**
     * Lay thoi gian cuoi cua ngay hien tai
     * @return int
     */
    public static function getLastTimeDate() {
        $currentDate = \DateTime::createFromFormat("d-m-Y H:i:s", date("d-m-Y") . " 23:59:59");
        return $currentDate->getTimestamp();
    }

    /**
     * Lay thoi gian dau cua ngay hien tai
     * @return int
     */
    public static function getFirstTimeDate() {
        $currentDate = \DateTime::createFromFormat("d-m-Y H:i:s", date("d-m-Y") . " 00:00:00");
        return $currentDate->getTimestamp();
    }

    /**
     * Cung cấp timestamp in seconds đầu tiên của phút ứng với thời gian cần lấy
     * @param int $timestamp
     * @return int||false
     */
    public static function getFirstTimeToMinute($timestamp) {
        if (!self::timestampValidate($timestamp)) {
            return false;
        }
        $dateTime = new \DateTime();
        $dateTime->setTimestamp($timestamp);
        $ts = $dateTime->format('Y-m-d H:i:00');
        $dateTimeNew = \DateTime::createFromFormat('Y-m-d H:i:s', $ts);
        return $dateTimeNew->getTimestamp();
    }

    /**
     * Cung cấp timestamp in seconds cuối cùng của phút ứng với thời gian cần lấy
     * @param int $timestamp 
     * @return int
     */
    public static function getLastTimeToMinute($timestamp) {
        if (!self::timestampValidate($timestamp)) {
            return false;
        }
        $dateTime = new \DateTime();
        $dateTime->setTimestamp($timestamp);
        $ts = $dateTime->format('Y-m-d H:i:59');
        $dateTimeNew = \DateTime::createFromFormat('Y-m-d H:i:s', $ts);
        return $dateTimeNew->getTimestamp();
    }

    /**
     * Convert string to timestamp
     * @param type $dateString
     * @param type $inputFormat
     * @return integer||false False convert fail
     */
    public static function convertStringToTimeStamp($dateString, $inputFormat) {

        $dateFormat = \DateTime::createFromFormat($inputFormat, $dateString);
        //echo $dateFormat->format($output);
        return ($dateFormat) ? $dateFormat->getTimestamp() : FALSE;
    }

    /**
     * 
     * @param integer $timestamp
     * @return boolean
     */
    static function timestampValidate($timestamp) {
        return is_numeric($timestamp) && ($timestamp <= PHP_INT_MAX) && ($timestamp >= ~PHP_INT_MAX) ? true : false;
    }

}
