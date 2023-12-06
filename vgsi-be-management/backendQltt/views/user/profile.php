<?php

use common\models\User;
use common\models\UserRole;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\detail\DetailView;
use kartik\datecontrol\DateControl;


/**
 * @var yii\web\View $this
 * @var common\models\User $model
 */
$this->title = Yii::t('backend', 'Tổng quan');
$avatarPreview = '/assets/191b04b/img/avatar5.png';

if ($model->avatar) {
    $avatarPreview = $model->avatar;
}

$tab = Yii::$app->request->get('tab');

if (is_null($tab) || $tab == "")
    $tab = 'info';

?>


<style>
    [hidden] {
        display: none !important;
    }


    .div-center {
        display: flex;
        justify-content: center;
        /* Horizontally center the content */
        align-items: center;
        /* Vertically center the content */
    }

    label.control-label.has-star.col-md-2 {
        text-align: left;
    }



    .box-body {
        padding-left: 24px;
        padding-top: 24px;
    }

    .nav.nav-pills.nav-stacked {
        height: 450px !important;
        border-right: 1px solid #eee;
    }

    .nav-stacked>li>a {
        border-left: 0px !important;
        background-color: #fff !important;
        border-right: 3px solid transparent !important;
    }

    .nav.nav-pills.nav-stacked>li {
        border-bottom: 0px solid #f4f4f4 !important;
    }

    .nav-stacked>li.active>a {
        border-left-color: transparent !important;
        border-right-color: #016343 !important;
        background-color: #c5dbd2 !important;
        color: #016343 !important;
    }

    .nav-pills>li.active>a:focus {
        background-color: #c5dbd2 !important;
        color: #016343 !important;
    }

    label.control-label {
        text-align: left !important;
    }

    img.imagePreview {
        object-fit: contain;
        width: 400px;
        height: 400px;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #fafafa;
        border: 1px dashed #d9d9d9;
        border-radius: 4px;
        margin-right: 8px;
        margin-bottom: 8px;
        text-align: center;
        cursor: pointer;
    }

    .upload {
        position: absolute;
        top: 0;
        width: 400px;
        height: 400px;
        display: block !important;
        opacity: 0;
        cursor: pointer;
    }

    input.hasDatepicker {
        border-radius: 4px !important;
    }

    span.input-group-addon {
        border-top-right-radius: 4px !important;
        border-bottom-right-radius: 4px !important;
    }
</style>

<script>
    // Function to handle image preview
    function handleImagePreview() {
        const imageUpload = document.getElementById('imageUpload');
        const imagePreview = document.getElementById('imagePreview');

        const file = document.getElementById("imageUpload").files[0];

        if (file.size > 10 * 1024 * 1024) {
            alert('<?= Yii::t('backendQltt', 'Dung lượng ảnh tối đa 10MB') ?>')
        } else if (file) {
            const reader = new FileReader();

            reader.addEventListener('load', function () {
                imagePreview.src = reader.result;
            });

            reader.readAsDataURL(file);

            uploadAvatar(file)
        }
    }

    function uploadAvatar(file) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '/upload/tmp', true);

        var formData = new FormData();
        formData.append('UploadForm[files][]', file);
        formData.append('fileId', file.name);

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    var elem = document.getElementById('avatar');
                    elem.value = '/' + JSON.parse(xhr.response).full_path;
                } else {
                    // Error uploading file
                    console.error('Error uploading file:', xhr.status);
                }
            }
        };

        // Set the CSRF token as a header
        xhr.setRequestHeader('X-Csrf-Token', "<?= Yii::$app->request->getCsrfToken() ?>");

        xhr.send(formData);
    }

