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
        <form action="./list-folder-contents" method="GET" >
            <div class="form-group">
                <label>Google Drive Folder</label>
                <input type="text" class="form-control" name="googleDriveFolder" >
            </div>

            <div class="form-group">
                <label>Disk</label>
                <input type="text" class="form-control" name="disk" >
            </div>

            <div class="form-group">
                <label>Folder Root</label>
                <input type="text" class="form-control" name="rootPathFolder" id="output-path">
            </div>

            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
        
        


    </div>

</body>
</html