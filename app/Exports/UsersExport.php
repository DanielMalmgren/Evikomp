<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\User;

class UsersExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return User::All('personid', 'name', 'email');
    }

    public function headings(): array
    {
        return [
            'Personnr',
            'Namn',
            'E-post',
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
