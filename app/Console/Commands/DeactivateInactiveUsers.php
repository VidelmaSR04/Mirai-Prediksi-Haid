<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Mobile\User;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DeactivateInactiveUsers extends Command
{
    protected $signature = 'users:deactivate-inactive';
    protected $description = 'Menonaktifkan user yang tidak aktif selama 1 tahun';

    public function handle()
    {
        $oneYearAgo = Carbon::now()->subYear();
        
        $inactiveUsers = User::where('last_login_at', '<', $oneYearAgo)
            ->orWhereNull('last_login_at')
            ->where('status', 'Aktif')
            ->get();
        
        $count = 0;
        foreach ($inactiveUsers as $user) {
            $user->status = 'Tidak Aktif';
            $user->save();
            $count++;
            
            $this->info("User {$user->email} dinonaktifkan (terakhir login: {$user->last_login_at})");
        }
        
        $this->info("Total {$count} user telah dinonaktifkan.");
        
        // Log untuk admin
        Log::info('Auto-deactivation completed', [
            'users_deactivated' => $count,
            'date' => Carbon::now()
        ]);
    }
}