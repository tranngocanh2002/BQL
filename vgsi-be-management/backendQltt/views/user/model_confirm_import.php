<div class="modal fade" id="modal-confirm-import">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span></button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <h4 class="modal-title">
                    <?= Yii::t('backendQltt', 'Bạn có chắc chắn muốn tải lên danh sách người dùng không?') ?>
                </h4>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-default2 pull-left" data-dismiss="modal">
                        <?= Yii::t('backendQltt', 'Cancel') ?>
                    </button>
                    <button type="button" class="btn btn-primary" id="btn-confirm-import">
                        <?= Yii::t('backendQltt', 'Yes') ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .btn.btn-default2 {
        background-color: #f4f4f4 !important;
        border-color: #ddd;
    }

    /* .btn:active,
    .btn.active {

        -webkit-box-shadow: none;
        box-shadow: none;
    }

    .btn:active {
        -webkit-box-shadow: none;
    } */
</style>