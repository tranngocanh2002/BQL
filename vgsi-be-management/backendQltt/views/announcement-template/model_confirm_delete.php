<div class="modal fade" id="delete-<?= $id ?>">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span></button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <h4 class="modal-title"><?= Yii::t('backendQltt', 'Bạn có chắc chắn muốn xóa mẫu tin tức này?') ?></h4>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?= Yii::t('backendQltt', 'Cancel') ?></button>
                    <button type="button" class="btn btn-primary" onclick="deleteTemplate('<?= $id ?>')"><?= Yii::t('backendQltt', 'Yes') ?></button>
                </div>
            </div>
        </div>
    </div>
</div>