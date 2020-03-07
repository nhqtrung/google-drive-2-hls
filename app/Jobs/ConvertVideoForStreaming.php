<?php

namespace App\Jobs;

use App\Video;
use Carbon\Carbon;
use FFMpeg;
use FFMpeg\Format\Video\X264;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Jobs\putFileInDirGoogleDrive;
use App\Jobs\rewriteM3U8File;
use App\Export_progress;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Helpers\GoogleDriveHelper;

class ConvertVideoForStreaming implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $fileId, $watermark;

    public function __construct($fileId, $watermark)
    {
        $this->fileId = $fileId;
        $this->watermark = $watermark;
    }

    public function handle()
    {
        $mainDisk = 'google';
        $backupDisk = 'backup_google';
        $inputPath = 'download/google_drive/'.$this->fileId;
        $folder = 'google_drive/'.$this->fileId;
        $googleDriveFolder = 'google_drive/'.$this->fileId;
        $googleDriveHelper = new GoogleDriveHelper;

        // create some video formats...
        $lowBitrateFormat  = (new X264('aac'))->setKiloBitrate(1500);
        $midBitrateFormat  = (new X264('aac'))->setKiloBitrate(2400);
        $highBitrateFormat = (new X264('aac'))->setKiloBitrate(4200);

        $ffmpegExportHLS = FFMpeg::fromDisk('videos')
            ->open($inputPath)
            ->addFilter(function ($filters) {
                $filters->resize(new \FFMpeg\Coordinate\Dimension(1280, 720));
            });

        if ($this->watermark != 0 || $this->watermark != "0") {
            $ffmpegExportHLS->addFilter(function ($filters) {
                $watermarkPath = $this->watermark;
                $filters->watermark($watermarkPath, [
                    'position' => 'relative',
                    'bottom' => 0,
                    'right' => 0,
                ]);
            });
        }


        $ffmpegExportHLS->exportForHLS()
            ->setSegmentLength(10)
            // ->onProgress(function ($percentage) {
            //     $this->export_progress->percentent_progress = $percentage;
            //     $this->export_progress->save();
            // })
            ->toDisk('converted_videos')
            ->addFormat($lowBitrateFormat)
            // ->addFormat($midBitrateFormat)
            // ->addFormat($highBitrateFormat)
            ->save($folder.'/EncryptedDocument_T5.m3u8');

        $mainDiskFolderId = $googleDriveHelper->getFolderIdFromOriginalPath($googleDriveFolder, $mainDisk);
        $backupDiskFolderId = $googleDriveHelper->getFolderIdFromOriginalPath($googleDriveFolder, $backupDisk);


        //change all file extension from ts to txt
        $hls_playlist = Storage::disk('converted_videos')->files($folder);
        $storagePath = Storage::disk('converted_videos')->getAdapter()->getPathPrefix();

        $content = "";
        $fileSize = -1;
        $fileIndex = 0;

        $m3u8File = Storage::disk('converted_videos')->get($folder.'/EncryptedDocument_T5_1500.m3u8');

        foreach($hls_playlist as $file) {
            $file = str_replace('/', '\\', $file);
            $fileInfo = explode('\\', $file);
            $fileInfo = $fileInfo[sizeof($fileInfo) - 1];
            $fileInfo = explode(".", $fileInfo);
            $fileName = $fileInfo[sizeof($fileInfo) - 2];
            $fileExtension = $fileInfo[sizeof($fileInfo) - 1];

            if ($fileExtension == 'ts') {

                $m3u8File = str_replace($fileName.'.ts', "file".$fileIndex." ".($fileSize + 1)." ".($fileSize + Storage::disk('converted_videos')->size($file)), $m3u8File);
                $fileSize = $fileSize + Storage::disk('converted_videos')->size($file);
                $content = $content.Storage::disk('converted_videos')->get($file);
                if ($fileSize > (12*1024*1024)) {
                    Storage::disk('google')->put($mainDiskFolderId.'/file'.$fileIndex, $content);
                    $content = "";
                    $fileSize = -1;
                    $fileIndex++;
                }

            }


        }

        if (!empty($content)) {
            Storage::disk('google')->put($mainDiskFolderId.'/file'.$fileIndex, $content);
        }
        Storage::disk('converted_videos')->deleteDirectory($folder);
        Storage::disk('converted_videos')->put($folder.'/index.m3u8', $m3u8File);

        rewriteM3U8File::dispatch($folder, $googleDriveFolder, $mainDisk);
    }
}