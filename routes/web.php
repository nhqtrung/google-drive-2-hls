<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/




Route::get('hls_file', 'convertController@getFile');

Route::get('list', function() {
    $dir = '/';
    $recursive = false; // Get subdirectories also?
    $contents = collect(Storage::cloud()->listContents($dir, $recursive));

    return $contents->where('type', '=', 'file'); // files
});

Route::get('form-rewrite', 'convertController@rewriteM3U8Form');

Route::get('list-folder-contents', 'convertController@getFileInFolder');

Route::get('put', function() {
    Storage::cloud()->put('test.txt', 'Hello World');
    return 'File was saved to Google Drive';
});

Route::get('put-existing', function() {
    $filename = '02_500_00001.ts';
    $filePath = public_path($filename);
    $fileData = File::get($filePath);

    Storage::cloud()->put('Entycrypted_500_0001.txt', $fileData);

    return 'File was saved to Google Drive';
});

Route::get('get', function() {
    $googleDriveHelper = new App\Helpers\GoogleDriveHelper;

    // Get the files inside the folder...
    $files = $googleDriveHelper->getAllFileFromOriginnalPath($googleDriveFolder, 'google');
    
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
    Storage::disk('converted_videos')->put($rootPathFolder.'/index.m3u8', $newData);
});

// Route::get('hls/{fileId}', 'HLSController@redirectToStreamLink'); 

Route::get('hls/{rStart}/{rEnd}/{fileId}', 'HLSController@redirectToStreamLink');

Route::get('/', 'convertController@showFormInfo');

Route::post('/', 'convertController@exportVideoForHls');

Route::get('file-upload', 'FileController@fileUpload');

Route::post('file-upload', 'FileController@fileUploadPost')->name('fileUploadPost');

Route::get('combine-file', 'HLSController@combineFile');

Route::get('download', function() {
    $filename = 'file55';


    $dir = 'Temp/Combine_Video';
    $recursive = false; // Get subdirectories also?
    $contents = collect(Storage::cloud()->listContents($dir, $recursive));

    $file = $contents
        ->where('type', '=', 'file')
        ->where('filename', '=', 'file55')
        ->first(); // there can be duplicate file names!

    //return $file; // array with file info

    $rawData = Storage::cloud()->get($file['path']);

    return response($rawData, 200)
        ->header('ContentType', $file['mimetype'])
        ->header('Content-Disposition', "attachment; filename='$filename'");
});

Route::get('test1', 'HLSController@test');

Route::get('/process', 'ProcessController@showFormInput');
Route::post('/process', 'ProcessController@donwloadFileFromGoogleDrive');
Route::get('/edit-file', 'ProcessController@editFile');

Route::get('/player', 'HLSController@getPlayer');

Route::get('/register-lika', 'HLSController@registerLika');
Route::get('/crawl-lika', 'HLSController@crawlLika');