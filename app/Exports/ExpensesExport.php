<?php

namespace App\Exports;

use App\Models\Expense;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExpensesExport implements FromCollection, WithHeadings, WithMapping
{
    protected $startDate;
    protected $endDate;
    protected $category;

    public function __construct($startDate, $endDate, $category)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->category = $category;
    }

    public function collection()
    {
        $query = Expense::where('user_id', Auth::id())->whereBetween('date', [
            $this->startDate,
            $this->endDate
        ]);

        if (!empty($this->category)) {
            $query->where('category', $this->category);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return ['Name', 'Amount', 'Date', 'Category', 'Notes','Created On'];
    }

    public function map($row): array
    {
        return [
            $row->name,
            number_format($row->amount, 2),
            $row->date->format('D, jS M Y'),
            $row->category,
            $row->notes,
            $row->created_at->format('D, jS M Y h:i A'),
        ];
    }
}
