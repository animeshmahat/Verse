<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteOldNotifications extends Command
{
    protected $signature = 'notifications:delete-old';
    protected $description = 'Delete notifications that are read and older than 48 hours';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        DB::table('notifications')
            ->where('read', true)
            ->where('read_at', '<', now()->subHours(48))
            ->delete();

        $this->info('Old notifications deleted successfully.');
    }
}
