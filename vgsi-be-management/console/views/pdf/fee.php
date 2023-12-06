<?php
/* @var $this yii\web\View */
/* @var $serviceBill common\models\ServiceBill */
/* @var $serviceBillItems common\models\ServiceBillItem */

/* @var $apartment common\models\Apartment */

use common\helpers\CUtils;
use yii\helpers\Html;
use common\models\ServicePaymentFee;
use common\models\ServiceBuildingConfig;
/*
 * type : 0 - phí nước, 1 - phí dịch vụ, 2 - phí xe
 */
$start_current_month = strtotime(date('Y-m-01 00:00:00'));
$end_current_month = strtotime(date('Y-m-t 23:59:59', $start_current_month));
//phí dịch vụ của tháng hiện tại
$servicePaymentFee = ServicePaymentFee::find()
    ->where(['apartment_id' => $apartment->id, 'status' => ServicePaymentFee::STATUS_UNPAID, 'type' => ServicePaymentFee::TYPE_SERVICE_BUILDING_FEE])
    ->andWhere(['>=', 'fee_of_month', $start_current_month])
    ->andWhere(['<=', 'fee_of_month', $end_current_month])
    ->one();
$serviceBuildingConfig = null;
if(!empty($servicePaymentFee)){
    //lấy thông tin config phí dịch vụ
    $serviceBuildingConfig = ServiceBuildingConfig::findOne(['building_cluster_id' => $servicePaymentFee->building_cluster_id, 'service_map_management_id' => $servicePaymentFee->service_map_management_id]);
}
//phí dịch vụ nợ cũ -> trước tháng hiện tại
$total_servicePaymentFee = ServicePaymentFee::find()->select(["SUM(more_money_collecte) as more_money_collecte"])->where(['apartment_id' => $apartment->id, 'status' => ServicePaymentFee::STATUS_UNPAID, 'type' => ServicePaymentFee::TYPE_SERVICE_BUILDING_FEE])
    ->andWhere(['<', 'fee_of_month', $start_current_month])->one();

//phí nước tháng hiện tại
$servicePaymentFeeWater = ServicePaymentFee::find()
    ->where(['apartment_id' => $apartment->id, 'status' => ServicePaymentFee::STATUS_UNPAID, 'type' => ServicePaymentFee::TYPE_SERVICE_WATER_FEE])
    ->andWhere(['>=', 'fee_of_month', $start_current_month])
    ->andWhere(['<=', 'fee_of_month', $end_current_month])
    ->one();
//phí nước nợ cũ
$total_servicePaymentFeeWater = ServicePaymentFee::find()->select(["SUM(more_money_collecte) as more_money_collecte"])->where(['apartment_id' => $apartment->id, 'status' => ServicePaymentFee::STATUS_UNPAID, 'type' => ServicePaymentFee::TYPE_SERVICE_WATER_FEE])
    ->andWhere(['<', 'fee_of_month', $start_current_month])->one();

