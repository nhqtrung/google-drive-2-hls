<?php 
namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;
use GuzzleHttp\RedirectMiddleware;
use GuzzleHttp\RequestOptions;

class GoogleDriveHelper
{
	public function getFolderIdFromOriginalPath($path, $disk){
        $folderLevel = explode("/", $path);
        $previousDirectory = '/';
        $recursive = false; // Get subdirectories also?
        $previousContents = collect(Storage::disk($disk)->listContents($previousDirectory, $recursive));
        // dd($previousContents);
        for ($i = 0; $i < count($folderLevel); $i++) {
            $nextDirectory = $previousContents->where('type', '=', 'dir')
                                            ->where('filename', '=', $folderLevel[$i])
                                            ->first(); // There could be duplicate directory names!
            if ( ! $nextDirectory ) {
                $previousDirectoryPath = $previousDirectory['path'] ?? "" ;
                $folderPath = $previousDirectoryPath . '/' . $folderLevel[$i];

                Storage::disk($disk)->makeDirectory($folderPath);


                $previousContents = collect(Storage::disk($disk)->listContents($previousDirectoryPath, $recursive));
                $previousDirectory = $previousContents->where('type', '=', 'dir')
                                                ->where('filename', '=', $folderLevel[$i])
                                                ->first();
                $previousContents = collect(Storage::disk($disk)->listContents($previousDirectory['path'], $recursive));
            } else {
                $previousDirectory = $nextDirectory;
                $previousContents = collect(Storage::disk($disk)->listContents($previousDirectory['path'], $recursive));
            }
        }
        return $previousDirectory['path'];
    }
    
    public function getAllFileFromOriginnalPath($path, $disk){
        $folderLevel = explode('/', $path);
        $previousDirectory = '/';
        $recursive = false; // Get subdirectories also?
        $previousContents = collect(Storage::disk($disk)->listContents($previousDirectory, $recursive));

        for ($i = 0; $i < count($folderLevel); $i++) {
            $nextDirectory = $previousContents->where('type', '=', 'dir')
                            ->where('filename', '=', $folderLevel[$i])
                            ->first(); // There could be duplicate directory names!
            if ( ! $nextDirectory ) {
                return 'No directory name: '.$folderLevel[$i];
            } else {
                $previousDirectory = $nextDirectory;
                $previousContents = collect(Storage::disk($disk)->listContents($previousDirectory['path'], $recursive));
            }
        }

        return $previousContents->where('type', '=', 'file');
    }

    public function DownloadLargeFileFromGoogleDrive($fileId) {
        $client = new Client();
        $confirmCode;
        $jar;
        if (empty($fileId)) {
            return [];
        }
        $response = $client->get("https://drive.google.com/uc?export=download&id=".$fileId);
  
        if ($response->getStatusCode() == 200) {
            $cookie = $response->getHeader('set-cookie')[0];
            $cookieJar = SetCookie::fromString($cookie);
            $jar = new CookieJar();
            $jar->setCookie(SetCookie::fromString($cookie));
            if (strpos($cookieJar->getName(), "download_warning_") !== false) {

                $confirmCode = $cookieJar->getValue();

                $client2 = new Client(['cookies' => $jar]);

                $response2 = $client2->get('https://drive.google.com/uc?export=download&id='.$fileId.'&confirm='.$confirmCode);

                $params = explode(";", $response2->getHeader('Content-Disposition')[0]);
                
                Storage::disk('videos')->put('download/google_drive/'.$fileId, $response2->getBody());


            }
        }
        return $fileId;
    }


}