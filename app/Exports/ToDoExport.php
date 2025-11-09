<?php

namespace App\Exports;

use App\Models\ToDo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class ToDoExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return ToDo::all();
    }

    public function headings(): array
    {
        return [
            'Title',
            'Assignee',
            'Due Date',
            'Time Tracked',
            'Status'
        ];
    }

    public function prepareRows($collection)
    {
        $total_to_do = $collection->length;
        dd($total_to_do);
    }
}
