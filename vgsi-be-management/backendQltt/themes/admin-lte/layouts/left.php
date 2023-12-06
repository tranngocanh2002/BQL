<?php
/* @var $this \yii\web\View */

/* @var $directoryAsset string */
/* @var $username string */
$avatar = $directoryAsset . "/img/avatar5.png";

if (Yii::$app->user->identity->avatar) {
    $avatar = Yii::$app->user->identity->avatar;
}

?>
<aside class="main-sidebar" style="background-color: #016343;width:231px">

    <section class="sidebar">

        <!-- Sidebar user panel -->
        <div class="user-panel">
            <!-- <div class="pull-left image" style="color: #ffffffa6;">
                <img src="<?= $avatar ?>" class="img-circle" alt="User Image"
                    style="object-fit: cover; height: 45px; width: 45px" />
            </div>
            <div class="pull-left info" style="color:#ffffffa6;">
                <p>
                    <?= $username ?>
                </p>
                <a href="#" style="color: #ffffffa6;"><i class="fa fa-circle text-success"
                        style="background-color: #016343 ;"></i> Online</a>
            </div> -->
        </div>
        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu', 'data-widget' => 'tree'],
                'items' => $this->context->menuLeftItems,
            ]
        ) ?>

    </section>

</aside>
<style>
    .sidebar-mini.sidebar-collapse .main-sidebar {
        width: 101px !important;
        align-items: center !important;
        text-align: center;
    }

    .main-sidebar {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
    }

    .logo-wrapper {
        margin-left: 0px !important;
        transition: transform .3s ease-in-out, width .3s ease-in-out;
    }

    .treeview-menu>li {
        text-align: left !important;
    }

    .sidebar-mini:not(.sidebar-mini-expand-feature).sidebar-collapse .sidebar-menu>li:hover>a>span:not(.pull-right) {
        left: 105px;
        display: none !important;
    }

    .sidebar-mini.sidebar-collapse .content-wrapper {
        margin-left: 100px !important;
    }

    .sidebar-mini:not(.sidebar-mini-expand-feature).sidebar-collapse .sidebar-menu>li:hover>.treeview-menu {
        margin-left: 54px !important;
        margin-top: -40px !important;
        padding-top: 12px;
        padding-bottom: 12px;
    }

    .sidebar-mini:not(.sidebar-mini-expand-feature).sidebar-collapse .sidebar-menu>li:hover>.treeview-menu>li.active>a {
        border-left-style: none;
        border-top-left-radius: 4px;
        border-top-right-radius: 4px;
        background-color: transparent;
        color: white;
    }

    .sidebar-mini:not(.sidebar-mini-expand-feature).sidebar-collapse .sidebar-menu>li:hover>a {

        color: white;
    }

    .skin-green-light .sidebar-menu .treeview-menu>li>a:hover {
        color: white;


    }

    li.active:nth-child(5)>a:nth-child(1) {
        color: white;
        border-left-style: none;
        background-color: transparent;
    }

    .sidebar-mini:not(.sidebar-mini-expand-feature).sidebar-collapse .sidebar-menu>li>a {
        display: flex;
        justify-content: center;
        padding-left: 5px;
        align-items: center;
        vertical-align: middle;
        align-self: flex-end;
    }
</style>