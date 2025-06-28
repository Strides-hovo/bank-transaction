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
                loadCurrencyExchangeTable();
            } else {
                alert('Server error: ' + (resp.message || ''));
            }
        }).fail(function (_, status) {
            alert('Server error: ' + status);
        });
    }
}


function loadCurrencyExchangeTable() {
    const importUrl = '/transactions'
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

    const accountsData = [
        {
            bank: 'Revolut',
            currency: 'EUR',
            starting_balance: 0,
            end_balance_chf: 0
        },
        {
            bank: 'SwissBank',
            currency: 'CHF',
            starting_balance: 0,
            end_balance_chf: 0
        }
    ]
    const importUrl = '/accounts'
    const table = $('#accounts').DataTable({
        ajax: {
            url: importUrl,
            dataSrc: '',
        },
        columns: [
            { data: 'bank', title: 'Banks' },
            { data: 'currency', title: 'Currency' },
            { data: 'starting_balance', title: 'Starting Balance' },
            {
                data: 'starting_balance',
                title: 'Edit Balance',
                render: function (data, type, row, meta) {

                    return `<input type="number" class="edit-balance" data-index="${meta.row}" value="${data}">`;
                }
            },
            { data: 'end_balance_chf', title: 'End Balance (CHF)' }
        ],
        searching: false,
        paging: false,
        info:     false,

    });
    $('#accounts tbody').on('input', '.edit-balance', function () {
        const index = $(this).data('index');
        accountsData[index].starting_balance = parseFloat($(this).val()) || 0;

        // Здесь можно пересчитать end_balance_chf, если известен курс

        table.row(index).data(accountsData[index]).invalidate();
    });
}

$(initExcelImporter);
$(loadCurrencyExchangeTable)
$(bankAccountsTable)




