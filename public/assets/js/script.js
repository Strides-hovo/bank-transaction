function initExcelImporter() {
    let $drop = $('.upload-block .drop-zone');
    let $input = $('#excelFile');
    let $fileName = $('.upload-block .file-name > strong');
    let uploadUrl = '/import';

    // click on zone opening upload dialog
    $drop.on('click', function () {
        $input.click();
    });

    // check file in dialog
    $input.on('change', function () {
        let file = this.files[0];
        if (!file) return;
        $fileName.text(file.name);
        upload(file);
    });

    // drag & drop
    $drop.on('dragenter dragover', function (e) {
        e.preventDefault();
        $drop.addClass('dragover');
    }).on('dragleave dragend drop', function (e) {
        e.preventDefault();
        $drop.removeClass('dragover');
    });

    // process drop
    $drop.on('drop', function (e) {
        e.preventDefault();
        let dt = e.originalEvent.dataTransfer;
        if (!dt.files.length) return;
        let file = dt.files[0];
        if (!/\.(xlsx?|xls)$/i.test(file.name)) {
            return alert('invalid format, need .xls or .xlsx');
        }
        $input[0].files = dt.files;
        $fileName.text(file.name);
        upload(file);
    });

    // loading function
    function upload(file) {
        let fd = new FormData();
        fd.append('excel', file);
        console.log(file.name)
        $.ajax({
            url: uploadUrl,
            type: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            dataType: 'json'
        }).done(function (resp) {
            if (resp.status === 'success') {
                // redraw currency exchange table
                loadCurrencyExchangeTable()
                // redraw accounts table
                bankAccountsTable()
            } else {
                alert('Server error: ' + (resp.message || ''));
            }
        }).fail(function (_, status) {
            alert('Server error: ' + status);
        });
    }
}


function loadCurrencyExchangeTable() {
    const importUrl = '/rates'
    const data = {
        ajax: {
            url: importUrl,
            dataSrc: '',
        },
        columns: [
            {data: 'Currency'},
            {data: 'Fx Rate'},
        ],
        searching: false,
        paging: false,
        info:     false,

    }
    if ($.fn.DataTable.isDataTable('#rate')) {
        $('#rate').DataTable().ajax.url(importUrl).load()
    }
    else{
        $('#rate').DataTable(data)
    }
}


function bankAccountsTable(){

    const importUrl = '/accounts'
    let table
    const data = {
        ajax: {
            url: importUrl,
            dataSrc: '',
        },
        columns: [
            { data: 'bank', title: 'Banks' },
            { data: 'currency', title: 'Currency' },
            { data: 'start_balance', title: 'Starting Balance' },
            { data: 'end_balance', title: 'End Balance'},
            { data: 'end_balance_chf', title: 'End Balance (CHF)' }
        ],
        searching: false,
        paging: false,
        info:     false,

    };
    if ($.fn.DataTable.isDataTable('#accounts')) {
        table = $('#accounts').DataTable().ajax.url(importUrl).load()
    }
    else{
        table = $('#accounts').DataTable(data)
    }


    table.on('click', 'tbody td', function () {

        const cell = table.cell(this);
        const cellIndex = Number(cell.index().column)
        if (cellIndex !== 0 && cellIndex !== 2) return

        const originalValue = cell.data();
        const placeholder = cellIndex === 0 ? 'Enter new account name:' : 'Enter new starting balance:'
        const newValue = prompt(placeholder, originalValue);
        if (newValue === null) return;

        if (newValue !== originalValue){
            const rowData = table.row(cell.index().row).data();

            $.ajax({
                url: '/balance/update',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    id: rowData.id,
                    account: cellIndex === 0 ? newValue : rowData.bank,
                    start_balance: cellIndex === 2 ? newValue : rowData.start_balance
                }),
                success: function (d,g) {
                    console.log(d,g)
                    cell.data(newValue).draw(false);
                    table.ajax.reload(null, false); // reload end_balance and CHF
                },
                error: function (err, msg, x) {
                    console.log(err, msg, x)
                    alert('Failed to update balance');
                },
                always: (a,b) => {
                    console.log(a,b)
                }
            });
        }

    });

}



function transactions() {

    const columns = [
        { data: "account" },
        { data: "number" },
        { data: "amount" },
        { data: "currency"},
        { data: "date"},
    ]

    const fields = [
        { label: "Transaction No", name: "number" },
        { label: "Amount", name: "amount" },
        { label: "Date", name: "date", type: "datetime", format: 'YYYY-MM-DD'},
        {label: '', name: "id"}
    ]

    const editor = new DataTable.Editor({
        ajax: {
            url: '/transactions/update',
            type: 'POST'
        },
        table: '#transactions',
        fields,
        idSrc:  'id',
    });

    const table = $('#transactions').DataTable({
        ajax: {
            url: '/transactions',
            dataSrc: ''
        },
        columns ,
        dom: 'lfrtip',
        buttons: [
            'excel', 'pdf'
        ],
        searching: false,
        pageLength: 10,
        lengthMenu: [ [10, 25, 50, -1], [10, 25, 50, 'All'] ],

    });

    $('#transactions').on('click', 'tbody td', function (e) {

        const index = $(this).index()
        const keys = columns.flatMap(v => v.data)
        const key = keys[index]
        const editing = fields.flatMap(v => v.name)
        if (editing.includes(key)){
            editor.inline(this, key);

        }
    });

    editor.on('postSubmit', function (a,b) {
        if (b.status === 'success'){
            table.ajax.reload(null, false);
        }
        else{
            editor.error(b.message)
        }
    });


    $('#transactions').on('click', '.btn-delete', function () {
    const row = table.row($(this).parents('tr'));
    const data = row.data();
    if (confirm(`Удалить транзакцию ${data.number}?`)) {
        $.ajax({
            url: '/transactions/delete',
            method: 'POST',
            data: { id: data.id },
            success: () => row.remove().draw(false)
        });
    }
});
}


async function chart(){

    const serverData = {
        categories: [],
        series: []
    }

     await fetch('/transactions/chart')
         .then(r => r.json())
         .then(response => {
             serverData.series = response.series
             serverData.categories = response.categories
         })

    // Data retrieved https://en.wikipedia.org/wiki/List_of_cities_by_average_temperature
    Highcharts.chart('container', {
        chart: {
            type: 'line'
        },
        title: null,

        xAxis: {
            categories: serverData.categories
        },
        yAxis: {
            title: null
        },
        plotOptions: {
            line: {
                dataLabels: {
                    enabled: true
                },
                enableMouseTracking: false
            }
        },
        series: serverData.series
    });

}



$(chart)
$(initExcelImporter);
$(loadCurrencyExchangeTable)
$(bankAccountsTable)
$(transactions)



