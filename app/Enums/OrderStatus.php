<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Open = 'open';
    case Processing = 'processing';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Open => 'Aberto',
            self::Processing => 'Em Processamento',
            self::Completed => 'Concluido',
            self::Cancelled => 'Cancelado',
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return match ($this) {
            self::Open => in_array($target, [self::Processing, self::Cancelled]),
            self::Processing => in_array($target, [self::Completed, self::Cancelled]),
            self::Completed, self::Cancelled => false,
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::Completed, self::Cancelled]);
    }

    public function color(): string
    {
        return match ($this) {
            self::Open => 'blue',
            self::Processing => 'yellow',
            self::Completed => 'green',
            self::Cancelled => 'red',
        };
    }
}
