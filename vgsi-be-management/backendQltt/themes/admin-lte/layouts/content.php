<?php
use dmstr\widgets\Alert;
use yii\bootstrap\Alert as BootstrapAlert;
use yii\widgets\Breadcrumbs;

?>
<div class="content-wrapper">
    <section class="content-header"
        style="background-color: white; margin-top:5px; padding-bottom: 12px; min-height: 79px;">
        <p id="breadcumbs"></p>
        <?php if (isset($this->blocks['content-header'])) { ?>
            <h1><strong>
                    <?= $this->blocks['content-header'] ?>
                </strong></h1>
        <?php } else { ?>
            <h1>
                <strong>
                    <?php
                    if ($this->title !== null) {
                        echo \yii\helpers\Html::encode($this->title);
                    } else {
                        echo \yii\helpers\Inflector::camel2words(
                            \yii\helpers\Inflector::id2camel($this->context->module->id)
                        );
                        echo ($this->context->module->id !== \Yii::$app->id) ? '<small>Module</small>' : '';
                    } ?>
                </strong>
            </h1>
        <?php }
        ?>

        <?=
            Breadcrumbs::widget(
                [
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]
            ) ?>
    </section>

    <section class="content">
        <?= Alert::widget() ?>
        <?php
        if (Yii::$app->session->hasFlash('message')) {
            echo BootstrapAlert::widget([
                'options' => [
                    'class' => 'alert-success',
                ],
                'body' => Yii::$app->session->getFlash('message'),
            ]);
        }
        ?>
        <?= $content ?>
    </section>
</div>

<!-- <footer class="main-footer">
    <div class="pull-right hidden-xs">
        <b><?= Yii::t('common', 'Version') ?></b> 1.2
    </div>
    <strong><?= Yii::t('common', 'Copyright') ?> &copy; <?= date('Y') ?> <a href="http://luci.vn">Luci</a>.</strong> <?= Yii::t('common', 'All rights reserved') ?>
