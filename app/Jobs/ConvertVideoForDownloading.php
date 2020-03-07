<?php

namespace App\Jobs;

use App\Video;
use Carbon\Carbon;
use FFMpeg;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\Format\Video\X264;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ConvertVideoForDownloading implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $video;

    public function __construct(Video $video)
    {
        $this->video = $video;
    }

    public function handle()
    {
        // create a video format...
        $lowBitrateFormat = (new X264)->setKiloBitrate(500);

        // open the uploaded video from the right disk...
        FFMpeg::fromDisk($this->video->disk)
            ->open($this->video->path)

        // add the 'resize' filter...
            ->addFilter(function ($filters) {
                $filters->resize(new Dimension(960, 540));
            })

        // call the 'export' method...
            ->export()

        // tell the MediaExporter to which disk and in which format we want to export...
            ->toDisk('local')
            ->inFormat($lowBitrateFormat)

        // call the 'save' method with a filename...
            ->save($this->video->id . '.mp4');

        // update the database so we know the convertion is done!
        $this->video->update([
            'converted_for_downloading_at' => Carbon::now(),
        ]);
    }
}