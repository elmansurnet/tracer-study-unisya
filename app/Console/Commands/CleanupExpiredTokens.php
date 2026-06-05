<?php

namespace App\Console\Commands;

use App\Models\EmployerAccessToken;
use Illuminate\Console\Command;

class CleanupExpiredTokens extends Command
{
    protected $signature   = 'tokens:cleanup';
    protected $description = 'Hapus token employer yang sudah kadaluarsa lebih dari 30 hari';

    public function handle(): int
    {
        $deleted = EmployerAccessToken::where('expires_at', '<', now()->subDays(30))
            ->where(function ($q) {
                $q->where('is_used', true)->orWhere('is_revoked', true);
            })
            ->delete();

        $this->info("Berhasil menghapus {$deleted} token employer kadaluarsa.");
        return Command::SUCCESS;
    }
}