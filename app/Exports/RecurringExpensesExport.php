<?php

namespace App\Exports;

use App\Models\Expense;
use App\Models\RecurringExpense;
use App\Utils\Enums\ExpenseFrequency;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RecurringExpensesExport implements FromCollection, WithHeadings, WithMapping
{
    protected $startDate;
    protected $endDate;
    protected $category;
    protected $frequency;

    public function __construct($startDate, $endDate, $category,$frequency)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->category = $category;
        $this->frequency = $frequency;
    }
    /**
    * @return Collection
    */
    public function collection()
    {
        $query = RecurringExpense::where('user_id', Auth::id())->whereBetween('created_at', [
            $this->startDate,
            $this->endDate
        ]);

        if (!empty($this->category)) {
            $query->where('category', $this->category);
        }

        if (!empty($this->frequency)) {
            $query->where('frequency', $this->frequency);
        }

        return $query->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return ['Name', 'Amount(KES)', 'Start Date', 'Category','Frequency', 'Notes','Status','Created On','Last Processed','Next Process','Schedule'];
    }

    /**
     * @param $row
     * @return array
     */
    public function map($row): array
    {
        return [
            $row->name,
            number_format($row->amount, 2),
            $row->start_date->format('d-m-Y'),
            ucfirst($row->category->value),
            strtoupper($row->frequency->value),
            $row->notes,
            $row->is_active == 1 ? "Active": "Deactivated",
            Carbon::parse($row->created_at)->format('d-m-Y h:i A'),
            Carbon::parse($row->last_processed_at)->format('d-m-Y h:i A'),
            Carbon::parse($row->next_process_at)->format('d-m-Y h:i A'),
            $row->execution_days
        ];
    }
}
