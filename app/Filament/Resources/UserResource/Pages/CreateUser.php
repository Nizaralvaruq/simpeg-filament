<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
    {
        $data = $this->form->getRawState();

        if (!empty($data['employee_id'])) {
            $employee = \Modules\Kepegawaian\Models\DataInduk::find($data['employee_id']);
            if ($employee) {
                $employee->update(['user_id' => $this->record->id]);
            }
        }
    }
}
