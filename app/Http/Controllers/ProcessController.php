<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\GoogleDriveHelper;
use Illuminate\Support\Facades\Storage;

class ProcessController extends Controller
{

    public function showFormInput(Request $request) {
        return view('google.input-form');
    }

    public function donwloadFileFromGoogleDrive(Request $request) {
        $googleDriveHelper = new GoogleDriveHelper;
        if (empty($request->fileId)) {
            return abort(500);
        }

        $fileDownload = $googleDriveHelper->DownloadLargeFileFromGoogleDrive($request->fileId);

        return response($fileDownload);
    }

    public function editFile() {
        $fileData = Storage::disk('videos')->get('en.txt');
        $fileLines = preg_split("/\\r\\n|\\r|\\n/", $fileData);
        $newData = "";
        $i = 0;
        while ($i < count($fileLines)) {
            if (!strpos($fileLines[$i], "-->") == false  || $fileLines[$i] == '') {
                $i++;
            } else {
                $newData = $newData.$fileLines[$i].PHP_EOL;
                $i++;
            }

        }
        Storage::disk('videos')->put('title.txt', $newData);
    }
}
