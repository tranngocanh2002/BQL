<?php



$this->title = Yii::t('backend', 'Danh sách mẫu tin tức');
$templates = $dataProvider->query->all();


$image = '/images/imageDefault.jpg';
if (empty($template->image)) {
    $image = '/images/imageDefault.jpg';
}
?>

<style>
    .plus {
        --b: 4px;
        /* the thickness */
        width: 70px;
        /* the size */
        aspect-ratio: 1;
        border-radius: 100%;
        border: 10px solid #000;
        /* the outer space */
        background:
            conic-gradient(from 90deg at var(--b) var(--b), #000 90deg, #fff 0) calc(100% + var(--b)/2) calc(100% + var(--b)/2)/ calc(50% + var(--b)) calc(50% + var(--b));
        /* display: inline-block; */
    }

    .alt {
        border: none;
        background:
            conic-gradient(from 90deg at var(--b) var(--b), #fff 90deg, #000 0) calc(100% + var(--b)/2) calc(100% + var(--b)/2)/ calc(50% + var(--b)) calc(50% + var(--b));
        margin: 105px;
    }

    .fa.fa-plus.fa-5x {
        color: grey;
        margin-bottom: 80px;
        font-size: 90px
    }

    .create-template {
        height: 300px;
        border: 1px solid #e8e8e8;
        /* width: 200px; */
        position: relative;
        padding: 0;
        border-radius: 4px;
        margin-top: 20px;
    }

    .btn-create-template {
        position: absolute;
        bottom: 0;
        width: 100%;
        text-align: center;
    }

    .btn-create-template a {
        width: 100%;
        border-radius: 0;
        border-bottom: none;
        display: inline-block;
        text-align: center;
        border-top: 1px solid #e8e8e8;
        padding: 6px 0;
        color: rgba(0, 0, 0, 0.45);
        background-color: #fafafa;
    }

    .template {
        height: 300px;
        border: 1px solid #e8e8e8;
        /* width: 20%; */
        position: relative;
        /* margin-right: 10px; */
        padding: 0;
        border-radius: 4px;
        margin-top: 20px;
        margin-bottom: 20px;
    }

    .template .preview {
        width: 100%;
        height: 196px;
        object-fit: cover;
        border-top-left-radius: 4px;
        border-top-right-radius: 4px;
    }

    .btn.btn-skeleton {
        position: absolute;
        top: 0;
        height: 244px;
        width: 100%;
        color: transparent;
    }

    /* .btn-default {
        background-color: #fafafa;
    }

    .btn-default:hover {
        border: 1px solid #e8e8e8 !important;
        background-color: transparent;
    } */

    .botton-edit-delete {
        display: flex;
        align-items: center;
        margin-top: 10px;
    }

    .botton-edit-delete a {
        width: 100%;
        border-radius: 0;
        border-bottom: none;
        display: inline-block;
        text-align: center;
        border-top: 1px solid #e8e8e8;
        padding: 6px 0;
        color: rgba(0, 0, 0, 0.45);
        background-color: #fafafa;
    }

    .botton-edit-delete a:hover {
        color: var(--primary);
    }

    .divider {
        border-left: 1px solid #e8e8e8;
        height: 18px;
    }

    .title.text-center h4 {
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        margin-left: 4px;
        margin-right: 4px;
        font-size: 16px;
        color: black;
        font-weight: semibold;
    }

    .title.text-center p {
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        margin-left: 4px;
        margin-right: 4px;
        font-size: 14px;
        color: rgba(0, 0, 0, 0.85);
    }

    .box-body {
        padding-right: 35px;
    }

    @media screen and (min-width: 768px) {
        .template-container {
            width: 50%;
        }
    }

    @media screen and (min-width: 992px) {
        .template-container {
            width: 33%;
        }
    }

    @media screen and (min-width: 1200px) {
        .template-container {
            width: 20%;
        }
    }
</style>

<script>
    const showModelDelete = (id) => {
        // $('#' + id).modal('toggle');
        // JS Code
        krajeeDialog.confirm("<?= Yii::t('backendQltt', 'Bạn có chắc chắn muốn xóa mẫu tin tức này?') ?>", function (
            result) {
            if (result) { // ok button was pressed
                deleteTemplate(id)
            } else { // dialog dialog was cancelled
                // execute your code for cancellation
            }
        });
    };

    const deleteTemplate = (id) => {
        window.location.href = '/announcement-template/delete?id=' + id
    };
</script>

<div class="box">
    <div class="box-body">
        <div class="row" style="margin-left: 0;">
            <?php if (count($templates) > 0) { ?>
                <?php foreach ($templates as $key => $template) { ?>
                    <div class="col-md-3 template-container">
                        <div class="template">
                            <img style="border-bottom: 1px solid #e8e8e8;"
                                src="<?= empty($template->image) ? '/images/imageDefault.jpg' : $template->image ?>" alt=""
                                class="preview">
                            <div class="title text-center">
                                <a href="/announcement-template/view?id=<?= $template->id ?>">
                                    <h4>
                                        <?= (isset($_COOKIE['language']) && $_COOKIE['language'] === 'vi') ? $template->name : $template->name_en ?>
                                    </h4>
                                </a>
                                <a href="/announcement-template/view?id=<?= $template->id ?>">
                                    <p>
                                        <?= Yii::t('backendQltt', 'Mẫu') . ' ', $key + 1 ?>
                                    </p>
                                </a>
                            </div>
                            <div class="botton-edit-delete">
                                <?php if ($this->context->checkPermission('announcement-template', 'update')) { ?>
                                    <a href="/announcement-template/update?id=<?= $template->id ?>" class="btnEdit"
                                        title="<?= Yii::t('backendQltt', 'Edit') ?>">
                                        <span class="glyphicon glyphicon-edit"></span>
                                    </a>
                                <?php } ?>
                                <div class="divider"></div>
                                <?php if ($this->context->checkPermission('announcement-template', 'delete')) { ?>
                                    <a href="javascript:void(0)" onclick="showModelDelete('<?= $template->id ?>')" class="btnDelete"
                                        title="<?= Yii::t('backendQltt', 'Delete') ?>">
                                        <span class="glyphicon glyphicon-trash"></span>
                                    </a>
                                <?php } ?>
                                <?= $this->render('model_confirm_delete', [
                                    'id' => $template->id,
                                ]) ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
            <?php if ($this->context->checkPermission('announcement-template', 'create')) { ?>
                <div class="col-md-3 template-container">
                    <div class="create-template">
                        <div class="btn-create-template">
                            <i class="fa fa-plus fa-5x" aria-hidden="true"></i>
                            <a class="btn" href="/announcement-template/create">
                                <?= Yii::t('backend', 'Create template') ?>
                            </a>
                        </div>
                        <a href="/announcement-template/create" class="btn btn-skeleton">
                            <?= Yii::t('backend', 'Create template') ?>
                        </a>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>