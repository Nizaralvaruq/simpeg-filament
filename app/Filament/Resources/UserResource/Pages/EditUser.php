<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function afterSave(): void
    {
        $data = $this->form->getRawState();

        if (isset($data['employee_id'])) {
            // Remove old employee link if exists
            if ($this->record->employee && $this->record->employee->id != $data['employee_id']) {
                $this->record->employee->update(['user_id' => null]);
            }

            // Link new employee
            if (!empty($data['employee_id'])) {
                $employee = \Modules\Kepegawaian\Models\DataInduk::find($data['employee_id']);
                if ($employee) {
                    $employee->update(['user_id' => $this->record->id]);
                }
            }
        }
    }
}
