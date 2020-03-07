<html>

<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
    </script>
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
</head>

<body>
    <div class="container">
        <h2>Google Drive File ID</h2>
        <form action="process" method="POST" id="drive-link">
            @csrf
            <div class="form-group">
                <input type="text" class="form-control" name="fileId">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
        <div class="export-list">
            <div class="export-title">
                <h2>Queue</h2>
            </div>
            <div class="export-container">

            </div>
        </div>
    </div>
    <script>
        var progressQueue = [];
        var isQueueInActive = false;

        // setInterval(function() {
        //     if (!isQueueInActive && progressQueue.length > 0) {
        //         isQueueInActive = true;
        //         let progress = progressQueue.shift();
        //         let updateProgressBar = setInterval(async function() {
        //             let data = await $.get('./api/export-progress/' + progress.progress_id, function(data) {
        //                 return data;
        //             });
        //             $('.progress[data-id="' + progress.progress_id + '"]>.bar').width(data.percentent_progress + '%');
        //             $('.progress[data-id="' + progress.progress_id + '"]>.percent').html(data.percentent_progress + '%');
        //             if (data.percentent_progress >= 100) {
        //                 isQueueInActive = false;
        //                 clearInterval(updateProgressBar);
        //             }
        //         }, 2000);
        //     }
        // }, 2000);

    //     $('#drive-link').on('submit', function(event) {
    //         event.preventDefault();
    //         let item = `<div class="export-item">
    //                             <div class="progress" data-id="1">
    //                                 <div class="bar"></div>
    //                                 <div class="percent">0%</div>
    //                             </div>
    //                         </div>`
    //             $('.export-container').append(item);
    //         (async function(serializeForm) {
    //             let process = await $.post('./process', serializeForm, function(data) {
    //                 return data;
    //             });
    //             console.log(process);
    //             progressQueue.push(process);
    //             $('.progress[data-id="1"]').children('.percent').val('100%');
    //         })($(this).serialize());
    //     })
    // </script>
</body>

</html>