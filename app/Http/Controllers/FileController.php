<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
 
class FileController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function fileUpload()
    {
    	return view('fileUpload');
    }
 
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function fileUploadPost(Request $request)
    {
        $request->validate([
            'file' => 'required',
		]);
 
        $fileName = time().'.'.$request->file->getClientOriginalExtension();
 
        // $request->file->store('Files', 'videos');
        Storage::disk('videos')->putFile('Files', $request->file);
 
        return response()->json(['success'=>'You have successfully upload file.']);
    }
}