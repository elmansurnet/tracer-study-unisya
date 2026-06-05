<?php
// app/Enums/EmploymentStatus.php
namespace App\Enums;

enum EmploymentStatus: string
{
    case Bekerja          = 'bekerja';
    case Wirausaha        = 'wirausaha';
    case MelanjutkanStudi = 'melanjutkan_studi';
    case BelumBekerja     = 'belum_bekerja';
}