<?php

namespace App\Console\Commands;

use App\Jobs\SendEmail;
use App\Models\User;
use Illuminate\Console\Command;

class SendNewsLetter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:newsletter';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Newsletter';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $users = User::all();

        $users->map(function ($user) {
            if ($user->is_subscribed) {
                // Dispatch the queue
                SendEmail::dispatch($user->email, $user->name);
            }
        });
    }
}
