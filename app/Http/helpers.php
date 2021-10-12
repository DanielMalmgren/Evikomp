<?php

use Illuminate\Support\Facades\Auth;

function user_should_attest() {
    $user = Auth::user();

    setlocale(LC_TIME, $user->locale_id);
    $previous_month = (int)date("m", strtotime("first day of previous month"));
    $previous_month_year = (int)date("Y", strtotime("first day of previous month"));

    $last_month_is_attested = $user->month_is_fully_attested($previous_month_year, $previous_month, 0.5);

    $time=Auth::user()->month_total_time($previous_month_year, $previous_month);

    return !$last_month_is_attested && $time>=1.0 && Auth::user()->workplace->includetimeinreports;
}

function locale_is_default() {
    return \App::isLocale(\Config::get('app.fallback_locale'));
}

function add_flash_message(array $notification){
    if(empty(session('notification_collection'))) {
        // If notification_collection is either not set or not a collection
        $new_collection = new \Illuminate\Support\Collection();
        $new_collection->push(['notification_message' => $notification['message'], 'notification_type' => $notification['type']]);
        session()->flash('notification_collection', $new_collection);
    } else {
        // Add to the notification-collection
        $notification_collection = \Session::get( 'notification_collection' );
        $notification_collection->push(['notification_message' => $notification['message'],'notification_type' => $notification['type']]);
        session()->flash('notification_collection', $notification_collection);
    }
}

function str_word_count_utf8(string $str, int $format = 0) {
    if($format === 1) {
        return preg_split('~[^\p{L}\p{N}\'-]+~u',$str);
    }
    return count(preg_split('~[^\p{L}\p{N}\'-]+~u',$str));
}

/*
Handles month/year increment calculations in a safe way,
avoiding the pitfall of 'fuzzy' month units.

Returns a DateTime object with incremented month values, and a date value == 1.
*/
function incrementDate($monthIncrement = 0) {
    // Get the month value of the current date:
    $monthString = date('Y-m');
    // Create a date string corresponding to the 1st of the give month,
    // making it safe for monthly calculations:
    $safeDateString = "first day of $monthString";
    // Increment date by given month increments:
    $incrementedDateString = "$safeDateString $monthIncrement month";
    $newTimeStamp = strtotime($incrementedDateString);
    //$newDate = DateTime::createFromFormat('U', $newTimeStamp);
    return $newTimeStamp;
}
