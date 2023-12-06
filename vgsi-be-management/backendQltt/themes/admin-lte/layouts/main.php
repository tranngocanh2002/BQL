<?php

use common\models\UserRole;
use kartik\dialog\Dialog;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */

$username = (Yii::$app->user->isGuest) ? 'No Name' : Yii::$app->user->identity->full_name;
$userRoleId = (Yii::$app->user->isGuest) ? null : Yii::$app->user->identity->role_id;
$js = '';

$js .= <<<JS
    $('[data-toggle="tooltip"]').tooltip();
    $('[data-toggle="popover"]').popover();
JS;

// Register tooltip/popover initialization javascript
$this->registerJs($js);

$permissionName = '';
if ($userRoleId) {
    $userRole = UserRole::find()->where(['id' => $userRoleId])->one();
    $permissionName = $userRole->name;
}

if (in_array(Yii::$app->controller->action->id, ['login', 'request-password-reset', 'reset-password-email', 'verify-otp', 'reset-password'])) {
    /**
     * Do not use this code in your template. Remove it. 
     * Instead, use the code  $this->layout = '//main-login'; in your controller.
     */
    echo $this->render(
        'main-login',
        ['content' => $content]
    );
} else {

    if (class_exists('backendQltt\assets\AppAsset')) {
        backend\assets\AppAsset::register($this);
    } else {
        app\assets\AppAsset::register($this);
    }

    dmstr\web\AdminLteAsset::register($this);

    $directoryAsset = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');
    ?>
    <?php $this->beginPage();
    Dialog::widget(); ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">

    <head>
        <meta charset="<?= Yii::$app->charset ?>" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title>
            <?= Html::encode($this->title) ?>
        </title>
        <?php $this->head() ?>
    </head>
    <style>
        @import url('https://fonts.googleapis.com/css?family=Roboto');

        :root {
            --primary: #016343;
            --primary-bg: #015543;
            --secondary: #F8CD4A;
            --danger: #f15a29;
        }

        .bg-primary {
            background-color: var(--primary) !important
        }

        .bg-warning {
            background-color: var(--secondary) !important
        }

        .bg-danger {
            background-color: var(--danger) !important
        }

        .my-bg {
            background-color: var(--primary) !important
        }

        .my-bg:focus,
        .my-bg:hover {
            background-color: var(--primary-bg) !important;
        }

        .btn.btn-primary {
            background-color: var(--primary);
            border-color: transparent;
        }

        .btn.btn-primary:focus,
        .btn.btn-primary:hover {
            background-color: var(--primary-bg);
            border-color: transparent;
        }

        .skin-green-light .main-header .navbar {
            background-color: var(--primary-bg);
        }

        .skin-green-light .main-header {
            background-color: var(--primary-bg);
        }

        .skin-green-light .main-header .logo {
            background-color: var(--primary-bg);
            min-height: 73px;
        }

        .skin-green-light .main-header .logo:hover {
            background-color: var(--primary-bg);
        }

        .skin-green-light .main-header .navbar .sidebar-toggle:hover {
            background-color: azure;
        }

        .skin-green-light .sidebar-menu>li:focus>a,
        .skin-green-light .sidebar-menu>li.active>a {
            background-color: var(--primary-bg);
            color: var(--secondary);
            border-color: var(--secondary);
        }

        .skin-green-light .sidebar-menu>li>a {
            color: #ffffffa6;
            font-size: 16px !important;
            line-height: 20px;
            font-weight: 100 !important;
        }

        .skin-green-light .sidebar-menu>li:hover>a,
        .skin-green-light .sidebar-menu {
            color: #ffffffa6;
            background-color: var(--primary);
            font-size: 16px;
            font-weight: 200
        }

        .skin-green-light .sidebar-menu>li>.treeview-menu {
            background-color: var(--primary);


        }

        .skin-green-light .sidebar-menu .treeview-menu>li>a:hover {
            color: white;
            opacity: 0.85;

        }

        .skin-green-light .sidebar-menu>li:hover>a {
            color: white;
            opacity: 0.85;

        }

        .skin-green-light .sidebar-menu .treeview-menu>li.active>a {
            background-color: var(--primary-bg);
            color: var(--secondary);
            border-color: var(--secondary);
            border-left-style: solid;
            padding-left: 36px;
        }

        .input-group-addon {
            color: rgba(0, 0, 0, 0.45);
        }

        .has-success.highlight-addon .input-group-addon {
            color: #3c763d;
            background-color: white;
            border-color: #d2d6de;
        }


        .has-error.highlight-addon .input-group-addon {
            color: rgba(0, 0, 0, 0.45);
            background-color: white;
            border-color: #a94442;
        }

        .form-group.has-error label {
            color: black;
        }

        .form-group.has-success .form-control,
        .form-group.has-success .input-group-addon {
            border-color: #d2d6de;
        }

        .form-control:focus+.input-group-addon {
            border-color: var(--primary);
            border-width: 2px;

        }

        .input-group .form-control:focus {

            border-color: var(--primary);
            border-width: 2px;

        }

        .form-group.has-success label {
            color: black;
        }

        .modal-dialog {
            min-width: 400px;
            width: fit-content !important;
        }

        .modal-footer {
            text-align: right;
            border-top: 0px solid #e5e5e5;

        }

        .modal {

            padding-top: 10vh;
        }

        .bootstrap-dialog-footer-buttons .btn.btn-default .glyphicon.glyphicon-ban-circle,
        .glyphicon.glyphicon-ok {
            display: none;
        }

        .modal-content {
            border-radius: 4px;

        }

        .bootstrap-dialog.type-warning .modal-header {

            display: none;
        }

        .bootstrap-dialog .bootstrap-dialog-message {
            font-size: 14px;
            font-weight: 400;
            padding: 8px;
            text-align: center;
        }

        .bootstrap-dialog-body {
            margin-top: 12px;
        }

        .modal-header {
            display: none;
        }

        .modal-title {
            padding: 8px;
            text-align: left;
            font-size: 14px;
            margin-top: 12px;
        }

        i.glyphicon {
            margin-right: 0px !important;
        }

        input[type="checkbox"] {
            accent-color: var(--primary) !important;
            border-color: var(--primary) !important;
            width: 16px;
            height: 16px;
            margin-top: 2px;
        }

        input[type="checkbox"]:checked::before {
            background-color: var(--primary) !important;
            border-color: var(--primary) !important;
        }

        .form-control {
            border-radius: 4px;
        }

        .form-control[disabled],
        fieldset[disabled] .form-control {
            cursor: not-allowed;
            color: rgba(0, 0, 0, 0.45);
        }

        .well-small {
            border: none !important;
        }

        .bootstrap-dialog .bootstrap-dialog-message {
            font-weight: 600;
            text-align: left;
            padding-left: 16px;
        }

        .treeview-menu {

            padding-left: 0px;
        }

        body {
            font-family: "Segoe UI", "SegoeuiPc", "San Francisco", "Helvetica Neue", "Helvetica", "Lucida Grande", "Roboto", "Ubuntu", "Tahoma", Microsoft Sans Serif, Tahoma, Arial, sans-serif !important;
            font-weight: 400;
            font-size: 14px;
        }

        .skin-green-light .sidebar-menu>li {
            padding-bottom: 4px;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        .h1,
        .h2,
        .h3,
        .h4,
        .h5,
        .h6 {
            font-family: "Segoe UI", "SegoeuiPc", "San Francisco", "Helvetica Neue", "Helvetica", "Lucida Grande", "Roboto", "Ubuntu", "Tahoma", Microsoft Sans Serif, Tahoma, Arial, sans-serif !important;
        }

        .sidebar-menu li>a>.pull-right-container>.fa-angle-left {
            transform: rotate(-90deg);
        }

        .select2-container--krajee .select2-results__option--highlighted[aria-selected] {
            background-color: #c5dbd2;
            color: #fff;
            opacity: 1 !important;
        }

        .select2-container--krajee.select2-container--open .select2-selection,
        .select2-container--krajee .select2-selection:focus {
            transition: none;
        }

        .select2-container--krajee .select2-dropdown {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            border-radius: 4px;
            border-color: rgba(0, 0, 0, 0.15);
            overflow-x: hidden;
            margin-top: 4px;
            padding-top: 0px;
        }

        .select2-container--krajee.select2-container--open.select2-container--below .select2-selection {
            border-bottom-right-radius: 4px;
            border-bottom-left-radius: 4px;
            border-bottom-color: var(--primary-bg);
            border-width: 2px;
        }

        .content-header>h1 {

            font-size: 20px;
        }

        .select2-container--krajee .select2-results>.select2-results__options {

            scrollbar-width: none;
            border-radius: 4px;
        }

        .select2-search--dropdown {
            display: none;
        }

        .select2-container--krajee .select2-results__option[aria-selected] {
            color: rgba(0, 0, 0, 0.65);
            opacity: 0.75;
            font-weight: normal;
            font-size: 14px;
            transition: ease-out(0.3s);
        }

        .has-success .select2-container--open .select2-selection,
        .has-success .select2-container--krajee.select2-container--focus .select2-selection {
            -webkit-box-shadow: none;
            box-shadow: none;
            border-color: none;
        }


        .has-success .checkbox {
            color: var(--primary) !important;
        }

        .form-control+.input-group-addon:not(:first-child) {

            border-top-right-radius: 4px;
            border-bottom-right-radius: 4px;

        }

        .form-group.has-error .help-block {

            font-weight: normal;
        }

        .skin-green-light.sidebar-mini.sidebar-collapse .sidebar-menu>li>.treeview-menu {
            border-radius: 4px;
            border-top-left-radius: 0px;
            border-top-right-radius: 0px;
            padding: 0px;
            margin: 0px;
            border-left-style: none !important;
            border-color: red !important;
            border-radius: 4px !important;
        }

        @media (min-width: 768px) {

            .sidebar-mini.sidebar-collapse .main-sidebar .user-panel>.info,
            .sidebar-mini.sidebar-collapse .sidebar-form,
            .sidebar-mini.sidebar-collapse .sidebar-menu>li>a>span,
            .sidebar-mini.sidebar-collapse .sidebar-menu>li>.treeview-menu,
            .sidebar-mini.sidebar-collapse .sidebar-menu>li>a>.pull-right,
            .sidebar-mini.sidebar-collapse .sidebar-menu>li>a>span>.pull-right,
            .sidebar-mini.sidebar-collapse .sidebar-menu li.header {
                -webkit-transform: none;
                display: none !important;
            }

        }

        .box-body {
            padding-left: 20px;
            padding-top: 24px;
            min-height: 80vh;
        }

        .box {
            border-top: 0px;
        }

        .pagination {
            display: flex;
            justify-content: center;
        }

        .pagination>li>a {
            background-color: white !important;
            color: #666 !important;
            border-color: #e5e5e5;
            border-radius: 4px;
            text-align: center;
            vertical-align: middle;
            margin-right: 8px;
            min-width: 32px;
            display: inline-block;
            height: 32px;
            outline: none;
        }

        .prev {
            margin-right: 8px;
            border-radius: 4px;
        }

        .pagination>li>a:hover,
        .pagination>li>span:hover,
        .pagination>li>a:focus,
        .pagination>li>span:focus {

            border-color: var(--primary-bg);
        }

        .pagination>.active>a,
        .pagination>.active>span,
        .pagination>.active>a:hover,
        .pagination>.active>span:hover,
        .pagination>.active>a:focus,
        .pagination>.active>span:focus {

            border-color: var(--primary-bg);
        }

        .pagination>li:first-child>a,
        .pagination>li:first-child>span {

            border-radius: 4px;
        }

        .select2-container .select2-selection--single .select2-selection__rendered {
            margin-top: 0px
        }

        .skin-green-light .sidebar-menu .treeview-menu>li>a {
            color: #ffffffa6;
            display: flex;
            align-items: center;
            padding-left: 40px;
            padding-top: 12px;
            padding-bottom: 12px;
        }

        .summary {
            position: relative;
            top: 48px;
            display: inline;
            left: 20%;

        }

        .pagination>.disabled>span,
        .pagination>.disabled>span:hover,
        .pagination>.disabled>span:focus,
        .pagination>.disabled>a,
        .pagination>.disabled>a:hover,
        .pagination>.disabled>a:focus {

            border-radius: 4px;
        }

        .input-group {
            width: 200px;
        }


        a {
            color: #000;
        }

        .footerCount {
            padding-top: 8px;
            padding-right: 12px;
            color: rgba(0, 0, 0, 0.65);

        }

        .icon {
            border: 1px #5544335A solid;
            border-radius: 32px;
            padding: 8px;
            filter: sepia(50%);
            -webkit-filter: sepia(50%);
        }

        .centerColumn {
            vertical-align: middle !important
        }

        .centerColumn2 {
            text-align: center !important;
            vertical-align: middle !important;
            height: 60px;
        }

        .table-bordered {
            border: 1px #e8e8e8 solid;
        }

        .table-bordered>thead>tr>th {
            height: 57px;
            background-color: #eff1f4;
            font-weight: bold;
            font-size: 16px;
            border: 1px #e8e8e8 solid;
        }

        .table-bordered>tbody>tr>td {
            height: 50px;
            vertical-align: middle !important;
            border: 1px #e8e8e8 solid;
            background-color: #fff;
        }

        .select2-container--krajee.select2-container--open .select2-selection,
        .select2-container--krajee .select2-selection:focus {
            -webkit-box-shadow: none;
            box-shadow: none;

            border-color: var(--primary-bg);
        }
    </style>

    <body class="hold-transition <?= \dmstr\helpers\AdminLteHelper::skinClass() ?> sidebar-mini">
        <?php $this->beginBody() ?>
        <div class="wrapper">

            <?= $this->render(
                'header.php',
                ['directoryAsset' => $directoryAsset, 'username' => $username, 'permissionName' => $permissionName]
            ) ?>

            <?= $this->render(
                'left.php',
                ['directoryAsset' => $directoryAsset, 'username' => $username]
            )
                ?>

            <?= $this->render(
                'content.php',
                ['content' => $content, 'directoryAsset' => $directoryAsset]
            ) ?>

        </div>

        <?php $this->endBody() ?>
    </body>

    </html>
    <?php $this->endPage() ?>
<?php } ?>