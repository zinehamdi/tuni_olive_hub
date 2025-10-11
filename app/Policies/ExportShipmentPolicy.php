<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ExportShipment;
use App\Models\User;

class ExportShipmentPolicy
{
    protected function isExporterOrAdmin(User $user): bool
    {
        return in_array($user->role, ['exporter','admin'], true);
    }

    public function create(User $user): bool { return $this->isExporterOrAdmin($user); }
    public function update(User $user, ExportShipment $shipment): bool { return $this->isExporterOrAdmin($user); }
    public function transition(User $user, ExportShipment $shipment): bool { return $this->isExporterOrAdmin($user); }
    public function attachDocument(User $user, ExportShipment $shipment): bool { return $this->isExporterOrAdmin($user); }
    public function verifyDocument(User $user, ExportShipment $shipment): bool { return $this->isExporterOrAdmin($user); }
}
