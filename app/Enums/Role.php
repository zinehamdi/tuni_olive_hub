<?php

declare(strict_types=1);

namespace App\Enums;

enum Role: string
{
    case Farmer = 'farmer';
    case Mill = 'mill';
    case Packer = 'packer';
    case Carrier = 'carrier';
    case TraderCarrier = 'trader_carrier';
    case Restaurant = 'restaurant';
    case Consumer = 'consumer';
    case Exporter = 'exporter';
    case Admin = 'admin';
}
