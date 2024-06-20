<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MarkNotificationsAsRead extends Command
{
    protected $signature = 'notifications:mark-as-read';
    protected $description = 'Mark notifications as read after 72 hours of creation';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        DB::table('notifications')
            ->where('read', false)
            ->where('created_at', '<', now()->subHours(72))
            ->update(['read' => true, 'read_at' => now()]);

        $this->info('Notifications marked as read successfully.');
    }
}