</script>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <!-- /.box-header -->
            <div class="box-body">
                <div class="row">
                    <div class="col-md-3 col-lg-2">
                        <ul class="nav nav-pills nav-stacked">
                            <li class="<?= $tab == 'info' ? 'active' : '' ?>"><a data-toggle="tab" href="#home">
                                    <?= Yii::t('backend', 'Thông tin') ?>
                                </a></li>
                            <li class="<?= $tab == 'password' ? 'active' : '' ?>"><a data-toggle="tab" href="#menu1">
                                    <?= Yii::t('backend', 'Mật khẩu') ?>
                                </a></li>
                            <li class="<?= $tab == 'language' ? 'active' : '' ?>"><a data-toggle="tab" href="#menu2">
                                    <?= Yii::t('backend', 'Ngôn ngữ') ?>
                                </a></li>
                        </ul>

                    </div>
                    <div class="col-md-9 col-lg-10">
                        <div class="tab-content">
                            <div id="home" class="tab-pane fade in <?= $tab == 'info' ? 'active in' : '' ?>">
                                <div class="col-md-5">
                                    <div>
                                        <h4 style="margin-bottom: 24px;">
                                            <strong>
                                                <?= Yii::t('backend', 'Thông tin') ?>
                                            </strong>
                                        </h4>
                                        <?= $this->render('profile/about', [
                                            'model' => $model,
                                            'tab' => $tab,
                                        ]) ?>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <div class="form-group div-center">
                                        <img id="imagePreview" src="<?= $avatarPreview ?>" alt="Preview">
                                    </div>
                                    <div class="form-group div-center">
                                        <input type="file" hidden="hidden" id="imageUpload" name="image"
                                            accept="image/*" onchange="handleImagePreview();">
                                        <label for="imageUpload" class="btn btn-primary">
                                            <?= Yii::t('backend', 'Thay đổi ảnh đại diện') ?>
                                        </label>
                                    </div>
                                </div>
                                <!-- <div class="col-md-7">
                                    <div class="form-group div-center">
                                        <img id="imagePreview" class="imagePreview" src="<?= $avatarPreview ?>" alt="">
                                        <div style="position: absolute; top: 190px">
                                            <label for="imageUpload"
                                                style="display: flex; flex-direction: column; align-items: center;">
                                                <img class="icon" src="/images/plus.png" alt="" width="22">
                                                <?php echo $avatarPreview ?>
                                            </label>
                                        </div>
                                        <input class="upload" type="file" hidden="hidden" id="imageUpload" name="image"
                                            accept="image/*" onchange="handleImagePreview();">
                                    </div>
                                </div> -->
                            </div>
                            <?= $this->render('profile/password', [
                                'model' => $modelChangePassword,
                                'tab' => $tab,
                            ]) ?>

                            <?= $this->render('profile/language', [
                                'model' => $model,
                                'tab' => $tab,
                            ]) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<style>
    .box-body {
        padding-left: 24px;
        padding-top: 24px;
    }

    .nav.nav-pills.nav-stacked {
        /* height: 450px !important; */
        border-right: 1px solid #eee;
    }

    .nav-stacked>li>a {
        border-left: 0px !important;
        background-color: #fff !important;
        border-right: 3px solid transparent !important;
    }

    .nav.nav-pills.nav-stacked>li {
        border-bottom: 0px solid #f4f4f4 !important;
    }

    .nav-stacked>li.active>a {
        border-left-color: transparent !important;
        border-right-color: #016343 !important;
        background-color: #c5dbd2 !important;
        color: #016343 !important;
    }

    .nav-pills>li.active>a:focus {
        background-color: #c5dbd2 !important;
        color: #016343 !important;
    }

    /* .form-control {
        border-radius: 4px !important;
    }

    .form-control[disabled],
    fieldset[disabled] .form-control {
        cursor: not-allowed;
        color: rgba(0, 0, 0, 0.25) !important;
    } */

    label.control-label {
        text-align: left !important;
    }

    img#imagePreview {
        object-fit: contain;
        width: 385px;
        height: 385px;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #fafafa;
        border: 1px dashed #d9d9d9;
        border-radius: 4px;
        margin-bottom: 8px;
        text-align: center;
        /* cursor: pointer; */
    }

    .upload {
        position: absolute;
        top: 0;
        width: 400px;
        height: 400px;
        display: block !important;
        opacity: 0;
        cursor: pointer;
    }

    input.hasDatepicker {
        border-radius: 4px !important;
    }

    /* #chang-password .form-control {
        border-
    } */

    span.input-group-addon {
        border-top-right-radius: 4px !important;
        border-bottom-right-radius: 4px !important;
    }
</style>