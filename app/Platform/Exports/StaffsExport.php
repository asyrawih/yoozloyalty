<?php
namespace Platform\Exports;

use App\Staff;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StaffsExport implements ShouldAutoSize, FromCollection, WithHeadings, WithMapping
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
            'Staff ID',
            'Role',
            'Email',
            'Store Point',
            'Active',
            'Last Activity'
        ];
    }

    public function map($row): array
    {
        $stores = collect($row->businesses)->pluck('name');

        return [
            $row->name,
            $row->id,
            $row->staff_role['name'],
            $row->email,
            implode(', ', $stores->toArray()),
            ($row->active) ? 'Yes' : 'No',
            ($row->last_login) ? Carbon::parse($row->last_login, config('app.timezone'))->setTimezone($this->user->getTimezone())->diffForHumans() : null,
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Staff::withoutGlobalScopes()
            ->where('created_by', $this->user->id)
            ->get();
    }
}
