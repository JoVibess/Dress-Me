<?php

namespace App\Enum;

enum CreditSourceType: string
{
    case SUBSCRIPTION = 'subscription';
    case ONE_TIME_PURCHASE = 'one_time_purchase';
}
