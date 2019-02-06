<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\User;
use App\ActiveTime;

class ActiveTimeExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    private $user;

    public function __construct($user) {
        $this->user = $user;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $active_times = array();
        for($i = 1; $i <= date("j"); $i++) {
            $this_time = $active_times_db = ActiveTime::where('user_id', $this->user->id)->whereMonth('date', date("n"))->whereDay('date', $i)->first();
            if($this_time) {
                $active_times[$i] = date("H:i", $this_time->seconds+59); //Add 59 to get it rounded upwards
            } else {
                $active_times[$i] = "00:00";
            }
        }

        //logger(print_r($active_times, true));

        return collect([$active_times]);
    }

    public function headings(): array
    {
        $headings_array = array();

        for ($i = 1; $i <= date("t"); $i++) {
            $headings_array[] = $i;
        }

        return $headings_array;
    }
}
