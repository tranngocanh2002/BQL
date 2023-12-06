<?php

namespace backendQltt\controllers;

use common\models\BuildingArea;
use common\models\BuildingCluster;
use common\models\rbac\AuthItem;
use backendQltt\models\ManagementUserForm;
use common\models\rbac\AuthGroup;
use Yii;
use backendQltt\models\BuildingClusterSearch;
use common\models\ManagementUser;
use common\models\User;
use yii\base\Event;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * BuildingClusterController implements the CRUD actions for BuildingCluster model.
 */
class BuildingClusterController extends BaseController
{
    /**
     * @inheritdoc
     */

    /**
     * Lists all BuildingCluster models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BuildingClusterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single BuildingCluster model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
            'managementUserModel' => ManagementUserForm::findModelByBuildingClusterId($id),
        ]);
    }

    /**
     * Creates a new BuildingCluster model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $event = new Event(); 
        $model_user = new User(); 
        $model = new BuildingCluster();
        $managementUserModel = new ManagementUserForm();

        $bill_template_old = '{"style_string":"\n .jsx-parser ol {\n margin: 0;\n padding: 0\n }\n \n .jsx-parser table td,\n table th {\n padding: 0\n }\n \n .jsx-parser .c7c20{\n min-width: 30%;\n color: #000000;\n font-weight: 700;\n text-decoration: none;\n vertical-align: baseline;\n font-size: 10pt;\n font-family: \"Times New Roman\";\n font-style: normal;\n text-align: right;\n }\n .jsx-parser .c7 {\n border-right-style: solid;\n padding-top: 6pt;\n border-top-width: 0pt;\n border-bottom-color: null;\n border-right-width: 0pt;\n padding-left: 0pt;\n border-left-color: null;\n padding-bottom: 6pt;\n line-height: 1.0;\n border-right-color: null;\n border-left-width: 0pt;\n border-top-style: solid;\n background-color: #ffffff;\n border-left-style: solid;\n border-bottom-width: 0pt;\n border-top-color: null;\n border-bottom-style: solid;\n orphans: 2;\n widows: 2;\n padding-right: 0pt;\n display: flex;\n justify-content: space-between;\n align-items: center;\n }\n \n .jsx-parser .c9 {\n border-right-style: solid;\n padding-top: 6pt;\n border-top-width: 0pt;\n border-bottom-color: null;\n border-right-width: 0pt;\n padding-left: 0pt;\n border-left-color: null;\n padding-bottom: 2pt;\n line-height: 1.0;\n border-right-color: null;\n border-left-width: 0pt;\n border-top-style: solid;\n border-left-style: solid;\n border-bottom-width: 0pt;\n border-top-color: null;\n border-bottom-style: solid;\n orphans: 2;\n widows: 2;\n text-align: center;\n padding-right: 0pt\n }\n .jsx-parser .c9999 {\n border-right-style: solid;\n padding-top: 1pt;\n border-top-width: 0pt;\n border-bottom-color: null;\n border-right-width: 0pt;\n padding-left: 0pt;\n border-left-color: null;\n padding-bottom: 1pt;\n line-height: 1.0;\n border-right-color: null;\n border-left-width: 0pt;\n border-top-style: solid;\n border-left-style: solid;\n border-bottom-width: 0pt;\n border-top-color: null;\n border-bottom-style: solid;\n orphans: 2;\n widows: 2;\n text-align: center;\n padding-right: 0pt\n }\n \n .jsx-parser .c3 {\n border-right-style: solid;\n padding: 2pt 2pt 2pt 2pt;\n border-bottom-color: #dddddd;\n border-top-width: 0pt;\n border-right-width: 0pt;\n border-left-color: #dddddd;\n vertical-align: top;\n border-right-color: #dddddd;\n border-left-width: 0pt;\n border-top-style: solid;\n background-color: #ffffff;\n border-left-style: solid;\n border-bottom-width: 0pt;\n width: 114.8pt;\n border-top-color: #dddddd;\n border-bottom-style: solid\n }\n \n .jsx-parser .c26 {\n border-right-style: solid;\n padding: 2pt 2pt 2pt 2pt;\n border-bottom-color: #dddddd;\n border-top-width: 0pt;\n border-right-width: 0pt;\n border-left-color: #dddddd;\n vertical-align: top;\n border-right-color: #dddddd;\n border-left-width: 0pt;\n border-top-style: solid;\n background-color: #ffffff;\n border-left-style: solid;\n border-bottom-width: 0pt;\n width: 86.2pt;\n border-top-color: #dddddd;\n border-bottom-style: solid\n }\n \n .jsx-parser .c27 {\n border-right-style: solid;\n padding: 2pt 2pt 2pt 2pt;\n border-bottom-color: #dddddd;\n border-top-width: 0pt;\n border-right-width: 0pt;\n border-left-color: #dddddd;\n vertical-align: top;\n border-right-color: #dddddd;\n border-left-width: 0pt;\n border-top-style: solid;\n background-color: #ffffff;\n border-left-style: solid;\n border-bottom-width: 0pt;\n width: 91.5pt;\n border-top-color: #dddddd;\n border-bottom-style: solid\n }\n \n .jsx-parser .c15 {\n border-right-style: solid;\n padding: 2pt 2pt 2pt 2pt;\n border-bottom-color: #dddddd;\n border-top-width: 0pt;\n border-right-width: 0pt;\n border-left-color: #dddddd;\n vertical-align: top;\n border-right-color: #dddddd;\n border-left-width: 0pt;\n border-top-style: solid;\n background-color: #ffffff;\n border-left-style: solid;\n border-bottom-width: 0pt;\n width: 68.2pt;\n border-top-color: #dddddd;\n border-bottom-style: solid\n }\n \n .jsx-parser .c6 {\n border-right-style: solid;\n padding: 5pt 5pt 5pt 5pt;\n border-bottom-color: #666666;\n border-top-width: 1pt;\n border-right-width: 1pt;\n border-left-color: #666666;\n vertical-align: top;\n border-right-color: #666666;\n border-left-width: 1pt;\n border-top-style: solid;\n border-left-style: solid;\n border-bottom-width: 1pt;\n width: 330pt;\n border-top-color: #666666;\n border-bottom-style: solid\n }\n \n .jsx-parser .c29 {\n border-right-style: solid;\n padding: 5pt 5pt 5pt 5pt;\n border-bottom-color: #000000;\n border-top-width: 0pt;\n border-right-width: 0pt;\n border-left-color: #000000;\n vertical-align: top;\n border-right-color: #000000;\n border-left-width: 0pt;\n border-top-style: solid;\n border-left-style: solid;\n border-bottom-width: 0pt;\n width: 243pt;\n border-top-color: #000000;\n border-bottom-style: solid\n }\n \n .jsx-parser .c30 {\n border-right-style: solid;\n padding: 2pt 2pt 2pt 2pt;\n border-bottom-color: #dddddd;\n border-top-width: 0pt;\n border-right-width: 0pt;\n border-left-color: #dddddd;\n vertical-align: top;\n border-right-color: #dddddd;\n border-left-width: 0pt;\n border-top-style: solid;\n border-left-style: solid;\n border-bottom-width: 0pt;\n width: 99pt;\n border-top-color: #dddddd;\n border-bottom-style: solid\n }\n \n .jsx-parser .c19 {\n border-right-style: solid;\n padding: 5pt 5pt 5pt 5pt;\n border-bottom-color: #000000;\n border-top-width: 0pt;\n border-right-width: 0pt;\n border-left-color: #000000;\n vertical-align: top;\n border-right-color: #000000;\n border-left-width: 0pt;\n border-top-style: solid;\n border-left-style: solid;\n border-bottom-width: 0pt;\n width: 255pt;\n border-top-color: #000000;\n border-bottom-style: solid\n }\n \n .jsx-parser .c14 {\n border-right-style: solid;\n padding: 5pt 5pt 5pt 5pt;\n border-bottom-color: #666666;\n border-top-width: 1pt;\n border-right-width: 1pt;\n border-left-color: #666666;\n vertical-align: top;\n border-right-color: #666666;\n border-left-width: 1pt;\n border-top-style: solid;\n border-left-style: solid;\n border-bottom-width: 1pt;\n width: 150pt;\n border-top-color: #666666;\n border-bottom-style: solid\n }\n \n .jsx-parser .c1 {\n color: #000000;\n font-weight: 400;\n text-decoration: none;\n vertical-align: baseline;\n font-size: 10pt;\n font-family: \"Times New Roman\";\n font-style: normal\n }\n \n .jsx-parser .c25 {\n color: #000000;\n font-weight: 400;\n text-decoration: none;\n vertical-align: baseline;\n font-size: 9pt;\n font-family: \"Times New Roman\";\n font-style: normal\n }\n \n .jsx-parser .c20 {\n color: #000000;\n font-weight: 700;\n text-decoration: none;\n vertical-align: baseline;\n font-size: 10pt;\n font-family: \"Times New Roman\";\n font-style: normal\n }\n \n .jsx-parser .c2 {\n color: #000000;\n font-weight: 700;\n text-decoration: none;\n vertical-align: baseline;\n font-size: 10.5pt;\n font-family: \"Times New Roman\";\n font-style: normal\n }\n \n .jsx-parser .c16 {\n color: #000000;\n font-weight: 400;\n text-decoration: none;\n vertical-align: baseline;\n font-size: 10.5pt;\n font-family: \"Times New Roman\";\n font-style: italic\n }\n \n .jsx-parser .c22 {\n color: #000000;\n font-weight: 700;\n text-decoration: none;\n vertical-align: baseline;\n font-size: 9pt;\n font-family: \"Times New Roman\";\n font-style: normal\n }\n \n .jsx-parser .c10 {\n color: #000000;\n font-weight: 400;\n text-decoration: none;\n vertical-align: baseline;\n font-size: 11pt;\n font-family: \"Arial\";\n font-style: normal\n }\n \n .jsx-parser .c17 {\n padding-top: 0pt;\n padding-bottom: 0pt;\n line-height: 1.15;\n orphans: 2;\n widows: 2;\n text-align: left\n }\n \n .jsx-parser .c5 {\n padding-top: 0pt;\n padding-bottom: 0pt;\n line-height: 1.0;\n text-align: center\n }\n \n .jsx-parser .c0 {\n padding-top: 0pt;\n padding-bottom: 0pt;\n line-height: 1.0;\n text-align: left\n }\n \n .jsx-parser .c24 {\n width:100%;\n border-spacing: 0;\n border-collapse: collapse;\n }\n \n .jsx-parser .c18 {\n width:100%;\n border-spacing: 0;\n border-collapse: collapse;\n }\n \n .jsx-parser .c23 {\n width:100%;\n border-spacing: 0;\n border-collapse: collapse;\n }\n \n .jsx-parser .c13 {\n font-size: 16pt;\n font-family: \"Times New Roman\";\n font-weight: 700;\n }\n \n .jsx-parser .c8 {\n background-color: #ffffff;\n padding: 0pt 8pt 0pt 8pt\n }\n \n .jsx-parser .c11 {\n background-color: #ffffff;\n height: 11pt\n }\n \n .jsx-parser .c21 {\n height: 11pt\n }\n \n .jsx-parser .c4 {\n height: 24pt\n }\n \n .jsx-parser .c28 {\n background-color: #ffffff\n }\n \n .jsx-parser .c12 {\n height: 0pt\n }\n \n .jsx-parser .title {\n padding-top: 0pt;\n color: #000000;\n font-size: 26pt;\n padding-bottom: 3pt;\n font-family: \"Arial\";\n line-height: 1.15;\n page-break-after: avoid;\n orphans: 2;\n widows: 2;\n text-align: left\n }\n \n .jsx-parser .subtitle {\n padding-top: 0pt;\n color: #666666;\n font-size: 15pt;\n padding-bottom: 16pt;\n font-family: \"Arial\";\n line-height: 1.15;\n page-break-after: avoid;\n orphans: 2;\n widows: 2;\n text-align: left\n }\n \n .jsx-parser li {\n color: #000000;\n font-size: 11pt;\n font-family: \"Arial\"\n }\n \n .jsx-parser p {\n margin: 0;\n color: #000000;\n font-size: 11pt;\n font-family: \"Arial\"\n }\n \n .jsx-parser h1 {\n padding-top: 20pt;\n color: #000000;\n font-size: 20pt;\n padding-bottom: 6pt;\n font-family: \"Arial\";\n line-height: 1.15;\n page-break-after: avoid;\n orphans: 2;\n widows: 2;\n text-align: left\n }\n \n .jsx-parser h2 {\n padding-top: 18pt;\n color: #000000;\n font-size: 16pt;\n padding-bottom: 6pt;\n font-family: \"Arial\";\n line-height: 1.15;\n page-break-after: avoid;\n orphans: 2;\n widows: 2;\n text-align: left\n }\n \n .jsx-parser h3 {\n padding-top: 16pt;\n color: #434343;\n font-size: 14pt;\n padding-bottom: 4pt;\n font-family: \"Arial\";\n line-height: 1.15;\n page-break-after: avoid;\n orphans: 2;\n widows: 2;\n text-align: left\n }\n \n .jsx-parser h4 {\n padding-top: 14pt;\n color: #666666;\n font-size: 12pt;\n padding-bottom: 4pt;\n font-family: \"Arial\";\n line-height: 1.15;\n page-break-after: avoid;\n orphans: 2;\n widows: 2;\n text-align: left\n }\n \n .jsx-parser h5 {\n padding-top: 12pt;\n color: #666666;\n font-size: 11pt;\n padding-bottom: 4pt;\n font-family: \"Arial\";\n line-height: 1.15;\n page-break-after: avoid;\n orphans: 2;\n widows: 2;\n text-align: left\n }\n \n .jsx-parser h6 {\n padding-top: 12pt;\n color: #666666;\n font-size: 11pt;\n padding-bottom: 4pt;\n font-family: \"Arial\";\n line-height: 1.15;\n page-break-after: avoid;\n font-style: italic;\n orphans: 2;\n widows: 2;\n text-align: left\n }\n }","jsx":"<div class=\"c8\">\r\n <p class=\"c17 c21\"><span class=\"c10\"></span></p><a id=\"t.596889bd2267afc7ef97a3091f18b552ea14f1a8\"></a><a\r\n id=\"t.0\"></a>\r\n <table class=\"c24\">\r\n <tbody>\r\n <tr class=\"c12\">\r\n <td class=\"c29\" colspan=\"1\" rowspan=\"1\">\r\n <p class=\"c17\"><span class=\"c22\">Đơn vị: Luci Building</span></p>\r\n <p class=\"c17\"><span class=\"c25\">Bộ phận:.........................................</span></p>\r\n </td>\r\n <td class=\"c19\" colspan=\"1\" rowspan=\"1\">\r\n <p class=\"c5\"><span class=\"c22\">Mẫu số: 01-TT</span></p>\r\n <p class=\"c5\"><span class=\"c25\">(Ban hành theo quyết định số: 48/2006/QĐ - BTC Ngày 14/9/2006 của bộ trưởng BTC)</span></p>\r\n </td>\r\n </tr>\r\n </tbody>\r\n </table>\r\n <p class=\"c7\"><span class=\"c7c20\"></span><span class=\"c13\">PHIẾU THU</span><span class=\"c7c20\">Số: {number}</span></p>\r\n <p class=\"c9 c28\"><span class=\"c2\">{execution_date}</span></p>\r\n <p class=\"c9999 c28\"><span class=\"c2\">{bill_name}</span></p>\r\n <p class=\"c9 c11\"><span class=\"c2\"></span></p><a id=\"t.e573e07dec41c32b04b625135152558b5f9d025b\"></a><a\r\n id=\"t.1\"></a>\r\n <table class=\"c23\">\r\n <tbody>\r\n <tr class=\"c12\">\r\n <td class=\"c14\" colspan=\"1\" rowspan=\"1\">\r\n <p class=\"c0\"><span class=\"c1\">Họ và tên người nộp:</span></p>\r\n </td>\r\n <td class=\"c6\" colspan=\"1\" rowspan=\"1\">\r\n <p class=\"c0\"><span class=\"c1\">{payer_name}</span></p>\r\n </td>\r\n </tr>\r\n <tr class=\"c12\">\r\n <td class=\"c14\" colspan=\"1\" rowspan=\"1\">\r\n <p class=\"c0\"><span class=\"c1\">Căn hộ:</span></p>\r\n </td>\r\n <td class=\"c6\" colspan=\"1\" rowspan=\"1\">\r\n <p class=\"c0\"><span class=\"c1\">{apartment_name}</span></p>\r\n </td>\r\n </tr>\r\n <tr class=\"c12\">\r\n <td class=\"c14\" colspan=\"1\" rowspan=\"1\">\r\n <p class=\"c0\"><span class=\"c1\">Lý do thu:</span></p>\r\n </td>\r\n <td class=\"c6\" colspan=\"1\" rowspan=\"1\">\r\n {fees}\r\n </td>\r\n </tr>\r\n <tr class=\"c12\">\r\n <td class=\"c14\" colspan=\"1\" rowspan=\"1\">\r\n <p class=\"c0\"><span class=\"c1\">Số tiền:</span></p>\r\n </td>\r\n <td class=\"c6\" colspan=\"1\" rowspan=\"1\">\r\n <p class=\"c0\"><span class=\"c20\">{total_new_money_collected} VND</span></p>\r\n </td>\r\n </tr>\r\n <tr class=\"c12\">\r\n <td class=\"c14\" colspan=\"1\" rowspan=\"1\">\r\n <p class=\"c0\"><span class=\"c1\">Viết bằng chữ:</span></p>\r\n </td>\r\n <td class=\"c6\" colspan=\"1\" rowspan=\"1\">\r\n <p class=\"c0\"><span class=\"c20\">{total_new_money_collected_string}</span></p>\r\n </td>\r\n </tr>\r\n <tr class=\"c12\">\r\n <td class=\"c14\" colspan=\"1\" rowspan=\"1\">\r\n <p class=\"c0\"><span class=\"c1\">Kèm theo:.................................</span></p>\r\n </td>\r\n <td class=\"c6\" colspan=\"1\" rowspan=\"1\">\r\n <p class=\"c0\"><span class=\"c1\">Chứng từ gốc.</span></p>\r\n </td>\r\n </tr>\r\n </tbody>\r\n </table>\r\n <p class=\"c17 c21\"><span class=\"c10\"></span></p><a id=\"t.a888a99cd7e3fadfab7e4e8e6ed1ccd37f91d10f\"></a><a\r\n id=\"t.2\"></a>\r\n <table class=\"c18\">\r\n <tbody>\r\n <tr class=\"c4\">\r\n <td class=\"c3\" colspan=\"1\" rowspan=\"1\">\r\n <p class=\"c9\"><span class=\"c2\">Giám đốc</span></p>\r\n <p class=\"c9\"><span class=\"c16\">(Ký, họ tên, đóng dấu)</span>\r\n </p>\r\n </td>\r\n <td class=\"c28 c30\" colspan=\"1\" rowspan=\"1\">\r\n <p class=\"c9\"><span class=\"c2\">Trưởng ban quản lý</span></p>\r\n <p class=\"c9\"><span class=\"c16\">(Ký, họ tên)</span></p>\r\n </td>\r\n <td class=\"c15\" colspan=\"1\" rowspan=\"1\">\r\n <p class=\"c9\"><span class=\"c2\">Kế toán</span></p>\r\n <p class=\"c9\"><span class=\"c16\">(Ký, họ tên)</span></p>\r\n </td>\r\n <td class=\"c27\" colspan=\"1\" rowspan=\"1\">\r\n <p class=\"c9\"><span class=\"c2\">Người nộp tiền</span></p>\r\n <p class=\"c9\"><span class=\"c16\">(Ký, họ tên)</span></p>\r\n </td>\r\n <td class=\"c26\" colspan=\"1\" rowspan=\"1\">\r\n <p class=\"c9\"><span class=\"c2\">Người lập phiếu</span></p>\r\n <p class=\"c9\"><span class=\"c16\">(Ký, họ tên)</span></p>\r\n </td>\r\n </tr>\r\n </tbody>\r\n </table>\r\n <p class=\"c17 c21\"><span class=\"c10\"></span></p>\r\n</div>","jsx_row":"\n <p class=\"c0\"><span class=\"c1\">Thu {rr.service_map_management_service_name} {rr.fee_of_month}: {rr.new_money_collected} &#273;&#7891;ng</span></p>\n "}';
        $bill_invoice_template_old = '{"style_string":"\n .jsx-parser ol {\n margin: 0;\n padding: 0\n }\n \n .jsx-parser table td,\n table th {\n padding: 0\n }\n \n .jsx-parser .c7c20{\n min-width: 30%;\n color: #000000;\n font-weight: 700;\n text-decoration: none;\n vertical-align: baseline;\n font-size: 10pt;\n font-family: \"Times New Roman\";\n font-style: normal;\n text-align: right;\n }\n .jsx-parser .c7 {\n border-right-style: solid;\n padding-top: 6pt;\n border-top-width: 0pt;\n border-bottom-color: null;\n border-right-width: 0pt;\n padding-left: 0pt;\n border-left-color: null;\n padding-bottom: 6pt;\n line-height: 1.0;\n border-right-color: null;\n border-left-width: 0pt;\n border-top-style: solid;\n background-color: #ffffff;\n border-left-style: solid;\n border-bottom-width: 0pt;\n border-top-color: null;\n border-bottom-style: solid;\n orphans: 2;\n widows: 2;\n padding-right: 0pt;\n display: flex;\n justify-content: space-between;\n align-items: center;\n }\n \n .jsx-parser .c9 {\n border-right-style: solid;\n padding-top: 6pt;\n border-top-width: 0pt;\n border-bottom-color: null;\n border-right-width: 0pt;\n padding-left: 0pt;\n border-left-color: null;\n padding-bottom: 2pt;\n line-height: 1.0;\n border-right-color: null;\n border-left-width: 0pt;\n border-top-style: solid;\n border-left-style: solid;\n border-bottom-width: 0pt;\n border-top-color: null;\n border-bottom-style: solid;\n orphans: 2;\n widows: 2;\n text-align: center;\n padding-right: 0pt\n }\n .jsx-parser .c9999 {\n border-right-style: solid;\n padding-top: 1pt;\n border-top-width: 0pt;\n border-bottom-color: null;\n border-right-width: 0pt;\n padding-left: 0pt;\n border-left-color: null;\n padding-bottom: 1pt;\n line-height: 1.0;\n border-right-color: null;\n border-left-width: 0pt;\n border-top-style: solid;\n border-left-style: solid;\n border-bottom-width: 0pt;\n border-top-color: null;\n border-bottom-style: solid;\n orphans: 2;\n widows: 2;\n text-align: center;\n padding-right: 0pt\n }\n \n .jsx-parser .c3 {\n border-right-style: solid;\n padding: 2pt 2pt 2pt 2pt;\n border-bottom-color: #dddddd;\n border-top-width: 0pt;\n border-right-width: 0pt;\n border-left-color: #dddddd;\n vertical-align: top;\n border-right-color: #dddddd;\n border-left-width: 0pt;\n border-top-style: solid;\n background-color: #ffffff;\n border-left-style: solid;\n border-bottom-width: 0pt;\n width: 114.8pt;\n border-top-color: #dddddd;\n border-bottom-style: solid\n }\n \n .jsx-parser .c26 {\n border-right-style: solid;\n padding: 2pt 2pt 2pt 2pt;\n border-bottom-color: #dddddd;\n border-top-width: 0pt;\n border-right-width: 0pt;\n border-left-color: #dddddd;\n vertical-align: top;\n border-right-color: #dddddd;\n border-left-width: 0pt;\n border-top-style: solid;\n background-color: #ffffff;\n border-left-style: solid;\n border-bottom-width: 0pt;\n width: 86.2pt;\n border-top-color: #dddddd;\n border-bottom-style: solid\n }\n \n .jsx-parser .c27 {\n border-right-style: solid;\n padding: 2pt 2pt 2pt 2pt;\n border-bottom-color: #dddddd;\n border-top-width: 0pt;\n border-right-width: 0pt;\n border-left-color: #dddddd;\n vertical-align: top;\n border-right-color: #dddddd;\n border-left-width: 0pt;\n border-top-style: solid;\n background-color: #ffffff;\n border-left-style: solid;\n border-bottom-width: 0pt;\n width: 91.5pt;\n border-top-color: #dddddd;\n border-bottom-style: solid\n }\n \n .jsx-parser .c15 {\n border-right-style: solid;\n padding: 2pt 2pt 2pt 2pt;\n border-bottom-color: #dddddd;\n border-top-width: 0pt;\n border-right-width: 0pt;\n border-left-color: #dddddd;\n vertical-align: top;\n border-right-color: #dddddd;\n border-left-width: 0pt;\n border-top-style: solid;\n background-color: #ffffff;\n border-left-style: solid;\n border-bottom-width: 0pt;\n width: 68.2pt;\n border-top-color: #dddddd;\n border-bottom-style: solid\n }\n \n .jsx-parser .c6 {\n border-right-style: solid;\n padding: 5pt 5pt 5pt 5pt;\n border-bottom-color: #666666;\n border-top-width: 1pt;\n border-right-width: 1pt;\n border-left-color: #666666;\n vertical-align: top;\n border-right-color: #666666;\n border-left-width: 1pt;\n border-top-style: solid;\n border-left-style: solid;\n border-bottom-width: 1pt;\n width: 330pt;\n border-top-color: #666666;\n border-bottom-style: solid\n }\n \n .jsx-parser .c29 {\n border-right-style: solid;\n padding: 5pt 5pt 5pt 5pt;\n border-bottom-color: #000000;\n border-top-width: 0pt;\n border-right-width: 0pt;\n border-left-color: #000000;\n vertical-align: top;\n border-right-color: #000000;\n border-left-width: 0pt;\n border-top-style: solid;\n border-left-style: solid;\n border-bottom-width: 0pt;\n width: 243pt;\n border-top-color: #000000;\n border-bottom-style: solid\n }\n \n .jsx-parser .c30 {\n border-right-style: solid;\n padding: 2pt 2pt 2pt 2pt;\n border-bottom-color: #dddddd;\n border-top-width: 0pt;\n border-right-width: 0pt;\n border-left-color: #dddddd;\n vertical-align: top;\n border-right-color: #dddddd;\n border-left-width: 0pt;\n border-top-style: solid;\n border-left-style: solid;\n border-bottom-width: 0pt;\n width: 99pt;\n border-top-color: #dddddd;\n border-bottom-style: solid\n }\n \n .jsx-parser .c19 {\n border-right-style: solid;\n padding: 5pt 5pt 5pt 5pt;\n border-bottom-color: #000000;\n border-top-width: 0pt;\n border-right-width: 0pt;\n border-left-color: #000000;\n vertical-align: top;\n border-right-color: #000000;\n border-left-width: 0pt;\n border-top-style: solid;\n border-left-style: solid;\n border-bottom-width: 0pt;\n width: 255pt;\n border-top-color: #000000;\n border-bottom-style: solid\n }\n \n .jsx-parser .c14 {\n border-right-style: solid;\n padding: 5pt 5pt 5pt 5pt;\n border-bottom-color: #666666;\n border-top-width: 1pt;\n border-right-width: 1pt;\n border-left-color: #666666;\n vertical-align: top;\n border-right-color: #666666;\n border-left-width: 1pt;\n border-top-style: solid;\n border-left-style: solid;\n border-bottom-width: 1pt;\n width: 150pt;\n border-top-color: #666666;\n border-bottom-style: solid\n }\n \n .jsx-parser .c1 {\n color: #000000;\n font-weight: 400;\n text-decoration: none;\n vertical-align: baseline;\n font-size: 10pt;\n font-family: \"Times New Roman\";\n font-style: normal\n }\n \n .jsx-parser .c25 {\n color: #000000;\n font-weight: 400;\n text-decoration: none;\n vertical-align: baseline;\n font-size: 9pt;\n font-family: \"Times New Roman\";\n font-style: normal\n }\n \n .jsx-parser .c20 {\n color: #000000;\n font-weight: 700;\n text-decoration: none;\n vertical-align: baseline;\n font-size: 10pt;\n font-family: \"Times New Roman\";\n font-style: normal\n }\n \n .jsx-parser .c2 {\n color: #000000;\n font-weight: 700;\n text-decoration: none;\n vertical-align: baseline;\n font-size: 10.5pt;\n font-family: \"Times New Roman\";\n font-style: normal\n }\n \n .jsx-parser .c16 {\n color: #000000;\n font-weight: 400;\n text-decoration: none;\n vertical-align: baseline;\n font-size: 10.5pt;\n font-family: \"Times New Roman\";\n font-style: italic\n }\n \n .jsx-parser .c22 {\n color: #000000;\n font-weight: 700;\n text-decoration: none;\n vertical-align: baseline;\n font-size: 9pt;\n font-family: \"Times New Roman\";\n font-style: normal\n }\n \n .jsx-parser .c10 {\n color: #000000;\n font-weight: 400;\n text-decoration: none;\n vertical-align: baseline;\n font-size: 11pt;\n font-family: \"Arial\";\n font-style: normal\n }\n \n .jsx-parser .c17 {\n padding-top: 0pt;\n padding-bottom: 0pt;\n line-height: 1.15;\n orphans: 2;\n widows: 2;\n text-align: left\n }\n \n .jsx-parser .c5 {\n padding-top: 0pt;\n padding-bottom: 0pt;\n line-height: 1.0;\n text-align: center\n }\n \n .jsx-parser .c0 {\n padding-top: 0pt;\n padding-bottom: 0pt;\n line-height: 1.0;\n text-align: left\n }\n \n .jsx-parser .c24 {\n width:100%;\n border-spacing: 0;\n border-collapse: collapse;\n }\n \n .jsx-parser .c18 {\n width:100%;\n border-spacing: 0;\n border-collapse: collapse;\n }\n \n .jsx-parser .c23 {\n width:100%;\n border-spacing: 0;\n border-collapse: collapse;\n }\n \n .jsx-parser .c13 {\n font-size: 16pt;\n font-family: \"Times New Roman\";\n font-weight: 700;\n }\n \n .jsx-parser .c8 {\n background-color: #ffffff;\n padding: 0pt 8pt 0pt 8pt\n }\n \n .jsx-parser .c11 {\n background-color: #ffffff;\n height: 11pt\n }\n \n .jsx-parser .c21 {\n height: 11pt\n }\n \n .jsx-parser .c4 {\n height: 24pt\n }\n \n .jsx-parser .c28 {\n background-color: #ffffff\n }\n \n .jsx-parser .c12 {\n height: 0pt\n }\n \n .jsx-parser .title {\n padding-top: 0pt;\n color: #000000;\n font-size: 26pt;\n padding-bottom: 3pt;\n font-family: \"Arial\";\n line-height: 1.15;\n page-break-after: avoid;\n orphans: 2;\n widows: 2;\n text-align: left\n }\n \n .jsx-parser .subtitle {\n padding-top: 0pt;\n color: #666666;\n font-size: 15pt;\n padding-bottom: 16pt;\n font-family: \"Arial\";\n line-height: 1.15;\n page-break-after: avoid;\n orphans: 2;\n widows: 2;\n text-align: left\n }\n \n .jsx-parser li {\n color: #000000;\n font-size: 11pt;\n font-family: \"Arial\"\n }\n \n .jsx-parser p {\n margin: 0;\n color: #000000;\n font-size: 11pt;\n font-family: \"Arial\"\n }\n \n .jsx-parser h1 {\n padding-top: 20pt;\n color: #000000;\n font-size: 20pt;\n padding-bottom: 6pt;\n font-family: \"Arial\";\n line-height: 1.15;\n page-break-after: avoid;\n orphans: 2;\n widows: 2;\n text-align: left\n }\n \n .jsx-parser h2 {\n padding-top: 18pt;\n color: #000000;\n font-size: 16pt;\n padding-bottom: 6pt;\n font-family: \"Arial\";\n line-height: 1.15;\n page-break-after: avoid;\n orphans: 2;\n widows: 2;\n text-align: left\n }\n \n .jsx-parser h3 {\n padding-top: 16pt;\n color: #434343;\n font-size: 14pt;\n padding-bottom: 4pt;\n font-family: \"Arial\";\n line-height: 1.15;\n page-break-after: avoid;\n orphans: 2;\n widows: 2;\n text-align: left\n }\n \n .jsx-parser h4 {\n padding-top: 14pt;\n color: #666666;\n font-size: 12pt;\n padding-bottom: 4pt;\n font-family: \"Arial\";\n line-height: 1.15;\n page-break-after: avoid;\n orphans: 2;\n widows: 2;\n text-align: left\n }\n \n .jsx-parser h5 {\n padding-top: 12pt;\n color: #666666;\n font-size: 11pt;\n padding-bottom: 4pt;\n font-family: \"Arial\";\n line-height: 1.15;\n page-break-after: avoid;\n orphans: 2;\n widows: 2;\n text-align: left\n }\n \n .jsx-parser h6 {\n padding-top: 12pt;\n color: #666666;\n font-size: 11pt;\n padding-bottom: 4pt;\n font-family: \"Arial\";\n line-height: 1.15;\n page-break-after: avoid;\n font-style: italic;\n orphans: 2;\n widows: 2;\n text-align: left\n }\n }","jsx":"<div class=\"c8\">\r\n <p class=\"c17 c21\"><span class=\"c10\"></span></p><a id=\"t.596889bd2267afc7ef97a3091f18b552ea14f1a8\"></a><a\r\n id=\"t.0\"></a>\r\n <table class=\"c24\">\r\n <tbody>\r\n <tr class=\"c12\">\r\n <td class=\"c29\" colspan=\"1\" rowspan=\"1\">\r\n <p class=\"c17\"><span class=\"c22\">Đơn vị: Luci Building</span></p>\r\n <p class=\"c17\"><span class=\"c25\">Bộ phận:.........................................</span></p>\r\n </td>\r\n <td class=\"c19\" colspan=\"1\" rowspan=\"1\">\r\n <p class=\"c5\"><span class=\"c22\">Mẫu số: 01-TT</span></p>\r\n <p class=\"c5\"><span class=\"c25\">(Ban hành theo quyết định số: 48/2006/QĐ - BTC Ngày 14/9/2006 của bộ trưởng BTC)</span></p>\r\n </td>\r\n </tr>\r\n </tbody>\r\n </table>\r\n <p class=\"c7\"><span class=\"c7c20\"></span><span class=\"c13\">PHIẾU CHI</span><span class=\"c7c20\">Số: {number}</span></p>\r\n <p class=\"c9 c28\"><span class=\"c2\">{execution_date}</span></p>\r\n <p class=\"c9999 c28\"><span class=\"c2\">{bill_name}</span></p>\r\n <p class=\"c9 c11\"><span class=\"c2\"></span></p><a id=\"t.e573e07dec41c32b04b625135152558b5f9d025b\"></a><a\r\n id=\"t.1\"></a>\r\n <table class=\"c23\">\r\n <tbody>\r\n <tr class=\"c12\">\r\n <td class=\"c14\" colspan=\"1\" rowspan=\"1\">\r\n <p class=\"c0\"><span class=\"c1\">Họ và tên người nhận:</span></p>\r\n </td>\r\n <td class=\"c6\" colspan=\"1\" rowspan=\"1\">\r\n <p class=\"c0\"><span class=\"c1\">{payer_name}</span></p>\r\n </td>\r\n </tr>\r\n <tr class=\"c12\">\r\n <td class=\"c14\" colspan=\"1\" rowspan=\"1\">\r\n <p class=\"c0\"><span class=\"c1\">Căn hộ:</span></p>\r\n </td>\r\n <td class=\"c6\" colspan=\"1\" rowspan=\"1\">\r\n <p class=\"c0\"><span class=\"c1\">{apartment_name}</span></p>\r\n </td>\r\n </tr>\r\n <tr class=\"c12\">\r\n <td class=\"c14\" colspan=\"1\" rowspan=\"1\">\r\n <p class=\"c0\"><span class=\"c1\">Lý do chi:</span></p>\r\n </td>\r\n <td class=\"c6\" colspan=\"1\" rowspan=\"1\">\r\n {fees}\r\n </td>\r\n </tr>\r\n <tr class=\"c12\">\r\n <td class=\"c14\" colspan=\"1\" rowspan=\"1\">\r\n <p class=\"c0\"><span class=\"c1\">Số tiền:</span></p>\r\n </td>\r\n <td class=\"c6\" colspan=\"1\" rowspan=\"1\">\r\n <p class=\"c0\"><span class=\"c20\">{total_new_money_collected} VND</span></p>\r\n </td>\r\n </tr>\r\n <tr class=\"c12\">\r\n <td class=\"c14\" colspan=\"1\" rowspan=\"1\">\r\n <p class=\"c0\"><span class=\"c1\">Viết bằng chữ:</span></p>\r\n </td>\r\n <td class=\"c6\" colspan=\"1\" rowspan=\"1\">\r\n <p class=\"c0\"><span class=\"c20\">{total_new_money_collected_string}</span></p>\r\n </td>\r\n </tr>\r\n <tr class=\"c12\">\r\n <td class=\"c14\" colspan=\"1\" rowspan=\"1\">\r\n <p class=\"c0\"><span class=\"c1\">Kèm theo:.................................</span></p>\r\n </td>\r\n <td class=\"c6\" colspan=\"1\" rowspan=\"1\">\r\n <p class=\"c0\"><span class=\"c1\">Chứng từ gốc.</span></p>\r\n </td>\r\n </tr>\r\n </tbody>\r\n </table>\r\n <p class=\"c17 c21\"><span class=\"c10\"></span></p><a id=\"t.a888a99cd7e3fadfab7e4e8e6ed1ccd37f91d10f\"></a><a\r\n id=\"t.2\"></a>\r\n <table class=\"c18\">\r\n <tbody>\r\n <tr class=\"c4\">\r\n <td class=\"c3\" colspan=\"1\" rowspan=\"1\">\r\n <p class=\"c9\"><span class=\"c2\">Giám đốc</span></p>\r\n <p class=\"c9\"><span class=\"c16\">(Ký, họ tên, đóng dấu)</span>\r\n </p>\r\n </td>\r\n <td class=\"c28 c30\" colspan=\"1\" rowspan=\"1\">\r\n <p class=\"c9\"><span class=\"c2\">Trưởng ban quản lý</span></p>\r\n <p class=\"c9\"><span class=\"c16\">(Ký, họ tên)</span></p>\r\n </td>\r\n <td class=\"c15\" colspan=\"1\" rowspan=\"1\">\r\n <p class=\"c9\"><span class=\"c2\">Kế toán</span></p>\r\n <p class=\"c9\"><span class=\"c16\">(Ký, họ tên)</span></p>\r\n </td>\r\n <td class=\"c27\" colspan=\"1\" rowspan=\"1\">\r\n <p class=\"c9\"><span class=\"c2\">Người nhận tiền</span></p>\r\n <p class=\"c9\"><span class=\"c16\">(Ký, họ tên)</span></p>\r\n </td>\r\n <td class=\"c26\" colspan=\"1\" rowspan=\"1\">\r\n <p class=\"c9\"><span class=\"c2\">Người lập phiếu</span></p>\r\n <p class=\"c9\"><span class=\"c16\">(Ký, họ tên)</span></p>\r\n </td>\r\n </tr>\r\n </tbody>\r\n </table>\r\n <p class=\"c17 c21\"><span class=\"c10\"></span></p>\r\n</div>","jsx_row":"\n <p class=\"c0\"><span class=\"c1\">Thu {rr.service_map_management_service_name} {rr.fee_of_month}: {rr.new_money_collected} &#273;&#7891;ng</span></p>\n "}';
        $model->service_bill_template = $bill_template_old;
        $model->service_bill_invoice_template = $bill_invoice_template_old;
        $allTagRoles = AuthItem::find()->where(['type' => AuthItem::TYPE_ROLE])->groupBy('tag')->all();

        $transaction = Yii::$app->db->beginTransaction();

        if ($model->load(Yii::$app->request->post()) && $managementUserModel->load(Yii::$app->request->post())) {
            $buildingCluster = BuildingCluster::find()->where([
                'name' => $model->name,
                'is_deleted' => 0
            ])->one();

            if ($buildingCluster) {
                Yii::$app->session->setFlash('error', Yii::t('backendQltt', 'Tên dự án đã tồn tại trong hệ thống'));
                $transaction->rollBack();
                return $this->render('create', [
                    'model' => $model,
                    'allTagRoles' => $allTagRoles,
                    'authItemTags' => [],
                    'managementUserModel' => $managementUserModel,
                ]);
            }
            $checkDomain = BuildingCluster::find()->where([
                'domain' => $model->domain,
                'is_deleted' => 0
            ])->one();

            if ($checkDomain) {
                Yii::$app->session->setFlash('error', Yii::t('backendQltt', 'Domain đã tồn tại trong hệ thống'));
                $transaction->rollBack();
                return $this->render('create', [
                    'model' => $model,
                    'allTagRoles' => $allTagRoles,
                    'authItemTags' => [],
                    'managementUserModel' => $managementUserModel,
                ]);
            }

            $auth_item_tags = Yii::$app->request->post('auth_item_tags');

            if (!empty($auth_item_tags) && is_array($auth_item_tags)) {
                $model->auth_item_tags = json_encode($auth_item_tags);
            }

            if (!empty($model->service_bill_template)) {
                if (!empty($bill_template_old)) {
                    $templateBuilds = Json::decode($bill_template_old, true);
                    $templateBuilds['jsx'] = $model->service_bill_template;
                    $model->service_bill_template = Json::encode($templateBuilds);
                }
            }

            if (!empty($model->service_bill_invoice_template)) {
                if (!empty($bill_invoice_template_old)) {
                    $templateInvoiceBuilds = Json::decode($bill_invoice_template_old, true);
                    $templateInvoiceBuilds['jsx'] = $model->service_bill_invoice_template;
                    $model->service_bill_invoice_template = Json::encode($templateInvoiceBuilds);
                }
            }
            // var_dump($model->medias.'/'.$model->avatar);die();
            $avatar = [];
            $avatar['avatarUrl'] = $model->avatar;
            $avatar['imageUrl']  = $model->avatar;
            $avatarJson = json_encode($avatar);
            $model->medias = $avatarJson;
            $model->email = $managementUserModel->email;
            if ($model->save()) {
                $board = $model->setDefaultAuthGroup();
                $managementUserModel->building_cluster_id = $model->id;
                $managementUserModel->auth_group_id = $board->id;
                if (!$managementUserModel->store()) {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', Yii::t('backend', 'Create BuildingCluster Error'));
                    return $this->render('create', [
                        'model' => $model,
                        'allTagRoles' => $allTagRoles,
                        'authItemTags' => [],
                        'managementUserModel' => $managementUserModel,
                    ]);
                }
                $model_user->trigger('afterInsert', $event);
                Yii::$app->session->setFlash('message', Yii::t('backendQltt', 'Tạo mới dự án thành công'));
                $transaction->commit();
                return $this->redirect(['index']);
            } else {
                Yii::error($model->errors);
                Yii::error($managementUserModel->errors);
                if (!array_key_exists('domain', $model->errors)) {
                    Yii::$app->session->setFlash('error', Yii::t('backend', 'Create BuildingCluster Error'));
                }
                $transaction->rollBack();

                return $this->render('create', [
                    'model' => $model,
                    'allTagRoles' => $allTagRoles,
                    'authItemTags' => [],
                    'managementUserModel' => $managementUserModel,
                ]);
            }
        } else {
            if (!empty($model->service_bill_template)) {
                $templateBuilds = Json::decode($model->service_bill_template, true);
                $template = '';
                if ($templateBuilds['jsx']) {
                    $template .= $templateBuilds['jsx'];
                }
                $model->service_bill_template = $template;
            }
            if (!empty($model->service_bill_invoice_template)) {
                $templateInvoiceBuilds = Json::decode($model->service_bill_invoice_template, true);
                $templateInvoice = '';
                if ($templateInvoiceBuilds['jsx']) {
                    $templateInvoice .= $templateInvoiceBuilds['jsx'];
                }
                $model->service_bill_invoice_template = $templateInvoice;
            }

            return $this->render('create', [
                'model' => $model,
                'allTagRoles' => $allTagRoles,
                'authItemTags' => [],
                'managementUserModel' => $managementUserModel,
            ]);
        }
    }

    /**
     * Updates an existing BuildingCluster model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $event = new Event(); 
        $model_user = new User(); 
        $model = $this->findModel($id);
        $managementUserModel = new ManagementUserForm();
        $managementUser = ManagementUserForm::findModelByBuildingClusterId($id);
        $managementUserModel->email = $managementUser->email;
        $isUpdateAccountAdmin = Yii::$app->getRequest()->getQueryParam('update-admin');
        $transaction = Yii::$app->db->beginTransaction();

//        $bill_template_old = $model->service_bill_template;
//        $bill_template_invoice_old = $model->service_bill_invoice_template;
        $allTagRoles = AuthItem::find()->where(['type' => AuthItem::TYPE_ROLE])->groupBy('tag')->all();
        if ($model->load(Yii::$app->request->post()) || $managementUserModel->load(Yii::$app->request->post())) {
            // $auth_item_tags = Yii::$app->request->post('auth_item_tags');
            // if(!empty($auth_item_tags) && is_array($auth_item_tags)){
            //     $model->auth_item_tags = json_encode($auth_item_tags);
            // }

            // if(!empty($model->service_bill_template)){
            //     if(!empty($bill_template_old)){
            //         $templateBuilds = Json::decode($bill_template_old, true);
            //         $templateBuilds['jsx'] = $model->service_bill_template;
            //         $model->service_bill_template = Json::encode($templateBuilds);
            //     }
            // }
            // if(!empty($model->service_bill_invoice_template)){
            //     if(!empty($bill_template_invoice_old)){
            //         $templateInoviceBuilds = Json::decode($bill_template_invoice_old, true);
            //         $templateInoviceBuilds['jsx'] = $model->service_bill_invoice_template;
            //         $model->service_bill_invoice_template = Json::encode($templateInoviceBuilds);
            //     }
            // }
            // var_dump($model->avatar);die();
            $avatar = [];
            if(!empty($model->medias))
            {
                $avatar = json_decode($model->medias,true);
            }
            $avatar['avatarUrl'] = $model->avatar;
            $avatar['imageUrl'] = $model->avatar;
            $avatarJson = json_encode($avatar);
            $model->medias = $avatarJson;
            $buildingCluster = BuildingCluster::find()->where([
                'name' => $model->name,
                'is_deleted' => 0
            ])
            ->andWhere(['<>', 'id', $id])
            ->one();

            if ($buildingCluster) {
                Yii::$app->session->setFlash('error', Yii::t('backendQltt', 'Tên dự án đã tồn tại trong hệ thống'));
                $transaction->rollBack();
                return $this->render('create', [
                    'model' => $model,
                    'allTagRoles' => $allTagRoles,
                    'authItemTags' => [],
                    'managementUserModel' => $managementUserModel,
                ]);
            }

            if ($isUpdateAccountAdmin) {
                $emailBuildingcluster = Yii::$app->request->post('ManagementUserForm')['email'] ?? $managementUser->email;
                if (!$managementUserModel->updateRecord($model->id,$emailBuildingcluster)) {
                    $transaction->rollBack();
                    Yii::error($model->errors);
                    Yii::$app->session->setFlash('error', Yii::t('backend', 'Update BuildingCluster Error'));
                    $authItemTags = !empty($model->auth_item_tags) ? json_decode($model->auth_item_tags) : [];
                    return $this->render('update', [
                        'model' => $model,
                        'allTagRoles' => $allTagRoles,
                        'authItemTags' => $authItemTags,
                        'managementUserModel' => $managementUserModel,
                    ]);
                }

                Yii::$app->session->setFlash('message', Yii::t('backendQltt', ' Cập nhật tài khoản thành công'));

                $transaction->commit();

                return $this->redirect(['view', 'id' => $model->id]);
            } elseif ($model->save()) {
                $model_user->trigger('afterEdit', $event);
                Yii::$app->session->setFlash('message', Yii::t('backendQltt', ' Cập nhật dự án thành công'));

                $transaction->commit();

                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                Yii::error($model->errors);
                Yii::$app->session->setFlash('error', Yii::t('backend', 'Update BuildingCluster Error'));
                $authItemTags = !empty($model->auth_item_tags) ? json_decode($model->auth_item_tags) : [];
                $transaction->rollBack();
                return $this->render('update', [
                    'model' => $model,
                    'allTagRoles' => $allTagRoles,
                    'authItemTags' => $authItemTags,
                    'managementUserModel' => $managementUserModel,
                ]);
            }
        } else {
            $authItemTags = !empty($model->auth_item_tags) ? json_decode($model->auth_item_tags) : [];
            if (!empty($model->service_bill_template)) {
                $templateBuilds = Json::decode($model->service_bill_template, true);
                $template = '';
                if ($templateBuilds['jsx']) {
                    $template .= $templateBuilds['jsx'];
                }
                $model->service_bill_template = $template;
            }

            if (!empty($model->service_bill_invoice_template)) {
                $templateInvoiceBuilds = Json::decode($model->service_bill_invoice_template, true);
                $templateInvoice = '';
                if ($templateInvoiceBuilds['jsx']) {
                    $templateInvoice .= $templateInvoiceBuilds['jsx'];
                }
                $model->service_bill_invoice_template = $templateInvoice;
            }
            return $this->render('update', [
                'model' => $model,
                'allTagRoles' => $allTagRoles,
                'authItemTags' => $authItemTags,
                'managementUserModel' => $managementUserModel,
            ]);
        }
    }

    /**
     * Deletes an existing BuildingCluster model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $event = new Event(); 
        $model_user = new User(); 
        $buildingArea = BuildingArea::findOne(['building_cluster_id' => $id, 'is_deleted' => BuildingArea::NOT_DELETED]);
        if (!empty($buildingArea)) {
            Yii::$app->session->setFlash('error', Yii::t('backend', 'Building Cluster is being used'));
            return $this->redirect(['index']);
        }
        $model = $this->findModel($id);
        $model->is_deleted = BuildingCluster::DELETED;
        $model->save();
        $model_user->trigger('afterDelete', $event);
        Yii::$app->session->setFlash('message', Yii::t('backendQltt', 'Xóa dự án thành công'));
        return $this->redirect(['index']);
    }

    /**
     * Finds the BuildingCluster model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return BuildingCluster the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BuildingCluster::find()->where([ 'id'=> $id ,'is_deleted' => BuildingCluster::NOT_DELETED])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionToBql($id)
    {
        $managementUser = ManagementUser::findOne(['role_type' => ManagementUser::SUPPER_ADMIN, 'building_cluster_id' => $id]);
        if ($managementUser) {
            $resTokenLogin = $managementUser->setTokenLogin();
            if ($resTokenLogin['success']) {
                $jwtRefresh = $resTokenLogin['jwtRefresh'];
                $url = $managementUser->buildingCluster->domain . '/login_by_token?token=' . $jwtRefresh;
                Yii::$app->response->redirect($url)->send();
                Yii::$app->end();
                exit(0);
            }
        }
        Yii::$app->session->setFlash('error', Yii::t('backend', 'Building Cluster is being used'));
        return $this->redirect(['view', 'id' => $id]);
    }
}
