<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event\Booking;
use Illuminate\Support\Facades\DB;

class CheckEventPaymentPending extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'event:pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cron check event booking payment status pending';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $list = DB::select("SELECT *, (TIMESTAMPDIFF(HOUR,created_at,CURRENT_TIMESTAMP())) AS selisih 
                            FROM `bookings` 
                            WHERE paymentStatus='pending' AND (TIMESTAMPDIFF(HOUR,created_at,CURRENT_TIMESTAMP()))>=24 
                            ORDER BY `selisih` DESC");
        foreach($list as $row){
            // update paymentStatus = expired, ticket update, transaction update
            var_dump($row->id);
        }die;
    }
}
