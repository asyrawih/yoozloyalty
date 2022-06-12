<?php
namespace Platform\Exports;

use App\Customer;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Style;

use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CustomersExport implements ShouldAutoSize, FromCollection, WithColumnFormatting, WithHeadings, WithMapping
{
    use Exportable;

    private User $user;

    public function __construct()
    {
        $this->user = User::query()->find(Auth::id());
    }

    public function headings(): array
    {
      return [
        'Name',
        'E-mail',
        'Available Point',
        'Customer number',
        'Membership card number',
        'Website',
        'Last Activity'
      ];
    }

    public function map($row): array
    {
        return [
            $row->name,
            $row->email,
            $row->points,
            $row->number,
            $row->card,
            $row->campaign_text,
            ($row->last_login) ? Carbon::parse($row->last_login, config('app.timezone'))->setTimezone($this->user->getTimezone())->diffForHumans() : null,
        ];
    }

    public function columnFormats(): array
    {
        return [
          'C' => Style\NumberFormat::FORMAT_NUMBER,
          'D' => Style\NumberFormat::FORMAT_TEXT,
          'E' => Style\NumberFormat::FORMAT_TEXT,
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Customer::withoutGlobalScopes()
            ->where('created_by', $this->user->id)
            ->get();
    }
}
