<?php namespace commons\dates;

const   MYSQL_DATE_FORMAT       = 'Y-m-d',
        MYSQL_DATETIME_FORMAT   = 'Y-m-d h:i:s',
        SUNDAY                  = 0,
        MONDAY                  = 1,
        TUESDAY                 = 2,
        WEDNESDAY               = 3,
        THURSDAY                = 4,
        FRIDAY                  = 5,
        SATURDAY                = 6;

/**
 * Gets a date in MySQL Date Format
 * @param string $date
 * @return string|NULL
 */
function format_date_mysql(?string $date):?string{
    if(isset($date) && $date){
        return (new \DateTime($date))->format(MYSQL_DATE_FORMAT);
    }
    return $date;
}

/**
 * Adjusts a date from Mysql to a common human readable format
 * @param string $sql
 * @throws \InvalidArgumentException
 * @return string
 */
function date_from_sql(?string $sql): string {
    $sq = explode(' ', $sql);
    $date = explode('-', $sq[0]);
    if (count($date) == 3) {
        return "$date[1]/$date[2]/$date[0]";
    } else {
        throw new \InvalidArgumentException('Date must be in SQL format: '.$sql);
    }
}

/**
 * Adjusts a date time from Mysql to a common human readable format
 * @param string $sql
 * @throws \InvalidArgumentException
 * @return string
 */
function date_time_from_sql(?string $sql):string {
    $sq = explode(' ', $sql);
    $date = explode('-', $sq[0]);
    if (count($date) == 3) {
        return "$date[1]/$date[2]/$date[0] {$sq[1]}";
    } else {
        throw new \InvalidArgumentException('Date must be in SQL format: '.$sql);
    }
}

/**
 * The current date in MySQl Format
 * @return string
 */
function mysql_now_date():string{
    return (new \DateTime())->format(MYSQL_DATE_FORMAT);
}

/**
 * The current datetime in MySQl Format
 * @return string
 */
function mysql_now_datetime():string{
    return (new \DateTime())->format(MYSQL_DATETIME_FORMAT);
}

/**
 * Is today a Saturday?
 * @param string $date
 * @return bool
 */
function isDateSaturday(?string $date = null):bool{
    $timestamp	= isset($date)?strtotime($date):time();
    
    $date_of_the_week	= date("w", $timestamp);
    return ($date_of_the_week == SATURDAY);
}

/**
 * Is today Tuesday?
 * @param string $date
 * @return bool
 */
function isDateTuesday(?string $date = null): bool{
    $timestamp	= isset($date)?strtotime($date):time();
    
    $date_of_the_week	= date("w", $timestamp);
    return ($date_of_the_week == TUESDAY);
}

/**
 * Is today the first day of the month?
 * @param string $date
 * @return bool
 */
function isFirstDayOfTheMonth(?string $date = null):bool{
    $timestamp	= isset($date)?strtotime($date):time();
    
    $day_of_month	= date("j", $timestamp);
    return	($day_of_month == 1);
}
/**
 * Is today a Saturday?
 * @param string $date
 * @return bool
 */
function isDateNotSaturday(?string $date = null):bool{
    $timestamp	= isset($date)?strtotime($date):time();
    
    $date_of_the_week	= date("w", $timestamp);
    return ($date_of_the_week != SATURDAY);
}