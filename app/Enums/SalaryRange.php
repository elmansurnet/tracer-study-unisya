<?php
// app/Enums/SalaryRange.php
namespace App\Enums;

enum SalaryRange: string
{
    case BawahSatuJuta  = '<1jt';
    case SatuTigaJuta   = '1-3jt';
    case TigaLimaJuta   = '3-5jt';
    case LimaSeputuhJuta = '5-10jt';
    case AtasSeputuhJuta = '>10jt';

    public function label(): string
    {
        return match($this) {
            self::BawahSatuJuta   => '< Rp 1.000.000',
            self::SatuTigaJuta    => 'Rp 1.000.000 - Rp 3.000.000',
            self::TigaLimaJuta    => 'Rp 3.000.000 - Rp 5.000.000',
            self::LimaSeputuhJuta => 'Rp 5.000.000 - Rp 10.000.000',
            self::AtasSeputuhJuta => '> Rp 10.000.000',
        };
    }
}