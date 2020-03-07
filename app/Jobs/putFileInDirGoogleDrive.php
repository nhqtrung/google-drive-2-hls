<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class putFileInDirGoogleDrive implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $folderId, $fileName, $content, $disk;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($folderId, $fileName, $content, $disk)
    {
        $this->folderId = $folderId;
        $this->fileName = $fileName;
        $this->content = $content;
        $this->disk = $disk;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Storage::disk($this->disk)->put($this->folderId.'/'.$this->fileName, $this->content);
    }
}
