<?php

namespace App\Filament\Resources\Shield;

use App\Filament\Resources\Shield\RoleResource\Pages;
use BezhanSalleh\FilamentShield\Resources\Roles\RoleResource as ShieldRoleResource;

class RoleResource extends ShieldRoleResource
{
    protected static ?string $recordTitleAttribute = 'name';

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'view' => Pages\ViewRole::route('/{record}'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }

    public static function getResourceEntitiesSchema(): ?array
    {
        $groupsOrder = [
            'Menu Saya' => 1,
            'Kepegawaian' => 2,
            'Presensi' => 3,
            'Penilaian Kinerja' => 4,
            'Data Master' => 5,
            'Authorization' => 6,
        ];

        return collect(\BezhanSalleh\FilamentShield\Facades\FilamentShield::getResources())
            ->sort(function ($a, $b) use ($groupsOrder) {
                $aFqcn = $a['resourceFqcn'];
                $bFqcn = $b['resourceFqcn'];

                $aGroup = class_exists($aFqcn) && method_exists($aFqcn, 'getNavigationGroup') ? $aFqcn::getNavigationGroup() : 'Other';
                $bGroup = class_exists($bFqcn) && method_exists($bFqcn, 'getNavigationGroup') ? $bFqcn::getNavigationGroup() : 'Other';

                $aGroupOrder = $groupsOrder[$aGroup] ?? 99;
                $bGroupOrder = $groupsOrder[$bGroup] ?? 99;

                if ($aGroupOrder !== $bGroupOrder) {
                    return $aGroupOrder <=> $bGroupOrder;
                }

                $aSort = class_exists($aFqcn) && method_exists($aFqcn, 'getNavigationSort') ? $aFqcn::getNavigationSort() : 999;
                $bSort = class_exists($bFqcn) && method_exists($bFqcn, 'getNavigationSort') ? $bFqcn::getNavigationSort() : 999;

                if ($aSort !== $bSort) {
                    return $aSort <=> $bSort;
                }

                return $a['model'] <=> $b['model'];
            })
            ->map(function (array $entity): \Filament\Schemas\Components\Section {
                $sectionLabel = strval(
                    static::shield()->hasLocalizedPermissionLabels()
                        ? \BezhanSalleh\FilamentShield\Facades\FilamentShield::getLocalizedResourceLabel($entity['resourceFqcn'])
                        : $entity['model']
                );

                return \Filament\Schemas\Components\Section::make($sectionLabel)
                    ->description(fn(): \Illuminate\Support\HtmlString => new \Illuminate\Support\HtmlString('<span style="word-break: break-word;">' . \BezhanSalleh\FilamentShield\Support\Utils::showModelPath($entity['modelFqcn']) . '</span>'))
                    ->compact()
                    ->schema([
                        static::getCheckBoxListComponentForResource($entity),
                    ])
                    ->columnSpan(static::shield()->getSectionColumnSpan())
                    ->collapsible();
            })
            ->values()
            ->toArray();
    }
}