$total_price_all = 0;
?>
<style type="text/css">
    .lst-kix_list_2-6 > li:before {
        content: "\002022  "
    }

    .lst-kix_list_2-7 > li:before {
        content: "\002022  "
    }

    ul.lst-kix_list_1-0 {
        list-style-type: none
    }

    .lst-kix_list_2-4 > li:before {
        content: "\002022  "
    }

    .lst-kix_list_2-5 > li:before {
        content: "\002022  "
    }

    .lst-kix_list_2-8 > li:before {
        content: "\002022  "
    }

    ol.lst-kix_list_2-0 {
        list-style-type: none
    }

    ul.lst-kix_list_2-8 {
        list-style-type: none
    }

    ul.lst-kix_list_1-3 {
        list-style-type: none
    }

    ul.lst-kix_list_2-2 {
        list-style-type: none
    }

    .lst-kix_list_1-0 > li:before {
        content: "*  "
    }

    ul.lst-kix_list_1-4 {
        list-style-type: none
    }

    ul.lst-kix_list_2-3 {
        list-style-type: none
    }

    ul.lst-kix_list_1-1 {
        list-style-type: none
    }

    ul.lst-kix_list_1-2 {
        list-style-type: none
    }

    ul.lst-kix_list_2-1 {
        list-style-type: none
    }

    ul.lst-kix_list_1-7 {
        list-style-type: none
    }

    ul.lst-kix_list_2-6 {
        list-style-type: none
    }

    .lst-kix_list_1-1 > li:before {
        content: "\002022  "
    }

    .lst-kix_list_1-2 > li:before {
        content: "\002022  "
    }

    ul.lst-kix_list_1-8 {
        list-style-type: none
    }

    ol.lst-kix_list_2-0.start {
        counter-reset: lst-ctn-kix_list_2-0 0
    }

    ul.lst-kix_list_2-7 {
        list-style-type: none
    }

    ul.lst-kix_list_1-5 {
        list-style-type: none
    }

    ul.lst-kix_list_2-4 {
        list-style-type: none
    }

    ul.lst-kix_list_1-6 {
        list-style-type: none
    }

    ul.lst-kix_list_2-5 {
        list-style-type: none
    }

    .lst-kix_list_1-3 > li:before {
        content: "\002022  "
    }

    .lst-kix_list_1-4 > li:before {
        content: "\002022  "
    }

    .lst-kix_list_2-0 > li {
        counter-increment: lst-ctn-kix_list_2-0
    }

    .lst-kix_list_1-7 > li:before {
        content: "\002022  "
    }

    .lst-kix_list_1-5 > li:before {
        content: "\002022  "
    }

    .lst-kix_list_1-6 > li:before {
        content: "\002022  "
    }

    .lst-kix_list_2-0 > li:before {
        content: "" counter(lst-ctn-kix_list_2-0, upper-roman) ". "
    }

    .lst-kix_list_2-1 > li:before {
        content: "\002022  "
    }

    .lst-kix_list_1-8 > li:before {
        content: "\002022  "
    }

    .lst-kix_list_2-2 > li:before {
        content: "\002022  "
    }

    .lst-kix_list_2-3 > li:before {
        content: "\002022  "
    }

    ol {
        margin: 0;
        padding: 0
    }

    table td, table th {
        padding: 0
    }

    .c6 {
        border-right-style: solid;
        border-bottom-color: #000000;
        border-top-width: 1pt;
        border-right-width: 1pt;
        border-left-color: #000000;
        vertical-align: top;
        border-right-color: #000000;
        border-left-width: 1pt;
        border-top-style: solid;
        border-left-style: solid;
        border-bottom-width: 1pt;
        width: 74.8pt;
        border-top-color: #000000;
        border-bottom-style: solid
    }

    .c59 {
        border-right-style: solid;
        border-bottom-color: #000000;
        border-top-width: 1pt;
        border-right-width: 1pt;
        border-left-color: #000000;
        vertical-align: top;
        border-right-color: #000000;
        border-left-width: 1pt;
        border-top-style: solid;
        border-left-style: solid;
        border-bottom-width: 1pt;
        width: 279.5pt;
        border-top-color: #000000;
        border-bottom-style: solid
    }

    .c56 {
        border-right-style: solid;
        border-bottom-color: #000000;
        border-top-width: 1pt;
        border-right-width: 1pt;
        border-left-color: #000000;
        vertical-align: top;
        border-right-color: #000000;
        border-left-width: 1pt;
        border-top-style: solid;
        border-left-style: solid;
        border-bottom-width: 1pt;
        width: 204.6pt;
        border-top-color: #000000;
        border-bottom-style: solid
    }

    .c18 {
        border-right-style: solid;
        border-bottom-color: #000000;
        border-top-width: 1pt;
        border-right-width: 1pt;
        border-left-color: #000000;
        vertical-align: top;
        border-right-color: #000000;
        border-left-width: 1pt;
        border-top-style: solid;
        border-left-style: solid;
        border-bottom-width: 1pt;
        width: 181.6pt;
        border-top-color: #000000;
        border-bottom-style: solid
    }

    .c48 {
        border-right-style: solid;
        border-bottom-color: #000000;
        border-top-width: 1pt;
        border-right-width: 1pt;
        border-left-color: #000000;
        vertical-align: top;
        border-right-color: #000000;
        border-left-width: 1pt;
        border-top-style: solid;
        border-left-style: solid;
        border-bottom-width: 1pt;
        width: 88.2pt;
        border-top-color: #000000;
        border-bottom-style: solid
    }

    .c35 {
        border-right-style: solid;
        border-bottom-color: #000000;
        border-top-width: 1pt;
        border-right-width: 1pt;
        border-left-color: #000000;
        vertical-align: top;
        border-right-color: #000000;
        border-left-width: 1pt;
        border-top-style: solid;
        border-left-style: solid;
        border-bottom-width: 1pt;
        width: 94.8pt;
        border-top-color: #000000;
        border-bottom-style: solid
    }

    .c47 {
        border-right-style: solid;
        border-bottom-color: #000000;
        border-top-width: 1pt;
        border-right-width: 1pt;
        border-left-color: #000000;
        vertical-align: top;
        border-right-color: #000000;
        border-left-width: 1pt;
        border-top-style: solid;
        border-left-style: solid;
        border-bottom-width: 1pt;
        width: 86.8pt;
        border-top-color: #000000;
        border-bottom-style: solid
    }

    .c13 {
        border-right-style: solid;
        border-bottom-color: #000000;
        border-top-width: 1pt;
        border-right-width: 1pt;
        border-left-color: #000000;
        vertical-align: top;
        border-right-color: #000000;
        border-left-width: 1pt;
        border-top-style: solid;
        border-left-style: solid;
        border-bottom-width: 1pt;
        width: 117.8pt;
        border-top-color: #000000;
        border-bottom-style: solid
    }

    .c3 {
        border-right-style: solid;
        border-bottom-color: #000000;
        border-top-width: 1pt;
        border-right-width: 1pt;
        border-left-color: #000000;
        vertical-align: top;
        border-right-color: #000000;
        border-left-width: 1pt;
        border-top-style: solid;
        border-left-style: solid;
        border-bottom-width: 1pt;
        width: 76.5pt;
        border-top-color: #000000;
        border-bottom-style: solid
    }

    .c8 {
        border-right-style: solid;
        border-bottom-color: #000000;
        border-top-width: 1pt;
        border-right-width: 1pt;
        border-left-color: #000000;
        vertical-align: top;
        border-right-color: #000000;
        border-left-width: 1pt;
        border-top-style: solid;
        border-left-style: solid;
        border-bottom-width: 1pt;
        width: 57.1pt;
        border-top-color: #000000;
        border-bottom-style: solid
    }

    .c40 {
        border-right-style: solid;
        border-bottom-color: #000000;
        border-top-width: 1pt;
        border-right-width: 1pt;
        border-left-color: #000000;
        vertical-align: top;
        border-right-color: #000000;
        border-left-width: 1pt;
        border-top-style: solid;
        border-left-style: solid;
        border-bottom-width: 1pt;
        width: 126.8pt;
        border-top-color: #000000;
        border-bottom-style: solid
    }

    .c92 {
        border-right-style: solid;
        border-bottom-color: #000000;
        border-top-width: 1pt;
        border-right-width: 1pt;
        border-left-color: #000000;
        vertical-align: top;
        border-right-color: #000000;
        border-left-width: 1pt;
        border-top-style: solid;
        border-left-style: solid;
        border-bottom-width: 1pt;
        width: 134.1pt;
        border-top-color: #000000;
        border-bottom-style: solid
    }

    .c31 {
        border-right-style: solid;
        border-bottom-color: #000000;
        border-top-width: 1pt;
        border-right-width: 1pt;
        border-left-color: #000000;
        vertical-align: top;
        border-right-color: #000000;
        border-left-width: 1pt;
        border-top-style: solid;
        border-left-style: solid;
        border-bottom-width: 1pt;
        width: 59.2pt;
        border-top-color: #000000;
        border-bottom-style: solid
    }

    .c71 {
        border-right-style: solid;
        border-bottom-color: #000000;
        border-top-width: 1pt;
        border-right-width: 1pt;
        border-left-color: #000000;
        vertical-align: top;
        border-right-color: #000000;
        border-left-width: 1pt;
        border-top-style: solid;
        border-left-style: solid;
        border-bottom-width: 1pt;
        width: 279.4pt;
        border-top-color: #000000;
        border-bottom-style: solid
    }

    .c14 {
        border-right-style: solid;
        border-bottom-color: #000000;
        border-top-width: 1pt;
        border-right-width: 1pt;
        border-left-color: #000000;
        vertical-align: top;
        border-right-color: #000000;
        border-left-width: 1pt;
        border-top-style: solid;
        border-left-style: solid;
        border-bottom-width: 1pt;
        width: 50.4pt;
        border-top-color: #000000;
        border-bottom-style: solid
    }

    .c32 {
        color: #000000;
        font-weight: 400;
        text-decoration: none;
        vertical-align: baseline;
        font-size: 9pt;
        font-family: "Times New Roman";
        font-style: italic
    }

    .c25 {
        color: #000000;
        font-weight: 400;
        text-decoration: none;
        vertical-align: baseline;
        font-size: 14pt;
        font-family: "Times New Roman";
        font-style: normal
    }

    .c93 {
        margin-left: 21.6pt;
        padding-top: 5.2pt;
        text-indent: -18.4pt;
        padding-bottom: 0pt;
        line-height: 1.1500000000000001;
        text-align: left;
        margin-right: 0.3pt
    }

    .c50 {
        color: #000000;
        font-weight: 400;
        text-decoration: none;
        vertical-align: baseline;
        font-size: 10.5pt;
        font-family: "Times New Roman";
        font-style: normal
    }

    .c1 {
        color: #000000;
        font-weight: 700;
        text-decoration: none;
        vertical-align: baseline;
        font-size: 8pt;
        font-family: "Times New Roman";
        font-style: normal
    }

    .c91 {
        color: #000000;
        font-weight: 700;
        text-decoration: none;
        vertical-align: baseline;
        font-size: 5.5pt;
        font-family: "Times New Roman";
        font-style: normal
    }

    .c63 {
        color: #000000;
        font-weight: 700;
        text-decoration: none;
        vertical-align: baseline;
        font-size: 10pt;
        font-family: "Times New Roman";
        font-style: normal
    }

    .c20 {
        color: #000000;
        font-weight: 700;
        text-decoration: none;
        vertical-align: baseline;
        font-size: 8.5pt;
        font-family: "Times New Roman";
        font-style: normal
    }

    .c42 {
        color: #000000;
        font-weight: 400;
        text-decoration: none;
        vertical-align: baseline;
        font-size: 9pt;
        font-family: "Times New Roman";
        font-style: normal
    }

    .c57 {
        margin-left: 14.1pt;
        padding-top: 5.2pt;
        /*text-indent: -11.2pt;*/
        padding-bottom: 0pt;
        line-height: 1.1500000000000001;
        text-align: left;
        margin-right: 0.2pt
    }

    .c23 {
        color: #000000;
        font-weight: 400;
        text-decoration: none;
        vertical-align: baseline;
        font-size: 8pt;
        font-family: "Times New Roman";
        font-style: normal
    }

    .c78 {
        color: #000000;
        font-weight: 700;
        text-decoration: none;
        vertical-align: baseline;
        font-size: 11pt;
        font-family: "Times New Roman";
        font-style: normal
    }

    .c79 {
        color: #000000;
        font-weight: 400;
        text-decoration: none;
        vertical-align: baseline;
        font-size: 13pt;
        font-family: "Times New Roman";
        font-style: normal
    }

    .c7 {
        color: #000000;
        font-weight: 700;
        text-decoration: none;
        vertical-align: baseline;
        font-size: 7.5pt;
        font-family: "Times New Roman";
        font-style: normal
    }

    .c66 {
        color: #000000;
        font-weight: 400;
        text-decoration: none;
        vertical-align: super;
        font-size: 9pt;
        font-family: "Times New Roman";
        font-style: italic
    }

    .c96 {
        margin-left: 33.8pt;
        padding-top: 4.7pt;
        text-indent: -29.4pt;
        padding-bottom: 0pt;
        line-height: 1.1500000000000001;
        text-align: left;
        margin-right: 0.8pt
    }

    .c97 {
        color: #000000;
        font-weight: 400;
        text-decoration: none;
        vertical-align: baseline;
        font-size: 16.5pt;
        font-family: "Times New Roman";
        font-style: italic
    }

    .c24 {
        color: #000000;
        font-weight: 400;
        text-decoration: none;
        vertical-align: baseline;
        font-size: 11pt;
        font-family: "Times New Roman";
        font-style: normal
    }

    .c37 {
        color: #000000;
        font-weight: 700;
        text-decoration: none;
        vertical-align: baseline;
        font-size: 13pt;
        font-family: "Times New Roman";
        font-style: normal
    }

    .c34 {
        color: #000000;
        font-weight: 700;
        text-decoration: none;
        vertical-align: baseline;
        font-size: 9.5pt;
        font-family: "Times New Roman";
        font-style: normal
    }

    .c99 {
        color: #000000;
        font-weight: 700;
        text-decoration: none;
        vertical-align: baseline;
        font-size: 11.5pt;
        font-family: "Times New Roman";
        font-style: normal
    }

    .c33 {
        color: #000000;
        font-weight: 700;
        text-decoration: none;
        vertical-align: baseline;
        font-size: 9pt;
        font-family: "Times New Roman";
        font-style: normal
    }

    .c15 {
        margin-left: 30.4pt;
        padding-top: 3.2pt;
        text-indent: -27pt;
        padding-bottom: 0pt;
        line-height: 1.0666666666666667;
        text-align: left;
        margin-right: -0.7pt
    }

    .c51 {
        margin-left: 78.7pt;
        padding-top: 6.8pt;
        padding-bottom: 0pt;
        line-height: 1.0;
        text-align: center;
        margin-right: 54.2pt
    }

    .c64 {
        margin-left: 30.3pt;
        padding-top: 5.2pt;
        text-indent: -20.8pt;
        padding-bottom: 0pt;
        line-height: 1.1500000000000001;
        text-align: left
    }

    .c76 {
        margin-left: 5.2pt;
        padding-top: 5.2pt;
        text-indent: 11pt;
        padding-bottom: 0pt;
        line-height: 1.1500000000000001;
        text-align: left
    }

    .c41 {
        margin-left: 25.9pt;
        padding-top: 7.2pt;
        padding-left: -3.8pt;
        padding-bottom: 0pt;
        line-height: 1.0;
        text-align: left
    }

    .c58 {
        margin-left: 10.2pt;
        padding-top: 0pt;
        padding-bottom: 0pt;
        line-height: 1.0;
        text-align: center;
        margin-right: 8.7pt
    }

    .c101 {
        margin-left: 21.4pt;
        padding-top: 5.2pt;
        text-indent: -4.7pt;
        padding-bottom: 0pt;
        line-height: 1.1500000000000001;
        text-align: left
    }

    .c89 {
        margin-left: 5pt;
        padding-top: 5.2pt;
        text-indent: 4.9pt;
        padding-bottom: 0pt;
        line-height: 1.1500000000000001;
        text-align: left
    }

    .c19 {
        margin-left: 7.5pt;
        /*padding-top: 5pt;*/
        text-indent: -7.5pt;
        padding-bottom: 0pt;
        line-height: 1.0;
        text-align: left
    }

    .c74 {
        margin-left: 25.6pt;
        padding-top: 0pt;
        padding-left: -4.2pt;
        padding-bottom: 0pt;
        line-height: 1.0;
        text-align: left
    }

    .c29 {
        margin-left: 66.2pt;
        padding-top: 0pt;
        padding-bottom: 0pt;
        line-height: 1.0;
        text-align: center;
        margin-right: 64.5pt
    }

    .c68 {
        margin-left: 30.2pt;
        padding-top: 0.1pt;
        padding-left: -11.1pt;
        padding-bottom: 0pt;
        line-height: 1.0;
        text-align: left
    }

    .c84 {
        margin-left: 7.7pt;
        padding-top: 5.6pt;
        padding-bottom: 0pt;
        line-height: 1.5374999999999999;
        text-align: left;
        margin-right: 22.8pt
    }

    .c4 {
        /*margin-left: 50.7pt;*/
        padding-top: 4.7pt;
        padding-bottom: 0pt;
        line-height: 1.0;
        text-align: center;
        margin-right: 49.1pt
    }

    .c45 {
        margin-left: 46.4pt;
        padding-top: 4.7pt;
        padding-bottom: 0pt;
        line-height: 1.0;
        text-align: center;
        margin-right: 61.6pt
    }

    .c102 {
        margin-left: 57.8pt;
        padding-top: 5.1pt;
        text-indent: -57.8pt;
        padding-bottom: 0pt;
        line-height: 1.0;
        text-align: left
    }

    .c81 {
        margin-left: 25.6pt;
        padding-top: 0pt;
        padding-left: -10.7pt;
        padding-bottom: 0pt;
        line-height: 1.0;
        text-align: left
    }

    .c38 {
        margin-left: 27.9pt;
        padding-top: 5pt;
        padding-left: -11.1pt;
        padding-bottom: 0pt;
        line-height: 1.0;
        text-align: left
    }

    .c98 {
        margin-left: 49pt;
        padding-top: 4.7pt;
        padding-bottom: 0pt;
        line-height: 1.0;
        text-align: left
    }

    .c17 {
        padding-top: 0.4pt;
        padding-bottom: 0.1pt;
        line-height: 1.0;
        text-align: left;
        height: 11pt
    }

    .c28 {
        padding-top: 0.6pt;
        padding-bottom: 0pt;
        line-height: 1.0;
        text-align: left;
        height: 11pt
    }

    .c67 {
        margin-left: 18.1pt;
        padding-top: 4.7pt;
        padding-bottom: 0pt;
        line-height: 1.0;
        text-align: left
    }

    .c11 {
        padding-top: 0.2pt;
        padding-bottom: 0pt;
        line-height: 1.0;
        text-align: left;
        height: 11pt
    }

    .c85 {
        margin-left: 9.1pt;
        padding-top: 0pt;
        padding-bottom: 0pt;
        line-height: 1.0;
        text-align: left
    }

    .c53 {
        margin-left: 11.2pt;
        padding-top: 0pt;
        padding-bottom: 0pt;
        line-height: 1.0;
        text-align: left
    }

    .c43 {
        margin-left: 6.5pt;
        padding-top: 2.8pt;
        padding-bottom: 0pt;
        line-height: 1.0;
        text-align: center
    }

    .c44 {
        padding-top: 4.7pt;
        padding-bottom: 0pt;
        line-height: 1.0;
        text-align: right;
        margin-right: 3.4pt
    }

    .c49 {
        margin-left: 7.7pt;
        padding-top: 5.2pt;
        padding-bottom: 0pt;
        line-height: 1.0;
        text-align: left
    }

    .c26 {
        padding-top: 0pt;
        padding-bottom: 0pt;
        line-height: 1.0;
        text-align: left;
        /*height: 11pt*/
        margin-left: 5px;
    }

    .c70 {
        color: #000000;
        text-decoration: none;
        vertical-align: super;
        font-family: "Times New Roman";
        font-style: normal
    }

    .c80 {
        padding-top: 4.7pt;
        padding-bottom: 0pt;
        line-height: 1.0;
        text-align: right;
        margin-right: 11.6pt
    }

    .c61 {
        margin-left: 165.2pt;
        padding-top: 2.5pt;
        padding-bottom: 0pt;
        line-height: 1.0;
        text-align: left
    }

    .c77 {
        margin-left: 7.7pt;
        padding-top: 6.2pt;
        padding-bottom: 0pt;
        line-height: 1.0;
        text-align: left
    }

    .c82 {
        margin-left: 18.1pt;
        padding-top: 2.5pt;
        padding-bottom: 0pt;
        line-height: 1.0;
        text-align: left
    }

    .c0 {
        padding-top: 3.7pt;
        padding-bottom: 0pt;
        line-height: 1.0;
        text-align: right;
        margin-right: 3.3pt
    }

    .c2 {
        margin-left: 24.1pt;
        padding-top: 0pt;
        padding-bottom: 0pt;
        line-height: 1.0;
        text-align: left
    }

    .c88 {
        padding-top: 0.4pt;
        padding-bottom: 0pt;
        line-height: 1.0;
        text-align: left;
        height: 11pt
    }

    .c22 {
        padding-top: 0.3pt;
        padding-bottom: 0pt;
        line-height: 1.0;
        text-align: left;
        height: 11pt
    }

    .c72 {
        margin-left: 50.4pt;
        padding-top: 4.7pt;
        padding-bottom: 0pt;
        line-height: 1.0;
        text-align: left
    }

    .c94 {
        margin-left: 78.5pt;
        padding-top: 2.3pt;
        padding-bottom: 0pt;
        line-height: 1.0;
        text-align: center
    }

    .c5 {
        padding-top: 0pt;
        padding-bottom: 0pt;
        line-height: 1.15;
        text-align: left;
        height: 11pt
    }

    .c65 {
        margin-left: 6pt;
        padding-top: 0.1pt;
        padding-bottom: 0pt;
        line-height: 1.0;
        text-align: left
    }

    .c54 {
        margin-left: 6.2pt;
        padding-top: 7.5pt;
        padding-bottom: 0pt;
        line-height: 1.0;
        text-align: left
    }

    .c95 {
        padding-top: 4.7pt;
        padding-bottom: 0pt;
        line-height: 1.0;
        text-align: right;
        margin-right: 0.7pt
    }

    .c30 {
        margin-left: 154.8pt;
        padding-top: 4.7pt;
        padding-bottom: 0pt;
        line-height: 1.0;
        text-align: left
    }

    .c16 {
        margin-left: 7.5pt;
        /*padding-top: 6.8pt;*/
        padding-bottom: 0pt;
        line-height: 1.0;
        text-align: left
    }

    .c100 {
        margin-left: 11.5pt;
        padding-top: 5.3pt;
        padding-bottom: 0pt;
        line-height: 1.0;
        text-align: left
    }

    .c60 {
        padding-top: 0.5pt;
        padding-bottom: 0pt;
        line-height: 1.0;
        text-align: left;
        height: 11pt
    }

    .c73 {
        margin-left: 45.2pt;
        padding-top: 3.3pt;
        padding-bottom: 0pt;
        line-height: 1.0;
        text-align: left
    }

    .c87 {
        padding-top: 0pt;
        padding-bottom: 0pt;
        line-height: 1.0;
        text-align: center;
        margin-right: 54.2pt
    }

    .c27 {
        padding-top: 0pt;
        padding-bottom: 0pt;
        line-height: 1.0;
        text-align: right;
        margin-right: 103.8pt
    }

    .c10 {
        margin-left: 1.5pt;
        padding-top: 4.7pt;
        padding-bottom: 0pt;
        line-height: 1.0;
        text-align: center
    }

    .c86 {
        margin-left: 17.9pt;
        padding-top: 0.1pt;
        padding-bottom: 0pt;
        line-height: 1.0;
        text-align: left
    }

    .c62 {
        margin-left: 51.2pt;
        padding-top: 0pt;
        padding-bottom: 0pt;
        line-height: 1.0;
        text-align: left
    }

    .c55 {
        padding-top: 0.1pt;
        padding-bottom: 0pt;
        line-height: 1.0;
        text-align: left;
        height: 11pt
    }

    .c75 {
        margin-left: 6.2pt;
        border-spacing: 0;
        border-collapse: collapse;
        margin-right: auto
    }

    .c83 {
        background-color: #ffffff;
        max-width: 515pt;
        padding: 0pt 15pt 0pt 15pt
    }

    .c21 {
        padding: 0;
        margin: 0
    }

    .c39 {
        background-color: #ebf0de;
        font-size: 8pt
    }

    .c69 {
        color: #4f81bc;
        font-size: 13pt
    }

    .c36 {
        font-size: 8pt;
        font-weight: 700
    }

    .c90 {
        font-size: 9pt
    }

    .c9 {
        /*height: 14pt*/
    }

    .c12 {
        height: 29pt
    }

    .c52 {
        font-size: 8pt
    }

    .c46 {
        height: 40pt
    }

    .title {
        padding-top: 24pt;
        color: #000000;
        font-weight: 700;
        font-size: 36pt;
        padding-bottom: 6pt;
        font-family: "Times New Roman";
        line-height: 1.0;
        page-break-after: avoid;
        text-align: left
    }

    .subtitle {
        padding-top: 18pt;
        color: #666666;
        font-size: 24pt;
        padding-bottom: 4pt;
        font-family: "Georgia";
        line-height: 1.0;
        page-break-after: avoid;
        font-style: italic;
        text-align: left
    }

    li {
        color: #000000;
        font-size: 11pt;
        font-family: "Times New Roman"
    }

    p {
        margin: 0;
        color: #000000;
        font-size: 11pt;
        font-family: "Times New Roman"
    }

    h1 {
        padding-top: 3.3pt;
        color: #000000;
        font-size: 11pt;
        padding-bottom: 0pt;
        font-family: "Times New Roman";
        line-height: 1.0;
        text-align: left
    }

    h2 {
        padding-top: 0pt;
        color: #000000;
        font-weight: 700;
        font-size: 8pt;
        padding-bottom: 0pt;
        font-family: "Times New Roman";
        line-height: 1.0;
        text-align: left
    }

    h3 {
        padding-top: 14pt;
        color: #000000;
        font-weight: 700;
        font-size: 14pt;
        padding-bottom: 4pt;
        font-family: "Times New Roman";
        line-height: 1.0;
        page-break-after: avoid;
        text-align: left
    }

    h4 {
        padding-top: 12pt;
        color: #000000;
        font-weight: 700;
        font-size: 12pt;
        padding-bottom: 2pt;
        font-family: "Times New Roman";
        line-height: 1.0;
        page-break-after: avoid;
        text-align: left
    }

    h5 {
        padding-top: 11pt;
        color: #000000;
        font-weight: 700;
        font-size: 11pt;
        padding-bottom: 2pt;
        font-family: "Times New Roman";
        line-height: 1.0;
        page-break-after: avoid;
        text-align: left
    }

    h6 {
        padding-top: 10pt;
        color: #000000;
        font-weight: 700;
        font-size: 10pt;
        padding-bottom: 2pt;
        font-family: "Times New Roman";
        line-height: 1.0;
        page-break-after: avoid;
        text-align: left
    }
