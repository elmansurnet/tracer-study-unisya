<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendTracerStudyReminders extends Command
{
    protected $signature   = 'tracer:send-reminders';
    protected $description = 'Kirim pengingat Tracer Study kepada alumni yang belum mengisi (diisi Phase 6)';

    public function handle(): int
    {
        // Implementasi lengkap di Phase 6 (Notifikasi & Integrasi)
        $this->info('Reminder Tracer Study akan diimplementasikan di Phase 6.');
        return Command::SUCCESS;
    }
}