<?php

namespace App\Console\Commands;

use App\Mail\Announcement;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendAnnouncement extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:announcement';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    // protected $signature = 'mail:announcement';
    // protected $description = 'Queue an announcement for users';

    public function handle(): int
    {
        User::query()
            ->select(['id', 'name', 'email'])
            ->whereNotNull('email')
            ->chunkById(100, function ($users) {
                foreach ($users as $user) {
                    Mail::to($user->email, $user->name)
                        ->send(new Announcement());
                }
            });

        return self::SUCCESS;
    }
}
