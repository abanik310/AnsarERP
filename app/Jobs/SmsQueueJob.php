<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\modules\recruitment\Models\SmsQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SmsQueueJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $datas;
    public function __construct($datas)
    {
        $this->datas = $datas;
        $this->onQueue('recruitment');
        $this->onConnection('recruitment');
    }

    /**
     * Execute the job.
     *
     *
     * @return void
     */
    public function handle()
    {
		Log::info('SMS QUEUE CHECK');
        foreach ($this->datas as $data){
			Log::info('SMS QUEUE CHECK INSIDE');
            SmsQueue::create($data);
        }
    }
}
