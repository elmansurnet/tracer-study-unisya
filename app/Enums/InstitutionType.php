<?php
// app/Enums/InstitutionType.php
namespace App\Enums;

enum InstitutionType: string
{
    case Pemerintah = 'pemerintah';
    case Swasta     = 'swasta';
    case Bumn       = 'bumn';
    case Pendidikan = 'pendidikan';
    case Lainnya    = 'lainnya';
}