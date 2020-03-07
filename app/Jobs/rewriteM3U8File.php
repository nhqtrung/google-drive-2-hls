<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Jobs\putFileInDirGoogleDrive;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Helpers\GoogleDriveHelper;

class rewriteM3U8File implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $googleDriveFolder, $rootPathFolder, $disk;

    /**
     * Create a new job instance.
     *
     * @return void
     */


     
    public function __construct($rootPathFolder ,$googleDriveFolder, $disk)
    {
        $this->rootPathFolder = $rootPathFolder;
        $this->googleDriveFolder = $googleDriveFolder;
        $this->disk = $disk;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $googleDriveHelper = new GoogleDriveHelper;

        // Get the files inside the folder...
        $files = $googleDriveHelper->getAllFileFromOriginnalPath($this->googleDriveFolder, $this->disk);
        
        $listFile = $files->mapWithKeys(function($file) {
            $filename = $file['filename'];
            $path = $file['path'];
            $parsePath = explode("/", $path);
            $fileId = $parsePath[sizeof($parsePath) - 1];
            return [$filename => $file];
        });

        $m3u8File = Storage::disk('converted_videos')->get($this->rootPathFolder."/index.m3u8");
        $storagePath = Storage::disk('converted_videos')->getAdapter()->getPathPrefix();
        $fileLines = preg_split("/\\r\\n|\\r|\\n/", $m3u8File);
        $newData = "";
        $i = 0;
        while ($i < count($fileLines)) {
            if (strpos($fileLines[$i], "EXTINF")) {
                $newData = $newData.$fileLines[$i].PHP_EOL;
                $i++;
                $fileInfoSerialize = explode(" " ,$fileLines[$i]);

                $fileInf = $listFile[$fileInfoSerialize[0]];
                $path = $fileInf['path'];
                $parsePath = explode("/", $path);
                $fileId = $parsePath[sizeof($parsePath) - 1];

                $fileUrl = 'http://localhost:8080/li1cdn/public/hls/'.$fileInfoSerialize[1]."/".$fileInfoSerialize[2]."/".$fileId;
                $newData = $newData.$fileUrl.PHP_EOL;
                $i++;
            } else {
                $newData = $newData.$fileLines[$i] . PHP_EOL;
                $i++;
            }
        }
        Storage::disk('converted_videos')->put($this->rootPathFolder.'/final.m3u8', $newData);
    }
}

