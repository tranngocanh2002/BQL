<?php
$fileData = explode('/', $file->file_name);
?>

<div class="file-item" id="file_<?= $key ?>">
    <div class="file-name">
        <span>
            <?= end($fileData) ?>
        </span>
    </div>
    <div class="delete-icon">
        <a class="delete" type="button" onclick="movefile(<?= $key ?>, '<?= $file->full_path ?>')">
            <i class="fa fa-fw fa-close"></i>
        </a>
    </div>
</div>