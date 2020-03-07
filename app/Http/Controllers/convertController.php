<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use FFMpeg;
use FFMpeg\Format\Video\X264;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use SergeyHartmann\StreamingLinkGenerator\Generator;
use SergeyHartmann\StreamingLinkGenerator\StreamingLink;
use SergeyHartmann\StreamingLinkGenerator\CookieLoader\SimpleCookieLoader;
use App\Jobs\putFileInDirGoogleDrive;
use App\Jobs\rewriteM3U8File;
use App\Jobs\ConvertVideoForStreaming;
use App\Jobs\DownloadLargeFileFromGoogleDrive;
use App\Video;
use App\Export_progress;
use App\Helpers\GoogleDriveHelper;
class convertController extends Controller
{
    private $generator;
    private $cookieLoader;
    private $exportProgress, $video;
    public function __construct()
    {
        $this->cookieLoader = new SimpleCookieLoader(dirname(__FILE__) . '/g.cookie');
        $this->generator    = new Generator($this->cookieLoader);
        $this->exportProgress = new Export_progress;
        $this->video = new Video;
    }

    public function showFormInfo() {
        $fileInVideoDisk = Storage::disk('videos')->allFiles();
        return view('form-info', ['listFileInput' => $fileInVideoDisk]);
    }

    public function exportVideoForHls(Request $request) {
        $fileId = $request['fileId'];
        $watermark = $request['watermark'];
        if (!Storage::disk('videos')->exists('download/google_drive/'.$fileId)) {
            DownloadLargeFileFromGoogleDrive::dispatch($fileId, $watermark);
        }
        return response()->json([
            'm3u8' => "http://localhost:8080/li1cdn/public/converted_videos/google_drive/$fileId/final.m3u8",
            'player' => "http://localhost:8080/li1cdn/public/player?m3u8=http://localhost:8080/li1cdn/public/converted_videos/google_drive/$fileId/final.m3u8" 
        ]);
    }


    public function getFile() {
        $hls_playlist = Storage::disk('converted_videos')->files("test/05");
        $storagePath = Storage::disk('converted_videos')->getAdapter()->getPathPrefix();
        foreach($hls_playlist as $item) {
            $itemPath = $storagePath.$item;
            $fileData = File::get($itemPath);
            

            $file = explode("/", $item);
            $filename = $file[sizeof($file) - 1];
            if (strpos($filename, 'txt')) {
                Storage::cloud()->put($filename, $fileData);
            }
        }
        return 'Put file to Google Drive Success';
    }

    public function rewriteM3U8Form() {
        return view('form-rewrite-m3u8');
    }

    public function getFileInFolder(Request $request) {
        $googleDriveFolder = new GoogleDriveHelper;

        // Get the files inside the folder...
        $files = $googleDriveFolder->getAllFileFromOriginnalPath('Test/Test_Video', 'google');
        
        $listFile = $files->mapWithKeys(function($file) {
            $filename = $file['filename'];
            $path = $file['path'];
            $parsePath = explode("/", $path);
            $fileId = $parsePath[sizeof($parsePath) - 1];
            return [$filename => $file];
        });

        $m3u8File = Storage::disk('converted_videos')->get('Test/Test_Video/'."index.m3u8");
        $hls_playlist = Storage::disk('converted_videos')->files('Test/Test_Video');
        $storagePath = Storage::disk('converted_videos')->getAdapter()->getPathPrefix();
        foreach($hls_playlist as $item) {
            $filePath = $storagePath.$item;
            if (strpos($item, 'index.m3u8')) {
                $fileData = File::get($filePath);
                $fileLines = preg_split("/\\r\\n|\\r|\\n/", $fileData);
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
                Storage::disk('converted_videos')->put('Test/Test_Video/index.m3u8', $newData);
            }
        }
    }

    public function VideoExportProgressAPI($progressId) {
        $exportProgress = Export_progress::find($progressId);
        return response()->json(['videoId' => $exportProgress->idVideo, 'percentent_progress' => $exportProgress->percentent_progress]);
    }

    public function VideosExportInProgressAPI() {
        $exportProgress = Export_progress::with('video')->get();
        return response()->json($exportProgress);
    }

    public function test() {
        $googleDriveHelper = new GoogleDriveHelper;
        dd($googleDriveHelper->getFolderIdFromOriginalPath('phim','google'));
    }
}
