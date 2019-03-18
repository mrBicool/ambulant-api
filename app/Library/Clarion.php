<?php 

namespace App\Library;

use Carbon\Carbon;

class Clarion
{
    public function getClarionDate(Carbon $date){
        $start_date = '1801-01-01';
        $start_from = Carbon::parse($start_date);
        $diff = $date->diffInDays($start_from) + 4;
        return $diff;
    }

    public function getClarionTime(Carbon $date){
        $startOfTheDay = Carbon::create($date->year, $date->month, $date->day, 0, 0, 0);

        $result = $startOfTheDay->diffInSeconds($date);
        //$result = $startOfTheDay->diffInRealMilliseconds($date);

        return ($result * 100);
    }
}

