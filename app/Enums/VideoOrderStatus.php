<?php

namespace App\Enums;

enum VideoOrderStatus: string
{
    case New = 'new';
    case Confirmed = 'confirmed';
    case InProduction = 'in_production';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';
}
