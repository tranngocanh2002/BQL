<?php
use yii\helpers\Html;

?>

<script>
    function changeLanguage() {
        var language = document.getElementById("change_language").value;
        if (language == 1) {
            document.cookie = 'language=en; path=/'
        } else {
            document.cookie = 'language=vi; path=/'
        }

        location.reload();
    }

</script>
<div id="menu2" class="tab-pane fade <?= $tab == 'language' ? 'active in' : '' ?>">
    <div class="col-md-12">
        <h4 style="margin-bottom: 24px;">
            <strong><?= Yii::t('backend', 'Ngôn ngữ') ?></strong>
        </h4>
        <p style="margin-bottom: 12px">
            <?= Yii::t('backend', 'Chọn ngôn ngữ hiển thị hệ thống') ?>
        </p>
        <div class="col-md-4" style="padding-left: 0; margin-bottom: 12px">
            <select id="change_language" class="form-control" name="change_language" aria-invalid="false">
                <option value="1" <?php if (isset($_COOKIE['language']) && $_COOKIE['language'] == 'en')
                    echo 'selected'; ?>>
                    <?= Yii::t('backend', 'English') ?></option>
                <option value="2" <?php if (isset($_COOKIE['language']) && $_COOKIE['language'] == 'vi')
                    echo 'selected'; ?>>
                    <?= Yii::t('backend', 'Vietnamese') ?></option>
            </select>
        </div>
        <div class="col-md-12" style="padding-left: 0;">
            <small>(*)
                <?= Yii::t('backend', 'Sau khi nhấn cập cập, hệ thống sẽ tự động tải lại trang và hiện thị ngôn ngữ đã chọn') ?>
            </small>
        </div>
        <div class="col-md-12" style="padding-left: 0; margin-top: 20px">
            <?= Html::submitButton(Yii::t('backend', 'Cập nhật'), ['class' => 'btn btn-primary', 'onClick' => 'changeLanguage()']) ?>
        </div>
    </div>
</div>