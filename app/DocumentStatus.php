<?php

namespace App;

use Filament\Support\Icons\Heroicon;

enum DocumentStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Skipped = 'skipped';


    public static function options(): array
    {
        return [
            'approved' => 'Aprobado',
            'rejected' => 'Rechazado'
        ];
    }

    public static function getLabel(string $value)
    {
        return collect(self::cases())
            ->firstWhere('value', $value)
            ?->label();
    }

    public function label(): string
    {
        return match ($this) {
            self::Pending  => 'Pendiente',
            self::Processing => 'Procesando',
            self::Approved => 'Aprobado',
            self::Rejected => 'Rechazado',
            self::Skipped => 'Omitido'
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Processing => 'info',
            self::Approved => 'success',
            self::Rejected => 'danger',
            self::Skipped => 'gray'
        };
    }


    public function icon()
    {
        return match ($this) {
            self::Pending => Heroicon::OutlinedClock,
            self::Processing => Heroicon::OutlinedSparkles,
            self::Approved => Heroicon::OutlinedCheckBadge,
            self::Rejected => Heroicon::OutlinedXCircle,
            self::Skipped => Heroicon::OutlinedNoSymbol
        };
    }
}
