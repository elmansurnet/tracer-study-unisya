<?php
// app/Enums/OtpPurpose.php
namespace App\Enums;

enum OtpPurpose: string
{
    case Login          = 'login';
    case EmployerAccess = 'employer_access';
    case PhoneVerify    = 'phone_verify';
    case EmailVerify    = 'email_verify';
}