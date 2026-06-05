<?php

namespace App\Console\Commands;

use App\Services\OtpService;
use Illuminate\Console\Command;

class CleanupExpiredOtp extends Command
{
    protected $signature   = 'otp:cleanup';
    protected $description = 'Hapus OTP yang sudah kadaluarsa atau sudah digunakan (lebih dari 24 jam)';

    public function handle(OtpService $otpService): int
    {
        $deleted = $otpService->cleanupExpired();
        $this->info("Berhasil menghapus {$deleted} OTP kadaluarsa.");
        return Command::SUCCESS;
    }
}