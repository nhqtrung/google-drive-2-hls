<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVideoRequest;
use App\Jobs\ConvertVideoForDownloading;
use App\Jobs\ConvertVideoForStreaming;
use App\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{

    public function store(Request $request)
    {
        // $video = Video::create([
        //     'disk'          => 'videos',
        //     'original_name' => $request->video->getClientOriginalName(),
        //     'path'          => $request->video->store('videos', 'videos_disk'),
        //     'title'         => $request->title,
        // ]);

        $video = new Video;
        $video->disk = 'videos';
        $video->original_name = $request->video->getClientOriginalName();
        $video->path = $request->video->store('Test', 'videos');
        $video->title = $request->title;
        $video->save();

        // $this->dispatch(new ConvertVideoForDownloading($video));
        $this->dispatch(new ConvertVideoForStreaming($video));

        return response()->json([
            'id' => $video->id,
        ], 201);
    }

}
