<?php
namespace helpers;

class RecurrenceTool {
    
    /**
     * Calculates the next recurrence of a given item, based on its date and
     * a string describing the recurrence in the iCal format.
     * 
     * @param $recurrenceString Description of the recurrence.
     * @param $baseDate Date to base the recurrence off.
     * @return returns the next date of recurrence, or 'false' if there is none
     *         based on the provided rules.
     */
    public static function getNextRecurrence($recurrenceString, $baseDate) {
        $vevent = new vevent();
        $vevent->parse(array('RRULE:' . $recurrenceString));
        $rrule = $vevent->getProperty('rrule');
        
        $vevent->setProperty("dtstart", $baseDate);
        $start = $vevent->getProperty("dtstart");
        
        $saveUntil = (empty($rrule['UNTIL']))
                ? null
                : strtotime($rrule['UNTIL']['year'] . '-'
                    . $rrule['UNTIL']['month'] . '-'
                    . $rrule['UNTIL']['day']);
        $vevent->setProperty("dtend", '+10 years');
        $end = $vevent->getProperty("dtend");
        
        $rrule['COUNT'] = 2;
        if (isset($rrule['UNTIL']))
            unset($rrule['UNTIL']);
        $vevent->_recur2date(
            $recurlist,
            $rrule,
            $start,    // start date of item
            $start,    // start date of interval we're interested in
            $end       // end date
        );
        
        if (empty($recurlist)) {
            return false;
        } else {
            $keysArray = array_keys($recurlist);
            $nextDue = array_shift($keysArray);
            $nextDue = ($saveUntil && $saveUntil < $nextDue)
                    ? false
                    : date('Y-m-d', $nextDue);
        }
        return $nextDue;
    }
    
    public static function extractRecursionString($request) {
        if ($request['repeats'] == 'never')
            return false;
        
        switch($request['repeats']) {
            case 'repeats-daily':
                $endDate = empty($request['endDateDaily']) ? null : $request['endDateDaily'];
                $recurrence = RecurrenceTool::getDailyRecurrence(
                        $request['dailyRepeatInterval'], $endDate);
                break;
            case 'repeats-weekly':
                $endDate = empty($request['endDateWeekly']) ? null : $request['endDateWeekly'];
                $recurrence = RecurrenceTool::getWeeklyRecurrence(
                        $request['weeklyRepeatInterval'], $request['repeatDayOfWeek'],
                        $endDate);
                break;
            case 'repeats-monthly':
                $endDate = empty($request['endDateMonthly']) ? null : $request['endDateMonthly'];
                switch ($_POST['monthlyRecurrenceType']) {
                    case 'dayOfMonth':
                        $recurrence = RecurrenceTool::getMonthlyDayOfMonthRecurrence(
                                $request['monthlyRepeatInterval'], $request['dayOfMonthRecurrence'],
                                $endDate);
                        break;
                    case 'dayOfWeek':
                        $recurrence = RecurrenceTool::getMonthlyDayOfWeekRecurrence(
                                $request['monthlyRepeatInterval'], $request['dayOfWeekRecurrenceNumber'],
                                $request['dayOfWeekRecurrenceDay'], $endDate);
                        break;
                }
                break;
            case 'repeats-yearly':
                $endDate = empty($request['endDateYearly']) ? null : $request['endDateYearly'];
                $recurrence = RecurrenceTool::getYearlyRecurrence(
                        $request['yearlyRepeatInterval'], $endDate);
                break;
        }
        
        return $recurrence;
    }
    
    public static function getDailyRecurrence($interval, $endDate) {
        $vevent = new vevent();
        $rrule = array();
        
        $rrule['FREQ'] = 'DAILY';
        $rrule['INTERVAL'] = $interval;
        if (!empty($endDate))
            $rrule['UNTIL'] = $endDate;
        
        $vevent->setProperty("rrule",$rrule);
        $rruletext = substr($vevent->createRrule(), 6);
        
        return array(
                'recur' => $rruletext,
                'recurDesc' => "+{$rrule['INTERVAL']}".substr($rrule['FREQ'],0,1)
                );
    }
    
    public static function getWeeklyRecurrence($interval, $daysOfWeek, $endDate) {
        $vevent = new vevent();
        $rrule = array();
        
        $rrule['FREQ'] = 'WEEKLY';
        $rrule['INTERVAL'] = $interval;
        $out = array();
        foreach ($daysOfWeek as $day)
            array_push($out, array('DAY' => $day));
        $rrule['BYDAY'] = $out;
        if (!empty($endDate))
            $rrule['UNTIL'] = $endDate;
        
        $vevent->setProperty("rrule",$rrule);
        $rruletext = substr($vevent->createRrule(), 6);
        
        return array(
                'recur' => $rruletext,
                'recurDesc' => "+{$rrule['INTERVAL']}" . substr($rrule['FREQ'], 0, 1)
                );
    }
    
    public static function getMonthlyDayOfMonthRecurrence($interval, $dayOfMonth, $endDate) {
        $vevent = new vevent();
        $rrule = array();
        
        $rrule['FREQ'] = 'MONTHLY';
        $rrule['INTERVAL'] = $interval;
        $rrule['BYMONTHDAY'] = array($dayOfMonth);
        if (!empty($endDate))
            $rrule['UNTIL'] = $endDate;
        
        $vevent->setProperty("rrule",$rrule);
        $rruletext = substr($vevent->createRrule(), 6);
        
        return array(
                'recur' => $rruletext,
                'recurDesc' => "+{$rrule['INTERVAL']}" . substr($rrule['FREQ'], 0, 1)
                );
    }
    
    public static function getMonthlyDayOfWeekRecurrence($interval, $weekNumber, $dayOfWeek, $endDate) {
        $vevent = new vevent();
        $rrule = array();
        
        $rrule['FREQ'] = 'MONTHLY';
        $rrule['INTERVAL'] = $interval;
        $rrule['BYDAY'] = array((int)$weekNumber, 'DAY' => $dayOfWeek);
        if (!empty($endDate))
            $rrule['UNTIL'] = $endDate;
        
        $vevent->setProperty("rrule",$rrule);
        $rruletext = substr($vevent->createRrule(), 6);
        
        return array(
                'recur' => $rruletext,
                'recurDesc' => "+{$rrule['INTERVAL']}" . substr($rrule['FREQ'], 0, 1)
                );
    }
    
    public static function getYearlyRecurrence($interval, $endDate) {
        $vevent = new vevent();
        $rrule = array();
        
        $rrule['FREQ'] = 'YEARLY';
        $rrule['INTERVAL'] = $interval;
        if (!empty($endDate))
            $rrule['UNTIL'] = $endDate;
        
        $vevent->setProperty("rrule",$rrule);
        $rruletext = substr($vevent->createRrule(), 6);
        
        return array(
                'recur' => $rruletext,
                'recurDesc' => "+{$rrule['INTERVAL']}" . substr($rrule['FREQ'], 0, 1)
                );
    }
    
}
