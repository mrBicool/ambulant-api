<?php 

namespace App\Library;

use Carbon\Carbon;
use Hash;

class Helper
{
    public function getClarionDate(Carbon $date)
    {
        $start_date = '1801-01-01';
        $start_from = Carbon::parse($start_date);
        $diff = $date->diffInDays($start_from) + 4;
        return $diff;
    }

    public function getClarionTime(Carbon $date)
    {
        $startOfTheDay = Carbon::create($date->year, $date->month, $date->day, 0, 0, 0);

        $result = $startOfTheDay->diffInSeconds($date);
        //$result = $startOfTheDay->diffInRealMilliseconds($date);

        return ($result * 100);
    }

    public function createToken($string){ 
         return hash('sha256', $string);
        //return Hash::make($string.now());
    } 

}
