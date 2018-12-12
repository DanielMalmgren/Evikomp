<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\User;

class UsersExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //return User::All('personid', 'name', 'email');
        //$headers = collect(['Personnr','Namn','E-post']);
        $data = User::All('personid', 'name', 'email');//->prepend(['personid' => 'Personnr', 'name' => 'Namn', 'email' => 'E-post']);
        //logger("Header: ".$headers->toJson());
        //logger("Data: ".$data->toJson());
        //logger("Combined: ".$data->prepend(['personid' => 'Personnr', 'name' => 'Namn', 'email' => 'E-post'])->toJson());
        return $data;
    }

    public function headings(): array
    {
        return [
            'Personnr',
            'Namn',
            'E-post'
        ];
    }

    /*public function columnFormats(): array
    {
        return [
            'personid' => NumberFormat::FORMAT_TEXT,
            'name' => NumberFormat::FORMAT_TEXT,
            'email' => NumberFormat::FORMAT_TEXT,
        ];
    }*/
}
