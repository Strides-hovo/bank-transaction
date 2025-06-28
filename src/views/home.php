<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.min.css"/>
    <link rel="stylesheet" href="https://editor.datatables.net/css/editor.dataTables.css"/>
    <link rel="stylesheet" href="../../assets/css/style.css">

    <title><?= $title ?> </title>
</head>
<body>
<div class="container">
    <header><?= $header ?></header>
    <main>

        <div class="block-one">

            <div class="upload-block col-7">
                <label class="browse-btn" for="excelFile">Browse files</label>
                <span class="file-name">File: <strong>None</strong></span>
                <div class="drop-zone">
                    <img src="../../assets/img/upload.png" alt="Drag and drop icon">
                    Click or drop it here (drag & drop)
                </div>
                <input type="file" id="excelFile" accept=".xls,.xlsx">
            </div>

            <table id="rate" class="display col-5">
                <thead>
                <tr>
                    <th>Currency</th>
                    <th>Fx Rate</th>
                </tr>
                </thead>
            </table>
        </div>

        <div class="bank-accounts">
            <div>list of bank accounts</div>

            <table id="accounts" class="display col-12">
                <thead>
                <tr>
                    <th>Banks</th>
                    <th>Currency</th>
                    <th>Starting Balance</th>
                    <th>Edit Balance</th>
                    <th>End Balance (CHF)</th>
                </tr>
                </thead>
            </table>
        </div>

    </main>

</div>


<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>

<script src="../../assets/Editor-2.4.2/js/dataTables.editor.min.js"></script>

<script src="../../assets/js/script.js"></script>
</body>
</html>
