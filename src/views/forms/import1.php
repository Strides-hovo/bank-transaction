<?php
$action = isset($action) ? $action : '/import';
?>
<form method="post" action="<?= $action ?>">
    <div class="upload-block">
        <button class="browse-btn">Browse files</button>
        <span class="file-name">File: none</span>
        <div class="drop-zone">Click or drop it here (drag & drop)</div>
        <input type="file" id="excelFile" accept=".xls,.xlsx">
    </div>
</form>