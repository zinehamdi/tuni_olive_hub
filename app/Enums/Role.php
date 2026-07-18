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
    case Transiteur = 'transiteur';
    case Comptable = 'comptable';
    case ServiceBureau = 'service_bureau';
    case AgriEquipment = 'agri_equipment';
    case AgriMaterials = 'agri_materials';
    case AgriStudyOffice = 'agri_study_office';
}
