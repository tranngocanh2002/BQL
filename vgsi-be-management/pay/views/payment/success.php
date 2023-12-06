<?php
/* @var $this yii\web\View */

use yii\web\View;

/* @var $dataRes */
if(empty($dataRes)){
    die;
}
?>
<div class="container">
    <div class="jumbotron mt-3" style="text-align: center;">
        <h1>
            <?php
            if($dataRes['success']){
                echo $dataRes['message'];
            }else{
                echo 'Thanh toán không thành công!';
            }
            ?>
        </h1>
    </div>
</div>
