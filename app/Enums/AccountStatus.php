<?php

namespace App\Enums;

/**
 * Shared between User and Admin - "active/suspended/banned"
 */
enum AccountStatus: string
{
    case Active = 'active';
    case Suspended = 'suspended';
    case Banned = 'banned';
}
