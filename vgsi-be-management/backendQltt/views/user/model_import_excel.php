<div class="modal fade" id="modal-import-excel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><?= Yii::t('backend', 'Import dữ liệu') ?></h4>
            </div>
            <div class="modal-body">
                <div class="border-drop-file" id="border-drop-file">
                    <input type="file" style="display: none;" name="file" id="file" accept=".xls, .xlsx">
                    <label for="file" class="select-file">
                        <div class="plus alt"></div>
                    </label>

                </div>
                <div class="text-select-file text-center">
                    <span id="text-import"><?= Yii::t('backend', 'Chọn file tải lên') ?></span>
                    <div id="loading" class="d-none">
                        <div class="loadersmall"></div>
                        <span><?= Yii::t('backendQltt', 'Đang import file...') ?></span>
                    </div>
                </div>
                
            </div>
            <div class="modal-footer">

            </div>
        </div>
    </div>
</div>

<style>
    .border-drop-file {
        border: 2px dashed #000000;
        border-radius: 20px;
        box-sizing: border-box;
        width: 100px;
        min-height: 100px;
        margin: 0 auto;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        max-height: 100px;
        overflow: hidden;

    }

    .text-select-file {
        margin: 20px;
    }

    .plus {
        --b: 4px; /* the thickness */
        width: 50px; /* the size */
        aspect-ratio: 1;
        border: 10px solid #000; /* the outer space */
        background:
            conic-gradient(from 90deg at var(--b) var(--b),#000 90deg,#fff 0) 
            calc(100% + var(--b)/2) calc(100% + var(--b)/2)/
            calc(50%  + var(--b))   calc(50%  + var(--b));
        /* display: inline-block; */
    }

    .alt {
        border: none;
        margin: 10px;
        background:
        conic-gradient(from 90deg at var(--b) var(--b),#fff 90deg,#000 0) 
        calc(100% + var(--b)/2) calc(100% + var(--b)/2)/
        calc(50%  + var(--b))   calc(50%  + var(--b));
    }

    label.select-file {
        cursor: pointer;
    }

    .disable {
        pointer-events: none;
    }

    .loadersmall {
        border: 5px solid #f3f3f3;
        -webkit-animation: spin 1s linear infinite;
        animation: spin 1s linear infinite;
        border-top: 5px solid #555;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        margin: 0 auto;
        margin-bottom: 20px;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .d-none {
        display: none;
    }
</style>