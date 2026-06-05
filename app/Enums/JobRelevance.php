<?php
// app/Enums/JobRelevance.php
namespace App\Enums;

enum JobRelevance: string
{
    case SangatRelevan  = 'sangat_relevan';
    case Relevan        = 'relevan';
    case KurangRelevan  = 'kurang_relevan';
    case TidakRelevan   = 'tidak_relevan';
}