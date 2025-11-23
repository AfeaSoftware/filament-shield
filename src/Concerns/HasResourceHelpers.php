<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Concerns;

trait HasResourceHelpers
{
    public function getResourcePermissions(string $key): ?array
    {
        return array_values($this->getResourcePolicyActionsWithPermissions($key));
    }

    public function getResourcePolicyActions(string $key): ?array
    {
        return array_keys($this->getResourcePolicyActionsWithPermissions($key));
    }

    public function getResourcePermissionsWithLabels(string $key): ?array
    {
        return collect(
            data_get(
                target: $this->getResources(),
                key: "$key.permissions"
            )
        )
            ->mapWithKeys(fn (array $permission): array => [$permission['key'] => $permission['label']])
            ->toArray();
    }

    public function getResourcePolicyActionsWithPermissions(string $key): ?array
    {
        return collect(data_get(
            target: $this->getResources(),
            key: "$key.permissions"
        ))
            ->mapWithKeys(fn (array $permission, string $action): array => [$action => $permission['key']])
            ->toArray();
    }

    public function getAllResourcePermissionsWithLabels(): array
    {
        $panelId = $this->getCurrentPanelId();
        $cacheKey = "all_resource_permissions_with_labels_{$panelId}";

        if (! isset(\BezhanSalleh\FilamentShield\FilamentShield::$panelCache[$cacheKey])) {
            \BezhanSalleh\FilamentShield\FilamentShield::$panelCache[$cacheKey] = collect($this->getResources())
                ->flatMap(
                    fn (array $resource): array => $this->getResourcePermissionsWithLabels(
                        $resource['resourceFqcn']
                    )
                )
                ->toArray();
        }

        return \BezhanSalleh\FilamentShield\FilamentShield::$panelCache[$cacheKey];
    }

}