</footer> -->
<script>
    function getCookie(cname) {
        let name = cname + "=";
        let decodedCookie = decodeURIComponent(document.cookie);
        let ca = decodedCookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }

    localStorage.setItem('url', window.location.href);
    if (localStorage.getItem('url').includes("building-cluster/view")) {
        if (getCookie('language') == "vi") {
            localStorage.setItem('breadcumbs', 'Dự án / Chi tiết');
        } else {
            localStorage.setItem('breadcumbs', 'Projects / Detail');
        }
    } else if (localStorage.getItem('url').includes("building-cluster/create")) {
        if (getCookie('language') == "vi") {
            localStorage.setItem('breadcumbs', 'Dự án / Thêm mới');
        } else {
            localStorage.setItem('breadcumbs', 'Projects / Create');
        }
    } else if (localStorage.getItem('url').includes("building-cluster/update")) {
        if (getCookie('language') == "vi") {
            localStorage.setItem('breadcumbs', 'Dự án / Chỉnh sửa');
        } else {
            localStorage.setItem('breadcumbs', 'Projects / Edit');
        }
    } else if (localStorage.getItem('url').includes("building-cluster")) {
        if (getCookie('language') == "vi") {
            localStorage.setItem('breadcumbs', 'Dự án');
        } else {
            localStorage.setItem('breadcumbs', 'Projects');
        }
    } else if (localStorage.getItem('url').includes("announcement-campaign/view")) {
        if (getCookie('language') == "vi") {
            localStorage.setItem('breadcumbs', 'Tin tức / Chi tiết');
        } else {
            localStorage.setItem('breadcumbs', 'News / Detail');
        }
    } else if (localStorage.getItem('url').includes("announcement-campaign/update")) {
        if (getCookie('language') == "vi") {
            localStorage.setItem('breadcumbs', 'Tin tức / Chỉnh sửa');
        } else {
            localStorage.setItem('breadcumbs', 'News / Edit');
        }
    } else if (localStorage.getItem('url').includes("announcement-campaign/create")) {
        if (getCookie('language') == "vi") {
            localStorage.setItem('breadcumbs', 'Tin tức / Tạo mới');
        } else {
            localStorage.setItem('breadcumbs', 'News / Create');
        }
    } else if (localStorage.getItem('url').includes("announcement-campaign")) {
        if (getCookie('language') == "vi") {
            localStorage.setItem('breadcumbs', 'Tin tức');
        } else {
            localStorage.setItem('breadcumbs', 'News');
        }
    } else if (localStorage.getItem('url').includes("user/create")) {
        if (getCookie('language') == "vi") {
            localStorage.setItem('breadcumbs', 'Người dùng / Thêm mới');
        } else {
            localStorage.setItem('breadcumbs', 'User / Create');
        }
    } else if (localStorage.getItem('url').includes("user/view")) {
        if (getCookie('language') == "vi") {
            localStorage.setItem('breadcumbs', 'Người dùng / Chi tiết');
        } else {
            localStorage.setItem('breadcumbs', 'User / Detail');
        }
    } else if (localStorage.getItem('url').includes("user/update")) {
        if (getCookie('language') == "vi") {
            localStorage.setItem('breadcumbs', 'Người dùng / Chỉnh sửa');
        } else {
            localStorage.setItem('breadcumbs', 'User / Edit');
        }
    } else if (localStorage.getItem('url').includes("apartment-map-resident-user")) {
        if (getCookie('language') == "vi") {
            localStorage.setItem('breadcumbs', 'App cư dân');
        } else {
            localStorage.setItem('breadcumbs', 'Resident app');
        }
    } else if (localStorage.getItem('url').includes("announcement-template/create")) {
        if (getCookie('language') == "vi") {
            localStorage.setItem('breadcumbs', 'Cấu hình / Mẫu tin tức / Tạo mới');
        } else {
            localStorage.setItem('breadcumbs', 'Configuration / News template / Create');
        }
    } else if (localStorage.getItem('url').includes("announcement-template/update")) {
        if (getCookie('language') == "vi") {
            localStorage.setItem('breadcumbs', 'Cấu hình / Mẫu tin tức / Chỉnh sửa');
        } else {
            localStorage.setItem('breadcumbs', 'Configuration / News template / Edit');
        }
    } else if (localStorage.getItem('url').includes("announcement-template")) {
        if (getCookie('language') == "vi") {
            localStorage.setItem('breadcumbs', 'Cấu hình / Mẫu tin tức');
        } else {
            localStorage.setItem('breadcumbs', 'Configuration / News template');
        }
    } else if (localStorage.getItem('url').includes("user-role/create")) {
        if (getCookie('language') == "vi") {
            localStorage.setItem('breadcumbs', 'Cấu hình / Nhóm quyền / Tạo mới');
        } else {
            localStorage.setItem('breadcumbs', 'Configuration / Group management / Create');
        }
    } else if (localStorage.getItem('url').includes("user-role/update")) {
        if (getCookie('language') == "vi") {
            localStorage.setItem('breadcumbs', 'Cấu hình / Nhóm quyền / Chỉnh sửa');
        } else {
            localStorage.setItem('breadcumbs', 'Configuration / Group management / Edit');
        }
    } else if (localStorage.getItem('url').includes("user-role")) {
        if (getCookie('language') == "vi") {
            localStorage.setItem('breadcumbs', 'Cấu hình / Nhóm quyền');
        } else {
            localStorage.setItem('breadcumbs', 'Configuration / Group management');
        }
    } else if (localStorage.getItem('url').includes('user/profile')) {
        if (getCookie('language') == "vi") {
            localStorage.setItem('breadcumbs', 'Tổng quan');
        } else {
            localStorage.setItem('breadcumbs', 'Overview');
        }
    } else if (localStorage.getItem('url').includes('logger-user')) {
        if (getCookie('language') == "vi") {
            localStorage.setItem('breadcumbs', 'Cấu hình / Lịch sử người dùng');
        } else {
            localStorage.setItem('breadcumbs', 'Configuration / User history');
        }
    } else if (localStorage.getItem('url').includes('user')) {
        if (getCookie('language') == "vi") {
            localStorage.setItem('breadcumbs', 'Người dùng');
        } else {
            localStorage.setItem('breadcumbs', 'User management');
        }
    } else {
        if (getCookie('language') == "vi") {
            localStorage.setItem('breadcumbs', 'Dự án');
        } else {
            localStorage.setItem('breadcumbs', 'Projects');
        }
    }
    document.getElementById('breadcumbs').innerHTML = localStorage.getItem('breadcumbs');
</script> #