<?php

declare(strict_types=1);

namespace App\Services\Api\Store;

use App\Models\Car;
use App\Services\Api\Store\Helpers\SlugResolver;

final class CompareApiService
{
    public function compare(string $slug1, string $slug2): array
    {
        $car1 = $this->loadCar($slug1);
        $car2 = $this->loadCar($slug2);

        if (! $car1 || ! $car2) {
            return [];
        }

        return $this->buildSections($car1, $car2);
    }

    private function loadCar(string $slug): ?Car
    {
        $query = Car::with(['brand', 'specifications', 'features_list'])
            ->where('is_active', true);

        SlugResolver::applyCarSlug($query, $slug);

        return $query->first();
    }

    private function buildSections(Car $car1, Car $car2): array
    {
        $specs1 = $car1->specs ?? [];
        $specs2 = $car2->specs ?? [];

        return array_filter([
            $this->priceSection($car1, $car2),
            $this->performanceSection($specs1, $specs2),
            $this->designSection($car1, $car2, $specs1, $specs2),
            $this->checkSection(
                __('store-api.compare.sections.features'),
                'features_list',
                $car1,
                $car2,
            ),
            $this->checkSection(
                __('store-api.compare.sections.specs'),
                'specifications',
                $car1,
                $car2,
            ),
        ]);
    }

    private function priceSection(Car $car1, Car $car2): array
    {
        return [
            'title' => __('store-api.compare.sections.price'),
            'rows' => [
                $this->row(__('store-api.compare.labels.price'), $car1->cash_price, $car2->cash_price, 'price', 'lower'),
                $this->row(__('store-api.compare.labels.installment'), $car1->min_installment, $car2->min_installment, 'price', 'lower'),
            ],
        ];
    }

    private function performanceSection(array $specs1, array $specs2): array
    {
        return [
            'title' => __('store-api.compare.sections.performance'),
            'rows' => [
                $this->row(__('store-api.compare.labels.horsepower'), $specs1['hp'] ?? null, $specs2['hp'] ?? null, 'unit', 'higher', __('store-api.compare.units.hp')),
                $this->row(__('store-api.compare.labels.max_speed'), $specs1['max_speed'] ?? null, $specs2['max_speed'] ?? null, 'unit', 'higher', __('store-api.compare.units.kmh')),
                $this->row(__('store-api.compare.labels.acceleration'), $specs1['acceleration'] ?? null, $specs2['acceleration'] ?? null, 'unit', 'lower', __('store-api.compare.units.seconds')),
            ],
        ];
    }

    private function designSection(Car $car1, Car $car2, array $specs1, array $specs2): array
    {
        return [
            'title' => __('store-api.compare.sections.design'),
            'rows' => [
                $this->row(__('store-api.compare.labels.type'), $car1->type, $car2->type, 'text'),
                $this->row(__('store-api.compare.labels.seats'), $specs1['seats'] ?? null, $specs2['seats'] ?? null, 'unit', 'neutral', __('store-api.compare.units.seat')),
                $this->row(__('store-api.compare.labels.gearbox'), $specs1['gearbox'] ?? null, $specs2['gearbox'] ?? null, 'text'),
            ],
        ];
    }

    private function checkSection(string $title, string $relation, Car $car1, Car $car2): ?array
    {
        $items1 = $car1->$relation->pluck('name')->toArray();
        $items2 = $car2->$relation->pluck('name')->toArray();
        $all = array_unique(array_merge($items1, $items2));

        if (empty($all)) {
            return null;
        }

        return [
            'title' => $title,
            'rows' => array_map(fn ($item) => [
                'label' => $item,
                'val1' => in_array($item, $items1) ? '✓' : '✗',
                'val2' => in_array($item, $items2) ? '✓' : '✗',
                'type' => 'check',
                'winner' => match (true) {
                    in_array($item, $items1) && ! in_array($item, $items2) => 1,
                    ! in_array($item, $items1) && in_array($item, $items2) => 2,
                    default => 0,
                },
            ], $all),
        ];
    }

    private function row(string $label, mixed $val1, mixed $val2, string $type = 'text', string $compare = 'neutral', string $unit = ''): array
    {
        $winner = 0;

        if ($compare !== 'neutral' && $val1 !== null && $val2 !== null) {
            $n1 = (float) preg_replace('/[^0-9.]/', '', (string) $val1);
            $n2 = (float) preg_replace('/[^0-9.]/', '', (string) $val2);

            if ($n1 !== $n2) {
                $winner = match ($compare) {
                    'higher' => $n1 > $n2 ? 1 : 2,
                    'lower' => $n1 < $n2 ? 1 : 2,
                    default => 0,
                };
            }
        }

        return [
            'label' => $label,
            'val1' => $this->format($val1, $type, $unit),
            'val2' => $this->format($val2, $type, $unit),
            'type' => $type,
            'winner' => $winner,
        ];
    }

    private function format(mixed $value, string $type, string $unit): string
    {
        if ($value === null) {
            return '—';
        }

        $riyal = __('store-api.compare.units.riyal');

        return match ($type) {
            'price' => number_format((float) $value)." {$riyal}",
            'unit' => $value." {$unit}",
            default => (string) $value,
        };
    }
}
