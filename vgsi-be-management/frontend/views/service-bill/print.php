<?php
/* @var $this yii\web\View */
/* @var $serviceBill common\models\ServiceBill */
/* @var $serviceBillItems common\models\ServiceBillItem */

/* @var $buildingCluster common\models\BuildingCluster */

use common\helpers\CUtils;
use yii\helpers\Html;

?>
<style type="text/css">
    ol {
        margin: 0;
        padding: 0
    }

    table td, table th {
        padding: 0
    }

    .c18 {
        border-right-style: solid;
        padding: 2pt 2pt 2pt 2pt;
        border-bottom-color: #dddddd;
        border-top-width: 0pt;
        border-right-width: 0pt;
        border-left-color: #dddddd;
        vertical-align: middle;
        border-right-color: #dddddd;
        border-left-width: 0pt;
        border-top-style: solid;
        background-color: #ffffff;
        border-left-style: solid;
        border-bottom-width: 0pt;
        width: 82.5pt;
        border-top-color: #dddddd;
        border-bottom-style: solid
    }

    .c23 {
        border-right-style: solid;
        padding: 2pt 2pt 2pt 2pt;
        border-bottom-color: #dddddd;
        border-top-width: 0pt;
        border-right-width: 0pt;
        border-left-color: #dddddd;
        vertical-align: middle;
        border-right-color: #dddddd;
        border-left-width: 0pt;
        border-top-style: solid;
        background-color: #ffffff;
        border-left-style: solid;
        border-bottom-width: 0pt;
        width: 114.8pt;
        border-top-color: #dddddd;
        border-bottom-style: solid
    }

    .c28 {
        border-right-style: solid;
        padding: 0.7pt 0.7pt 0.7pt 0.7pt;
        border-bottom-color: #dddddd;
        border-top-width: 0pt;
        border-right-width: 0pt;
        border-left-color: #dddddd;
        vertical-align: middle;
        border-right-color: #dddddd;
        border-left-width: 0pt;
        border-top-style: solid;
        background-color: #ffffff;
        border-left-style: solid;
        border-bottom-width: 0pt;
        width: 234pt;
        border-top-color: #dddddd;
        border-bottom-style: solid
    }

    .c10 {
        border-right-style: solid;
        padding: 2pt 2pt 2pt 2pt;
        border-bottom-color: #dddddd;
        border-top-width: 0pt;
        border-right-width: 0pt;
        border-left-color: #dddddd;
        vertical-align: middle;
        border-right-color: #dddddd;
        border-left-width: 0pt;
        border-top-style: solid;
        background-color: #ffffff;
        border-left-style: solid;
        border-bottom-width: 0pt;
        width: 91.5pt;
        border-top-color: #dddddd;
        border-bottom-style: solid
    }

    .c20 {
        border-right-style: solid;
        padding: 2pt 2pt 2pt 2pt;
        border-bottom-color: #dddddd;
        border-top-width: 0pt;
        border-right-width: 0pt;
        border-left-color: #dddddd;
        vertical-align: middle;
        border-right-color: #dddddd;
        border-left-width: 0pt;
        border-top-style: solid;
        background-color: #ffffff;
        border-left-style: solid;
        border-bottom-width: 0pt;
        width: 84.8pt;
        border-top-color: #dddddd;
        border-bottom-style: solid
    }

    .c25 {
        border-right-style: solid;
        padding: 2pt 2pt 2pt 2pt;
        border-bottom-color: #dddddd;
        border-top-width: 0pt;
        border-right-width: 0pt;
        border-left-color: #dddddd;
        vertical-align: middle;
        border-right-color: #dddddd;
        border-left-width: 0pt;
        border-top-style: solid;
        background-color: #ffffff;
        border-left-style: solid;
        border-bottom-width: 0pt;
        width: 70.5pt;
        border-top-color: #dddddd;
        border-bottom-style: solid
    }

    .c27 {
        border-right-style: solid;
        padding: 5pt 5pt 5pt 5pt;
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
        width: 187.2pt;
        border-top-color: #000000;
        border-bottom-style: solid
    }

    .c2 {
        border-right-style: solid;
        padding: 5pt 5pt 5pt 5pt;
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
        width: 93.6pt;
        border-top-color: #000000;
        border-bottom-style: solid
    }

    .c5 {
        color: #000000;
        font-weight: 400;
        text-decoration: none;
        vertical-align: baseline;
        font-size: 11pt;
        font-family: "Times New Roman";
        font-style: normal
    }

    .c21 {
        color: #000000;
        font-weight: 700;
        text-decoration: none;
        vertical-align: baseline;
        font-size: 16pt;
        font-family: "Times New Roman";
        font-style: normal
    }

    .c29 {
        color: #000000;
        font-weight: 400;
        text-decoration: none;
        vertical-align: baseline;
        font-size: 12pt;
        font-family: "Times New Roman";
        font-style: normal
    }

    .c13 {
        color: #000000;
        font-weight: 700;
        text-decoration: none;
        vertical-align: baseline;
        font-size: 10.5pt;
        font-family: "Times New Roman";
        font-style: italic
    }

    .c12 {
        color: #000000;
        font-weight: 700;
        text-decoration: none;
        vertical-align: baseline;
        font-size: 10.5pt;
        font-family: "Times New Roman";
        font-style: normal
    }

    .c11 {
        color: #000000;
        font-weight: 400;
        text-decoration: none;
        vertical-align: baseline;
        font-size: 10.5pt;
        font-family: "Times New Roman";
        font-style: italic
    }

    .c6 {
        color: #000000;
        font-weight: 400;
        text-decoration: none;
        vertical-align: baseline;
        font-size: 10.5pt;
        font-family: "Times New Roman";
        font-style: normal
    }

    .c0 {
        padding-top: 0pt;
        padding-bottom: 0pt;
        line-height: 1.0;
        orphans: 2;
        widows: 2;
        text-align: center
    }

    .c16 {
        padding-top: 0pt;
        padding-bottom: 0pt;
        line-height: 1.5625;
        orphans: 2;
        widows: 2;
        text-align: justify
    }

    .c22 {
        padding-top: 0pt;
        padding-bottom: 0pt;
        line-height: 1.0;
        orphans: 2;
        widows: 2;
        text-align: justify
    }

    .c26 {
        padding-top: 0pt;
        padding-bottom: 0pt;
        line-height: 1.5625;
        orphans: 2;
        widows: 2;
        text-align: right
    }

    .c8 {
        margin-left: 1.5pt;
        border-spacing: 0;
        border-collapse: collapse;
        margin-right: auto
    }

    .c17 {
        border-spacing: 0;
        border-collapse: collapse;
        margin-right: auto
    }

    .c4 {
        padding-top: 0pt;
        padding-bottom: 0pt;
        line-height: 1.0;
        text-align: center
    }

    .c3 {
        padding-top: 0pt;
        padding-bottom: 0pt;
        line-height: 1.0;
        text-align: left
    }

    .c14 {
        font-size: 10.5pt;
        font-family: "Times New Roman";
        font-style: italic;
        font-weight: 400
    }

    .c24 {
        padding-top: 0pt;
        padding-bottom: 0pt;
        line-height: 1.15;
        text-align: left
    }

    .c30 {
        font-size: 10.5pt;
        font-family: "Times New Roman";
        font-weight: 700
    }

    .c9 {
        orphans: 2;
        widows: 2;
        height: 11pt
    }

    .c32 {
        background-color: #ffffff;
        max-width: 468pt;
        padding: 30px
    }

    .c31 {
        font-size: 10.5pt;
        font-family: "Times New Roman";
        font-weight: 400
    }

    .c1 {
        height: 42pt
    }

    .c15 {
        height: 11pt
    }

    .c19 {
        height: 20pt
    }

    .c7 {
        height: 0pt
    }

    .title {
        padding-top: 0pt;
        color: #000000;
        font-size: 26pt;
        padding-bottom: 3pt;
        font-family: "Arial";
        line-height: 1.15;
        page-break-after: avoid;
        orphans: 2;
        widows: 2;
        text-align: left
    }

    .subtitle {
        padding-top: 0pt;
        color: #666666;
        font-size: 15pt;
        padding-bottom: 16pt;
        font-family: "Arial";
        line-height: 1.15;
        page-break-after: avoid;
        orphans: 2;
        widows: 2;
        text-align: left
    }

    li {
        color: #000000;
        font-size: 11pt;
        font-family: "Arial"
    }

    p {
        margin: 0;
        color: #000000;
        font-size: 11pt;
        font-family: "Arial"
    }

    h1 {
        padding-top: 20pt;
        color: #000000;
        font-size: 20pt;
        padding-bottom: 6pt;
        font-family: "Arial";
        line-height: 1.15;
        page-break-after: avoid;
        orphans: 2;
        widows: 2;
        text-align: left
    }

    h2 {
        padding-top: 18pt;
        color: #000000;
        font-size: 16pt;
        padding-bottom: 6pt;
        font-family: "Arial";
        line-height: 1.15;
        page-break-after: avoid;
        orphans: 2;
        widows: 2;
        text-align: left
    }

    h3 {
        padding-top: 16pt;
        color: #434343;
        font-size: 14pt;
        padding-bottom: 4pt;
        font-family: "Arial";
        line-height: 1.15;
        page-break-after: avoid;
        orphans: 2;
        widows: 2;
        text-align: left
    }

    h4 {
        padding-top: 14pt;
        color: #666666;
        font-size: 12pt;
        padding-bottom: 4pt;
        font-family: "Arial";
        line-height: 1.15;
        page-break-after: avoid;
        orphans: 2;
        widows: 2;
        text-align: left
    }

    h5 {
        padding-top: 12pt;
        color: #666666;
        font-size: 11pt;
        padding-bottom: 4pt;
        font-family: "Arial";
        line-height: 1.15;
        page-break-after: avoid;
        orphans: 2;
        widows: 2;
        text-align: left
    }

    h6 {
        padding-top: 12pt;
        color: #666666;
        font-size: 11pt;
        padding-bottom: 4pt;
        font-family: "Arial";
        line-height: 1.15;
        page-break-after: avoid;
        font-style: italic;
        orphans: 2;
        widows: 2;
        text-align: left
    }</style>
