<?php
namespace helpers;

class HumaneDateView
{

	/**
     * Fetches a 'human-readable' version of a deadline.
     * 
     * @param $date Date to be parsed.
     * @return deadline in a human-readable format:
     *         'today', if due is today;
     *         'X days overdue', if deadline has passed;
     *         'in X days' if deadline is less than 7 days ahead;
     *         date, if deadline is further ahead.
     */
    public static function getHumanReadable($date) {
        $daysDue = ceil((strtotime($date) - time()) / (24 * 60 * 60));
        
        if ($daysDue == 0)
            return 'today';
        else if ($daysDue < 0)
            return (-$daysDue) . ' days overdue';
        else if ($daysDue > 0 && $daysDue <= 7)
            return 'in ' . $daysDue . ' days';
        else
            return date("M j, Y", strtotime($date));
    }

}
