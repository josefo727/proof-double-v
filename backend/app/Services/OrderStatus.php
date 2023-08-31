<?php

namespace App\Services;

class OrderStatus
{
    public const CREATED = 'created';
    public const ACCEPTED = 'accepted';
    public const DISTRIBUTION = 'distribution';
    public const DELIVERED = 'delivered';
    public const CANCELLED = 'cancelled';

    private $name;

    public function __construct(string $name)
    {
        if (!self::isValid($name)) {
            throw new \InvalidArgumentException("Estado inválido");
        }
        $this->name = $name;
    }
    /**
     * @return array<int,string>
     */
    public static function getAll(): array
    {
        return [
            self::CREATED,
            self::ACCEPTED,
            self::DISTRIBUTION,
            self::DELIVERED,
            self::CANCELLED,
        ];
    }

    public static function isValid(string $name): bool
    {
        return in_array($name, [
            self::CREATED,
            self::ACCEPTED,
            self::DISTRIBUTION,
            self::DELIVERED,
            self::CANCELLED,
        ]);
    }
    /**
     * @return array<string,array<int,string>>
     */
    public static function getTransitions(): array
    {
        return [
            self::CREATED => [self::ACCEPTED, self::CANCELLED],
            self::ACCEPTED => [self::DISTRIBUTION, self::CANCELLED],
            self::DISTRIBUTION => [self::DELIVERED, self::CANCELLED],
        ];
    }

    public static function getAcceptedValuesForTransaction()
    {
        return collect(array_values(OrderStatus::getTransitions()))
            ->collapse()
            ->unique()
            ->values()
            ->toArray();
    }
    /**
     * @return array<string,string>
     */
    public static function getLabels(): array
    {
        return [
            self::CREATED => 'Creada',
            self::ACCEPTED => 'Aceptada',
            self::DISTRIBUTION => 'En reparto',
            self::DELIVERED => 'Entregada',
            self::CANCELLED => 'Cancelada',
        ];
    }

    public function getLabel(): string
    {
        $labels = self::getLabels();
        return $labels[$this->name] ?? 'Desconocido';
    }

    public function canChangeStatus(string $newName): bool
    {
        $allowedTransitions = self::getTransitions()[$this->name] ?? [];

        return in_array($newName, $allowedTransitions);
    }

    public function is(string $name): bool
    {
        return $this->name === $name;
    }

    public function getNotification(): string
    {
        return match($this->name) {
            self::CREATED => 'La orden ha sido creada',
            self::ACCEPTED => 'La orden ha sido aceptada',
            self::DISTRIBUTION => 'La orden está en reparto',
            self::DELIVERED => 'La orden ha sido entregada',
            self::CANCELLED => 'La orden ha sido cancelada',
        };
    }
}
