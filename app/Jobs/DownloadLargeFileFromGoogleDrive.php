<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Helpers\GoogleDriveHelper;
use App\Video;
use App\Export_progress;

use App\Jobs\ConvertVideoForStreaming;

class DownloadLargeFileFromGoogleDrive implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $fileId, $watermark;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($fileId, $watermark)
    {
        $this->fileId = $fileId;
        $this->watermark = $watermark;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $googleDriveHelper = new GoogleDriveHelper;
        $fileDownload = $googleDriveHelper->DownloadLargeFileFromGoogleDrive($this->fileId);
        ConvertVideoForStreaming::dispatch($this->fileId, $this->watermark);
    }
}