</style>
<div class="c83">
    <div style="height: 50px">
        <div style="width: 320px;float: left;">
            <p class="c15"><span
                        class="c69">THÔNG BÁO THU PHÍ THÁNG <?= 'T' . date('m/Y') ?> EXPENSE NOTICE <?= date('m Y') ?></span>
            </p>
        </div>
        <div style="float: right">
            <span class="c24">Số: <?= $apartment->name ?>_<?= 'T' . date('m/Y') ?>_TBP</span>
            <p class="c73"><span class="c24">Ngày: <?= date('d/m/Y') ?></span></p>
        </div>
        <hr style="page-break-before:always;display:none;clear: both">
    </div>

    <h2 class="c19"><span class="c1">Tên Khách hàng/ Client's Name: <?= $apartment->resident_user_name ?></span></h2>
    <p class="c16">
        <span class="c36">Mã căn hộ : &nbsp; <?= $apartment->name ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
        <span class="c52" style="float: right">STT : <span class="c39">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;103</span></span>
    </p>
    <?php if(!empty($servicePaymentFee) || !empty($total_servicePaymentFee)){ ?>
        <ol class="c21 lst-kix_list_2-0 start" start="1">
            <li class="c81"><h2 style="display:inline"><span class="c1">Phí quản lý/ Management Cost&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Từ/from 01/07/2019 đến/to 31/07/2019</span>
                </h2></li>
        </ol>
        <p class="c17"><span class="c7"></span></p><a id="t.72b07d2fc0a20d952a28d2681173443a373c4cc8"></a><a id="t.0"></a>
        <table class="c75">
            <tbody>
            <tr class="c12">
                <td class="c14" colspan="1" rowspan="1">
                    <p class="c28"><span class="c20"></span></p>
                    <p class="c58"><span class="c1">STT/No</span></p>
                </td>
                <td class="c3" colspan="1" rowspan="1"><p class="c64"><span class="c1">Diện tích / Total area</span></p>
                </td>
                <td class="c6" colspan="1" rowspan="1">
                    <p class="c11"><span class="c34"></span></p>
                    <p class="c86"><span class="c1">Đơn giá/<?php if(!empty($serviceBuildingConfig) && $serviceBuildingConfig->unit == ServiceBuildingConfig::UNIT_APARTMENT){ echo "Căn hộ";}else{ echo 'm</span><span class="c36 c70">2</span>';} ?></p>
                </td>
                <td class="c31" colspan="1" rowspan="1">
                    <p class="c11"><span class="c34"></span></p>
                    <p class="c65"><span class="c1">Unit Price/<?php if(!empty($serviceBuildingConfig) && $serviceBuildingConfig->unit == ServiceBuildingConfig::UNIT_APARTMENT){ echo "Apartment";}else{ echo 'm</span><span class="c36 c70">2</span>';} ?></p>
                </td>
                <td class="c48" colspan="1" rowspan="1"><p class="c96"><span class="c1">Thành tiền/Amount - ( VND)</span>
                    </p></td>
                <td class="c8" colspan="1" rowspan="1"><p class="c57"><span class="c1">CÁC KHOẢN NỢ PHÍ</span>
                    </p></td>
                <td class="c35" colspan="1" rowspan="1">
                    <p class="c28"><span class="c20"></span></p>
                    <p class="c53"><span class="c1">Ghi chú/Note</span></p>
                </td>
            </tr>
            <?php $total_building_fee = 0; if(!empty($servicePaymentFee)){ $total_building_fee += $servicePaymentFee->price; ?>
                <tr class="c9">
                    <td class="c14" colspan="1" rowspan="1"><p class="c10"><span class="c23">1</span></p></td>
                    <td class="c3" colspan="1" rowspan="1"><p class="c98"><span class="c23"><?= $apartment->capacity ?></span></p></td>
                    <td class="c92" colspan="2" rowspan="1"><p class="c45"><span class="c23"><?= CUtils::formatPrice($serviceBuildingConfig->price) ?></span></p></td>
                    <td class="c48" colspan="1" rowspan="1"><p class="c72"><span class="c23"><?= CUtils::formatPrice($servicePaymentFee->price) ?></span></p></td>
                    <td class="c8" colspan="1" rowspan="1"><p class="c80"><span class="c23">-</span></p></td>
                    <td class="c35" colspan="1" rowspan="1"><p class="c26"><span class="c23"></span></p></td>
                </tr>
            <?php } ?>
            <?php if(!empty($total_servicePaymentFee)){ $total_building_fee += (int)$total_servicePaymentFee->more_money_collecte; ?>
                <tr class="c9">
                    <td class="c40" colspan="2" rowspan="1"><p class="c67"><span class="c1">Nợ cũ</span></p>
                    </td>
                    <td class="c71" colspan="4" rowspan="1"><p class="c30"><span class="c1"><?= CUtils::formatPrice((int)$total_servicePaymentFee->more_money_collecte) ?></span></p></td>
                    <td class="c35" colspan="1" rowspan="1"><p class="c26"><span class="c23"></span></p></td>
                </tr>
            <?php } ?>
            <tr class="c9">
                <td class="c40" colspan="2" rowspan="1"><p class="c67"><span class="c1">Tổng cộng/Total payment</span></p>
                </td>
                <td class="c71" colspan="4" rowspan="1"><p class="c30"><span class="c1"><?= CUtils::formatPrice($total_building_fee) ?></span></p></td>
                <td class="c35" colspan="1" rowspan="1"><p class="c26"><span class="c23"></span></p></td>
            </tr>
            </tbody>
        </table>
    <?php $total_price_all += $total_building_fee; } ?>

    <?php $total_water_fee = 0; if(!empty($servicePaymentFeeWater) || !empty($total_servicePaymentFeeWater)){
        $json_desc = [];
        if(!empty($servicePaymentFeeWater)){
            if(!empty($servicePaymentFeeWater->json_desc)){
                $json_desc = json_decode($servicePaymentFeeWater->json_desc, true);
            }
            $json_desc['price'] = $servicePaymentFeeWater->more_money_collecte;
            $total_water_fee += $servicePaymentFeeWater->more_money_collecte;
        }
        ?>
        <ol class="c21 lst-kix_list_2-0" start="2">
            <li class="c41">
                <span class="c78">Tiền nước/ Water cost:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                <span class="c1">Từ/from &nbsp;25/06/2019 &nbsp;đến/to 25/07/2019</span>
            </li>
        </ol>
        <p class="c11"><span class="c91"></span></p><a id="t.d21c913fd43e68461d0fcd5773e11a5d65cf0e64"></a><a id="t.1"></a>
        <table class="c75">
            <tbody>
            <tr class="c12">
                <td class="c14" colspan="1" rowspan="2"><p class="c26"><span class="c33"></span></p>
                    <p class="c54"><span class="c1">1.Phí nước</span></p></td>
                <td class="c3" colspan="1" rowspan="1"><p class="c93"><span class="c1">Chỉ số cũ / Previous Indicator</span>
                    </p></td>
                <td class="c6" colspan="1" rowspan="1"><p class="c76"><span class="c1">Chỉ số mới / Current Indicator</span>
                    </p></td>
                <td class="c56" colspan="2" rowspan="1"><p class="c55"><span class="c20"></span></p>
                    <p class="c62"><span class="c1">Tiêu thụ / Consumption (m3)</span></p></td>
                <td class="c35" colspan="1" rowspan="1"><p class="c28"><span class="c20"></span></p>
                    <p class="c2"><span class="c1">Ghi chú/Note</span></p></td>
            </tr>
            <tr class="c9">
                <td class="c3" colspan="1" rowspan="1"><p class="c0"><span class="c23"><?php if(!empty($json_desc)){ echo $json_desc['month']['start_index']; } ?></span></p></td>
                <td class="c6" colspan="1" rowspan="1"><p class="c0"><span class="c23"><?php if(!empty($json_desc)){ echo $json_desc['month']['end_index']; } ?></span></p></td>
                <td class="c56" colspan="2" rowspan="1"><p class="c43"><span class="c23"><?php if(!empty($json_desc)){ echo $json_desc['month']['total_index']; } ?></span></p></td>
                <td class="c35" colspan="1" rowspan="1"><p class="c26"><span class="c23"></span></p></td>
            </tr>
            <tr class="c46">
                <td class="c14" colspan="1" rowspan="1"><p class="c26"><span class="c33"></span></p>
                    <p class="c100"><span class="c1">STT/No</span></p></td>
                <td class="c3" colspan="1" rowspan="1"><p class="c89"><span
                                class="c1">Tiêu thụ thực tế Consumption (m3)</span></p></td>
                <td class="c6" colspan="1" rowspan="1"><p class="c101"><span class="c1">Đơn giá/m3 Price/m3</span></p></td>
                <td class="c13" colspan="1" rowspan="1"><p class="c22"><span class="c37"></span></p>
                    <p class="c85"><span class="c1">Thành tiền / Amount – VND</span></p></td>
                <td class="c18" colspan="2" rowspan="1"><p class="c22"><span class="c37"></span></p>
                    <p class="c29"><span class="c1">Ghi chú/Note</span></p></td>
            </tr>
            <tr class="c9">
                <td class="c14" colspan="1" rowspan="1"><p class="c95"><span class="c23">1</span></p></td>
                <td class="c3" colspan="1" rowspan="1"><p class="c44"><span class="c23"><?php if(!empty($json_desc)){ echo $json_desc['month']['total_index']; } ?></span></p></td>
                <td class="c6" colspan="1" rowspan="1"><p class="c44"><span class="c23">định mức</span></p></td>
                <td class="c13" colspan="1" rowspan="1"><p class="c44"><span class="c23"><?php if(!empty($json_desc)){ echo CUtils::formatPrice($json_desc['price']); } ?></span></p></td>
                <td class="c18" colspan="2" rowspan="1"><p class="c26"><span class="c23"><?php if(!empty($json_desc)){ echo implode("<br>", $json_desc['dm']); } ?></span></p></td>
            </tr>
            <?php if(!empty($total_servicePaymentFeeWater)){ $total_water_fee += (int)$total_servicePaymentFeeWater->more_money_collecte; ?>
                <tr class="c9">
                    <td class="c40" colspan="2" rowspan="1"><p class="c4"><span class="c1">Nợ cũ:</span></p></td>
                    <td class="c59" colspan="3" rowspan="1"><p class="c94"><span class="c23"><?= CUtils::formatPrice((int)$total_servicePaymentFeeWater->more_money_collecte) ?></span></p></td>
                    <td class="c35" colspan="1" rowspan="1"><p class="c26"><span class="c23"></span></p></td>
                </tr>
            <?php } ?>
            <tr class="c9">
                <td class="c40" colspan="2" rowspan="1"><p class="c82"><span class="c1">Tổng cộng/Total payment</span></p>
                </td>
                <td class="c59" colspan="3" rowspan="1"><p class="c61"><span class="c1"><?= CUtils::formatPrice($total_water_fee) ?></span></p></td>
                <td class="c35" colspan="1" rowspan="1"><p class="c26"><span class="c23"></span></p></td>
            </tr>
            </tbody>
        </table>
    <?php $total_price_all += $total_water_fee;} ?>
    <p class="c26"><span class="c63"></span></p>
    <p class="c26"><span class="c63"></span></p>
    <p class="c60"><span class="c99"></span></p>
    <ol class="c21 lst-kix_list_2-0" start="3">
        <li class="c74">
            <h2 style="display:inline">
                <span class="c1">Số thanh toán/ Payment</span>
            </h2>
        </li>
    </ol>
    <p class="c51">
        <span class="c1">Tổng thanh toán /Total payment : <?= CUtils::formatPrice($total_price_all) ?></span>
        <!--        <span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 78.44px; height: 23.57px;">-->
        <!--            <img alt="" src="images/image1.png" style="width: 78.44px; height: 23.57px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);" title="">-->
        <!--        </span>-->
    </p>
    <p class="c16"><span class="c23">Hạn thanh toán / Payment due date : 10/08/2019 (AUGUST 10st 08).</span></p>
    <p class="c16"><span class="c23">Xin vui lòng thanh toán số tiền trên cho Ban Quản lý tại sảnh T1 hoặc chuyển vào tài khoản công ty.</span>
    </p>
    <p class="c16"><span class="c23">You are kindly requested to finalize the above payment for Management Unit or transfer your payment to our active bank account.</span>
    </p>
    <p class="c49"><span class="c90">Tên người nhận/At : </span><span class="c33">CÔNG TY TNHH TM DV PJK ONE</span></p>
    <p class="c77"><span class="c42">Số tài khoản / Account No. : 10220583560015 – Tại Ngân Hàng: Techcombank Chi Nhánh Phú Mỹ Hưng – TP.HCM</span>
    </p>
    <p class="c84"><span class="c33">Vui lòng ghi đầy đủ khi nộp tiền qua tài khoản với nội dung: Mã căn: ……nộp Phí QL tháng ……. + Phí nước tháng... ) Ghi chú / Note:</span>
    </p>
    <ul class="c21 lst-kix_list_1-0 start">
        <li class="c68"><span class="c32">Nếu sau ngày 16 của tháng mà Quý Cư Dân vẫn chưa thanh toán tiền thì BQL sẽ tạm ngưng cung cấp dịch vụ tiện ích.</span>
        </li>
        <li class="c38"><span
                    class="c32">The Building managerment will cut service the nonpayment Apartment after 16 </span><span
                    class="c66">th</span><span class="c32">&nbsp;.</span></li>
    </ul>
    <p class="c22"><span class="c97"></span></p>
    <h2 class="c87">
        <span class="c1">BAN QUẢN LÝ TÒA NHÀ&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;NGƯỜI LẬP</span>
        <span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 656.74px; height: 91.91px;">
            <img alt="" src="https://api.staging.building.luci.vn/image2.png"
                 style="width: 656.74px; height: 91.91px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);"
                 title="">
        </span>
    </h2>
    <p class="c27"><span class="c1">MAI THỊ THÚY</span></p>
</div>