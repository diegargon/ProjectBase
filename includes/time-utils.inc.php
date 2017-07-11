<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */

/** TODO CLASS * */
!defined('IN_WEB') ? exit : true;

class TimeUtils {

    private $cfg;
    private $db;
    private $timezone;
    private $server_timezone;
    private $ftime_now;
    private $dateformat;

    public function __construct($cfg, $db) {
        $this->cfg = $cfg;
        $this->db = $db;
        $this->server_timezone = date_default_timezone_get();
        $this->setTimezone();
        $this->setDateformat();
        $this->ftime_now = new DateTime(date($this->dateformat, time()));
    }

    public function format_date($date, $timestamp = false) {
        //TODO DateTime
        if ($timestamp) {
            return date($this->dateformat, $date);
        } else {
            return date($this->dateformat, strtotime($date));
        }
    }

    private function setTimezone() {
        //TODO: LANG to TIME ZONE OR DETECT USER TIMEZONE WITH Javascript        
        // OR USER PREF
        date_default_timezone_set($this->cfg['DEFAULT_TIMEZONE']);
        $this->timezone = date_default_timezone_get();
    }

    private function setDateFormat() {
        //TODO: Check user preferences
        $this->dateformat = $this->cfg['DEFAULT_DATEFORMAT'];
    }

}

// replace and remove

function format_date($date, $timestamp = false) {
    global $cfg;
    if ($timestamp) {
        return date($cfg['DEFAULT_DATEFORMAT'], $date);
    } else {
        return date($cfg['DEFAULT_DATEFORMAT'], strtotime($date));
    }
}
