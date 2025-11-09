<?php

namespace App\Exports;

use App\Models\ToDo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ToDoExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    protected $filters;
    public $total_todo, $total_time_tracked;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = ToDo::query()
            ->where('title', 'LIKE', '%' . $this->filters['title'] . '%');

        if (!empty($this->filters['assignee'])) {
            $query->whereIn('assignee', array_map('trim', $this->filters['assignee']));
        }

        if (!empty($this->filters['status'])) {
            $query->whereIn('status', array_map('trim', $this->filters['status']));
        }

        if (!empty($this->filters['priority'])) {
            $query->whereIn('priority', array_map('trim', $this->filters['priority']));
        }

        if ($this->filters['start_date'] && $this->filters['end_date']) {
            $query->whereBetween('due_date', [$this->filters['start_date'], $this->filters['end_date']]);
        } else {
            if ($this->filters['start_date']) {
                $query->where('due_date', '>=', $this->filters['start_date']);
            }

            if ($this->filters['end_date']) {
                $query->where('due_date', '<=', $this->filters['end_date']);
            }
        }

        if ($this->filters['min_time'] && $this->filters['max_time']) {
            $query->whereBetween('time_tracked', [$this->filters['min_time'], $this->filters['max_time']]);
        } else {
            if ($this->filters['min_time']) {
                $query->where('time_tracked', '>=', $this->filters['min_time']);
            }

            if ($this->filters['max_time']) {
                $query->where('time_tracked', '>=', $this->filters['max_time']);
            }
        }

        $this->total_todo = $query->count();
        $this->total_time_tracked = $query->sum('time_tracked');

        return $query->get();
    }

    public function map($row): array
    {
        return [
            $row->title,
            $row->assignee,
            $row->due_date,
            $row->time_tracked,
            $row->status,
            $row->priority
        ];
    }

    public function headings(): array
    {
        return [
            'Title',
            'Assignee',
            'Due Date',
            'Time Tracked',
            'Status',
            'Priority'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $rows = count($this->collection());

                $start_row = $rows + 2;

                $event->sheet->setCellValue('A' . $start_row, 'Total Task: ');
                $event->sheet->setCellValue('F' . $start_row, $this->total_todo);
                $event->sheet->mergeCells('A' . $start_row . ':' . 'E' . $start_row);

                $event->sheet->setCellValue('A' . ($start_row + 1), 'Total Time: ');
                $event->sheet->setCellValue('F' . ($start_row + 1), $this->total_time_tracked);
                $event->sheet->mergeCells('A' . ($start_row + 1) . ':' . 'E' . ($start_row + 1));

                $event->sheet->getStyle('A' . $start_row . ':' . 'E' . ($start_row + 1))->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_RIGHT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ]
                ]);

                $event->sheet->getStyle('A1:F' . ($start_row + 1))->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
            }
        ];
    }
}
