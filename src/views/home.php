<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">



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


        <div class="transactions col-12">
            <p>Transactions</p>
            <table id="transactions" class="display col-12">
                <thead>
                <tr>
                    <th>Account</th>
                    <th>Transaction No</th>
                    <th>Amount</th>
                    <th>Currency</th>
                    <th>Data</th>

                </tr>
                </thead>
            </table>
        </div>


    </main>

</div>


<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>

<!-- DataTables 1.13.6 -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<!-- Select -->
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.7.0/css/select.dataTables.min.css">
<script src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>


<!-- Editor 2.4.2 -->
<link rel="stylesheet" href="/assets/Editor-2.4.2/css/editor.dataTables.min.css">
<script src="/assets/Editor-2.4.2/js/dataTables.editor.min.js"></script>

<!-- Buttons -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

<!-- DateTime  -->
<link rel="stylesheet" href="https://cdn.datatables.net/datetime/1.5.1/css/dataTables.dateTime.min.css">
<script src="https://cdn.datatables.net/datetime/1.5.5/js/dataTables.dateTime.min.js"></script>

<script src="../../assets/js/script.js"></script>
</body>
</html>