<div class="c32">
    <p class="c24 c9"><span class="c5"></span></p><a id="t.e3c2ec6a30cd2e4a1b60befc8c296d7cd086240d"></a><a
            id="t.0"></a>
    <table class="c17">
        <tbody>
        <tr class="c7">
            <td class="c28" colspan="1" rowspan="1"><p class="c16"><span class="c12">BAN QUẢN LÝ TÒA NHÀ</span></p></td>
            <td class="c28" colspan="1" rowspan="1"><p class="c26"><span class="c12">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; Số: <?= !empty($serviceBill->number) ? $serviceBill->number : 'Luci/PT2019/05/000001' ?></span>
                </p></td>
        </tr>
        </tbody>
    </table>
    <p class="c0"><span class="c21">PHIẾU THU</span></p>
    <p class="c0"><span class="c31">&nbsp;Lập ngày <?= date('d/m/Y', $serviceBill->created_at) ?> - Liên 1: Lưu</span>
    </p>
    <p class="c22"><span class="c6">Họ và tên người nộp tiền: <?= $serviceBill->payer_name ?></span></p>
    <p class="c22"><span class="c6">Mặt bằng: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= $serviceBill->apartment->parent_path . $serviceBill->apartment->name ?></span>
    </p>
    <p class="c22"><span class="c6">Nội dung thu tiền:</span></p><a id="t.208fbd4c21eddf70c18bc7641e954c7aec088547"></a><a
            id="t.1"></a>
    <table class="c17">
        <tbody>
        <tr class="c7">
            <td class="c2" colspan="1" rowspan="1"><p class="c4"><span class="c12">Dịch vụ</span></p></td>
            <td class="c2" colspan="1" rowspan="1"><p class="c4"><span class="c12">Đợt thanh toán</span></p></td>
            <td class="c2" colspan="1" rowspan="1"><p class="c4"><span class="c12">Phải thu</span></p></td>
            <td class="c2" colspan="1" rowspan="1"><p class="c4"><span class="c12">Thực thu</span></p></td>
            <td class="c2" colspan="1" rowspan="1"><p class="c4"><span class="c12">Diễn giải</span></p></td>
        </tr>
        <?php
        $total_price = 0;
        $i = 0;
        foreach ($serviceBillItems as $serviceBillItem) {
            $i++;
            $total_price += $serviceBillItem->price;
            ?>
            <tr class="c7">
                <td class="c2" colspan="1" rowspan="1"><p class="c3"><span
                                class="c6"><?= $serviceBillItem->serviceMapManagement->service_name; ?></span></p></td>
                <td class="c2" colspan="1" rowspan="1"><p class="c3"><span
                                class="c6"><?= date('m/Y', $serviceBillItem->fee_of_month) ?></span></p></td>
                <td class="c2" colspan="1" rowspan="1"><p class="c3"><span
                                class="c6"><?= CUtils::formatPrice($serviceBillItem->price) ?></span></p></td>
                <td class="c2" colspan="1" rowspan="1"><p class="c3"><span
                                class="c6"><?= CUtils::formatPrice($serviceBillItem->price) ?></span></p></td>
                <td class="c2" colspan="1" rowspan="1"><p class="c3"><span
                                class="c6" style="white-space:pre-wrap;"><?= $serviceBillItem->description ?></span></p></td>
            </tr>
        <?php } ?>
        <tr class="c19">
            <td class="c27" colspan="2" rowspan="1"><p class="c4"><span class="c12">Tổng</span></p></td>
            <td class="c2" colspan="1" rowspan="1"><p class="c3"><span
                            class="c12"><?= CUtils::formatPrice($total_price) ?></span></p></td>
            <td class="c2" colspan="1" rowspan="1"><p class="c3"><span
                            class="c12"><?= CUtils::formatPrice($total_price) ?></span></p></td>
            <td class="c2" colspan="1" rowspan="1"><p class="c3 c15"><span class="c6"></span></p></td>
        </tr>
        </tbody>
    </table>
    <p class="c22"><span
                class="c30">Đã nhận đủ số tiền (viết bằng chữ): <?= ucfirst(strtolower(CUtils::convert_number_to_words($total_price))) ?> VNĐ</span>
    </p><a id="t.1c5e5cdd991ccac04acafbf06cba6b81d56404bf"></a><a id="t.2"></a>
    <table class="c8">
        <tbody>
        <tr class="c1">
            <td class="c23" colspan="1" rowspan="1"><p class="c0"><span class="c12">Giám đốc</span></p>
                <p class="c0"><span class="c11">(Ký, họ tên, đóng dấu)</span></p></td>
            <td class="c20" colspan="1" rowspan="1"><p class="c0"><span class="c12">Kế toán trưởng</span></p>
                <p class="c0"><span class="c11">(Ký, họ tên)</span></p></td>
            <td class="c18" colspan="1" rowspan="1"><p class="c0"><span class="c12">Người nộp tiền</span></p>
                <p class="c0"><span class="c11">(Ký, họ tên)</span></p></td>
            <td class="c10" colspan="1" rowspan="1"><p class="c0"><span class="c12">Người lập phiếu</span></p>
                <p class="c0"><span class="c11">(Ký, họ tên)</span></p></td>
            <td class="c25" colspan="1" rowspan="1"><p class="c0"><span class="c12">Thủ quỹ</span></p>
                <p class="c0"><span class="c14">(Ký, họ tên)</span></p></td>
        </tr>
        </tbody>
    </table>
    <?php if ($i > 5) { ?>
        <hr style="page-break-before:always;border: none">
    <?php } else { ?>
        <p class="c24 c9"><span class="c5"></span></p>
        <p style="text-align: center;border: none">__ __ __ __ __ __ __ __ __ __ __ __ __ __ __ __ __ __ __ __ __ __ __
            __ __ __ __ __ __ __</p>
        <p class="c24 c9"><span class="c5"></span></p>
    <?php } ?>
    <p class="c9 c24"><span class="c5"></span></p><a id="t.e3c2ec6a30cd2e4a1b60befc8c296d7cd086240d"></a><a
            id="t.3"></a>
    <table class="c17">
        <tbody>
        <tr class="c7">
            <td class="c28" colspan="1" rowspan="1"><p class="c16"><span class="c12">BAN QUẢN LÝ TÒA NHÀ</span></p></td>
            <td class="c28" colspan="1" rowspan="1"><p class="c26"><span class="c12">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; Số: <?= !empty($serviceBill->number) ? $serviceBill->number : 'Luci/PT2019/05/000001' ?></span>
                </p></td>
        </tr>
        </tbody>
    </table>
    <p class="c0"><span class="c21">PHIẾU THU</span></p>
    <p class="c0"><span class="c31">&nbsp;Lập ngày <?= date('d/m/Y', $serviceBill->created_at) ?> - Liên 2: Giao cho cư dân</span>
    </p>
    <p class="c22"><span class="c6">Họ và tên người nộp tiền: <?= $serviceBill->payer_name ?></span></p>
    <p class="c22"><span class="c6">Mặt bằng: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= $serviceBill->apartment->parent_path . $serviceBill->apartment->name ?></span>
    </p>
    <p class="c22"><span class="c6">Nội dung thu tiền:</span></p><a id="t.208fbd4c21eddf70c18bc7641e954c7aec088547"></a><a
            id="t.1"></a>
    <table class="c17">
        <tbody>
        <tr class="c7">
            <td class="c2" colspan="1" rowspan="1"><p class="c4"><span class="c12">Dịch vụ</span></p></td>
            <td class="c2" colspan="1" rowspan="1"><p class="c4"><span class="c12">Đợt thanh toán</span></p></td>
            <td class="c2" colspan="1" rowspan="1"><p class="c4"><span class="c12">Phải thu</span></p></td>
            <td class="c2" colspan="1" rowspan="1"><p class="c4"><span class="c12">Thực thu</span></p></td>
            <td class="c2" colspan="1" rowspan="1"><p class="c4"><span class="c12">Diễn giải</span></p></td>
        </tr>
        <?php
        $total_price = 0;
        $i = 0;
        foreach ($serviceBillItems as $serviceBillItem) {
            $i++;
            $total_price += $serviceBillItem->price;
            ?>
            <tr class="c7">
                <td class="c2" colspan="1" rowspan="1"><p class="c3"><span
                                class="c6"><?= $serviceBillItem->serviceMapManagement->service_name; ?></span></p></td>
                <td class="c2" colspan="1" rowspan="1"><p class="c3"><span
                                class="c6"><?= date('m/Y', $serviceBillItem->fee_of_month) ?></span></p></td>
                <td class="c2" colspan="1" rowspan="1"><p class="c3"><span
                                class="c6"><?= CUtils::formatPrice($serviceBillItem->price) ?></span></p></td>
                <td class="c2" colspan="1" rowspan="1"><p class="c3"><span
                                class="c6"><?= CUtils::formatPrice($serviceBillItem->price) ?></span></p></td>
                <td class="c2" colspan="1" rowspan="1"><p class="c3"><span
                                class="c6" style="white-space:pre-wrap;"><?= $serviceBillItem->description ?></span></p></td>
            </tr>
        <?php } ?>
        <tr class="c19">
            <td class="c27" colspan="2" rowspan="1"><p class="c4"><span class="c12">Tổng</span></p></td>
            <td class="c2" colspan="1" rowspan="1"><p class="c3"><span
                            class="c12"><?= CUtils::formatPrice($total_price) ?></span></p></td>
            <td class="c2" colspan="1" rowspan="1"><p class="c3"><span
                            class="c12"><?= CUtils::formatPrice($total_price) ?></span></p></td>
            <td class="c2" colspan="1" rowspan="1"><p class="c3 c15"><span class="c6"></span></p></td>
        </tr>
        </tbody>
    </table>
    <p class="c22"><span
                class="c30">Đã nhận đủ số tiền (viết bằng chữ): <?= ucfirst(strtolower(CUtils::convert_number_to_words($total_price))) ?> VNĐ</span>
    </p><a id="t.1c5e5cdd991ccac04acafbf06cba6b81d56404bf"></a><a id="t.2"></a>
    <table class="c8">
        <tbody>
        <tr class="c1">
            <td class="c23" colspan="1" rowspan="1"><p class="c0"><span class="c12">Giám đốc</span></p>
                <p class="c0"><span class="c11">(Ký, họ tên, đóng dấu)</span></p></td>
            <td class="c20" colspan="1" rowspan="1"><p class="c0"><span class="c12">Kế toán trưởng</span></p>
                <p class="c0"><span class="c11">(Ký, họ tên)</span></p></td>
            <td class="c18" colspan="1" rowspan="1"><p class="c0"><span class="c12">Người nộp tiền</span></p>
                <p class="c0"><span class="c11">(Ký, họ tên)</span></p></td>
            <td class="c10" colspan="1" rowspan="1"><p class="c0"><span class="c12">Người lập phiếu</span></p>
                <p class="c0"><span class="c11">(Ký, họ tên)</span></p></td>
            <td class="c25" colspan="1" rowspan="1"><p class="c0"><span class="c12">Thủ quỹ</span></p>
                <p class="c0"><span class="c14">(Ký, họ tên)</span></p></td>
        </tr>
        </tbody>
    </table>
</div>