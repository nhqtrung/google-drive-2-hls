<html>

<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
        crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
        crossorigin="anonymous"></script>
</head>

<body>
    <style>
        .progress {
            position: relative;
            width: 100%;
            border: 1px solid #ddd;
            padding: 1px;
            border-radius: 3px;
            height: 28px;
        }
        .bar {
            background-color: #B4F5B4;
            width: 0%;
            height: 28px;
            border-radius: 3px;
        }
        .percent {
            position: absolute;
            display: inline-block;
            top: 3px;
            left: 48%;
        }
    </style>
    <div class="container">
        <h2>Chọn File và folder lưu để bắt đầu export cho stream</h2>
        <form action="./" method="POST" id="video-info-form">
            @csrf
            {{-- <div class="form-group">
                <label>Input File</label>
                <select class="form-control" name="input-path" id="input-path">
                    @foreach ($listFileInput as $filePath)
                        <option value="{{ $filePath }}">{{ $filePath }}</option>
                    @endforeach
                </select>
            </div> --}}
            <div class="form-group">
                <label>FileId</label>
                <input type="text" class="form-control" name="fileId" id="output-path">
            </div>


            <div class="form-group">
                <label>Watermark</label>
                <select name="watermark" id="watermark" class="form-control">
                    <option value="0">None</option>
                    <option value="D\\:\\/xampp\\/htdocs\\/li1cdn\\/public\\/logo\\/cab17-1280.png">Cab 17 1280x720</option>
                    <option value="D\\:\\/xampp\\/htdocs\\/li1cdn\\/public\\/logo\\/htvcphunu-1280.png">HTVC Phu Nu 1280x720</option>
                    <option value="D\\:\\/xampp\\/htdocs\\/li1cdn\\/public\\/logo\\/vtc4-1280.png">VTC4 1280x720</option>
                    <option value="D\\:\\/xampp\\/htdocs\\/li1cdn\\/public\\/logo\\/btv4-1280.png">BTV4 1280x720</option>
                    <option value="D\\:\\/xampp\\/htdocs\\/li1cdn\\/public\\/logo\\/btv3-1280.png">BTV3 1280x720</option>
                </select>
            </div>            

            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
        <div class="export-list">
            <div class="export-title"><h2>Danh sách File Export</h2></div>
            <div class="export-container">

            </div>
        </div>
    </div>
    <script>
        // $('#input-path').on('change', function() {
            
        //     let path = $(this).val();
        //     path = path.split('/');
        //     let filename = path[path.length - 1].split('.');
        //     let recommendFolderName = '';
        //     for (let i=0; i < path.length - 1; i++) {
        //         recommendFolderName += path[i] + '/';
        //     }
        //     for (let j=0; j < filename.length - 1; j++) {
        //         recommendFolderName += filename[j];
        //     }
        //     $('#output-path').val(recommendFolderName);
        //     $('#google-drive-folder').val(recommendFolderName);
        // })

    //     var progressQueue = [];
    //     var isQueueInActive = false;

    //     setInterval(function() {
    //         if (!isQueueInActive && progressQueue.length > 0) {
    //             isQueueInActive = true;
    //             let progress = progressQueue.shift();
    //             let updateProgressBar = setInterval(async function() {
    //                 let data = await $.get('./api/export-progress/' + progress.progress_id, function(data) {
    //                     return data;
    //                 });
    //                 $('.progress[data-id="' + progress.progress_id + '"]>.bar').width(data.percentent_progress + '%');
    //                 $('.progress[data-id="' + progress.progress_id + '"]>.percent').html(data.percentent_progress + '%');
    //                 if (data.percentent_progress >= 100) {
    //                     isQueueInActive = false;
    //                     clearInterval(updateProgressBar);
    //                 }
    //             }, 2000);
    //         }
    //     }, 2000);

    //     $('#video-info-form').on('submit', function(event) {
    //         event.preventDefault();

    //         (async function(serializeForm) {
    //             let progress = await $.post('./', serializeForm, function(data) {
    //                 return data;
    //             });
    //             progressQueue.push(progress);
    //             let item = `<div class="export-item">
    //                             <h4>${progress.video_path} => ${progress.folder_path}</h4>
    //                             <div class="progress" data-id="${progress.progress_id}">
    //                                 <div class="bar"></div>
    //                                 <div class="percent">0%</div>
    //                             </div>
    //                         </div>`
    //             $('.export-container').append(item);
    //         })($(this).serialize());
    //     });
    // </script>
</body>
</html