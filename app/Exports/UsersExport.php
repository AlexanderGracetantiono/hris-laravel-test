<?php

namespace App\Exports;

// use App\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\BeforeWriting;
use Maatwebsite\Excel\Events\BeforeSheet;

use \Maatwebsite\Excel\Sheet;
class UsersExport implements FromCollection, WithHeadings,WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use RegistersEventListeners;
    protected $data_export;
    public function __construct(array $data_export)
    {
        $this->data_export = $data_export;
    }
    public function collection()
    { 
        $data= $this->data_export;
        return collect([
            $data
        ]);
    }
    public function headings(): array
    {
        return [
            'Bridge Code',
            'User Name',
            'Scan Timestamp',
            'Location',
            'Category',
            'Product',
            'Model',
            'Version',
            'SKU',
        ];
    }
    public function registerEvents(): array
{
    return [
        // Handle by a closure.
        AfterSheet::class => function(AfterSheet $event) {

            $event->sheet->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

            $event->sheet->styleCells(
                'A1:G8',
                [
                    'borders' => [
                        'outline' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                            'color' => ['argb' => 'FFFF0000'],
                        ],
                    ]
                ]
            );
        },
        BeforeExport::class => function(BeforeExport $event) {
            $event->writer->getProperties()->setTitle('ALEXANDER GRA');
        },
    ];
}
}
