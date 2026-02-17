<?php

namespace App\Livewire\Admin\Datatables;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class PatientTable extends DataTableComponent
{


    //Construimos el modelo 
    public function builder(): Builder
    {
        return Patient::query()->select('patients.*')->with('user');
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make("Debug User ID", "user_id")
                ->label(fn($row) => $row->user_id ?? 'NULL'),
            Column::make("Id", "id")
                ->sortable(),
            Column::make("Nombre")
                ->sortable(function(Builder $query, string $direction) {
                    return $query->orderBy(
                        User::select('name')
                            ->whereColumn('users.id', 'patients.user_id')
                            ->limit(1),
                        $direction
                    );
                })
                ->label(function($row) {
                    return $row->user->name ?? 'N/A';
                }),
            Column::make("Email")
                ->sortable(function(Builder $query, string $direction) {
                    return $query->orderBy(
                        User::select('email')
                            ->whereColumn('users.id', 'patients.user_id')
                            ->limit(1),
                        $direction
                    );
                })
                ->label(function($row) {
                    return $row->user->email ?? 'N/A';
                }),
            Column::make("Número de id")
                ->sortable(function(Builder $query, string $direction) {
                    return $query->orderBy(
                        User::select('id_number')
                            ->whereColumn('users.id', 'patients.user_id')
                            ->limit(1),
                        $direction
                    );
                })
                ->label(function($row) {
                    return $row->user->id_number ?? 'N/A';
                }),
            Column::make("Teléfono")
                ->sortable(function(Builder $query, string $direction) {
                    return $query->orderBy(
                        User::select('phone')
                            ->whereColumn('users.id', 'patients.user_id')
                            ->limit(1),
                        $direction
                    );
                })
                ->label(function($row) {
                    return $row->user->phone ?? 'N/A';
                }),
            Column::make("Fecha de creación", "created_at")
                ->sortable()
                ->label(function($row) {
                    return $row->created_at ? $row->created_at->format('d/m/Y') : 'N/A';
                }),
            Column::make("Acciones")
                ->label(function($row){
                    return view('admin.patients.actions', 
                    ['patient' => $row]);
                }),
            /*Column::make("Allergies", "allergies")
                ->sortable(),
            Column::make("Chronic conditions", "chronic_conditions")
                ->sortable(),
            Column::make("Surgical history", "surgical_history")
                ->sortable(),
            Column::make("Family history", "family_history")
                ->sortable(),
            Column::make("Observations", "observations")
                ->sortable(),
            Column::make("Emergency contact name", "emergency_contact_name")
                ->sortable(),
            Column::make("Emergency contact phone", "emergency_contact_phone")
                ->sortable(),
            Column::make("Emergency contact relationship", "emergency_contact_relationship")
                ->sortable(),
            Column::make("Created at", "created_at")
                ->sortable(),
            Column::make("Updated at", "updated_at")
                ->sortable(),*/
        ];
    }
}
