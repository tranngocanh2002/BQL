<?php

namespace backendQltt\controllers;

use Yii;
use common\models\AnnouncementCampaign;
use common\models\ResidentUser;
use common\models\ResidentUserNotify;
use backendQltt\models\AnnouncementCampaignSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\VarDumper;
use common\helpers\NotificationTemplate;
use common\models\ResidentNotifyReceiveConfig;
use common\models\User;
use DateTime;
use yii\base\Event;

/**
 * AnnouncementCampaignController implements the CRUD actions for AnnouncementCampaign model.
 */
class AnnouncementCampaignController extends BaseController
{

    /**
     * Lists all AnnouncementCampaign models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AnnouncementCampaignSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AnnouncementCampaign model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new AnnouncementCampaign model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $event = new Event(); 
        $model_user = new User(); 
        $model = new AnnouncementCampaign();
        if ($model->load(Yii::$app->request->post())) {
            $isTrash = Yii::$app->request->get('isTrash');

            if ($isTrash == 'true') {
                $model->status = AnnouncementCampaign::STATUS_UNACTIVE;
            }

            if (!empty($model->send_event_at)) {
                $datetime = DateTime::createFromFormat('H:i d/m/Y', $model->send_event_at);
                $timestamp = $datetime->getTimestamp();
                $model->send_event_at = $timestamp;
            }
            
            if (!empty($model->targets)) {
                $model->targets = json_encode($model->targets);
            }

            $newsEnCount = AnnouncementCampaign::find()->where([
                'type' => AnnouncementCampaign::TYPE_POST_NEW,
                'title_en' => $model->title_en,

            ])->count();

            if ($newsEnCount > 0) {
                Yii::$app->session->setFlash('error', Yii::t('backendQltt', 'Tiêu đề (En) đã tồn tại trên hệ thống.'));

                return $this->render('create', [
                    'model' => $model,
                ]);
            }

            $newsCount = AnnouncementCampaign::find()->where([
                'type' => AnnouncementCampaign::TYPE_POST_NEW,
                'title' => $model->title,

            ])->count();

            if ($newsCount > 0) {
                Yii::$app->session->setFlash('error', Yii::t('backendQltt', 'Tiêu đề  đã tồn tại trên hệ thống.'));

                return $this->render('create', [
                    'model' => $model,
                ]);
            }

            $model->type = AnnouncementCampaign::TYPE_POST_NEW;

            if (gettype($model->is_send_push) === 'array') {
                $model->is_send_push = (int) $model->is_send_push[0];
            }
            if(2 == $model->status && isset($timestamp) && $timestamp < (int)time())
            {
                Yii::$app->session->setFlash('error', Yii::t('backendQltt', 'Thời gian hẹn giờ không được nhỏ hơn thời gian hiện tại'));
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
            if (!$model->save()) {
                Yii::error($model->errors);
                Yii::$app->session->setFlash('error', Yii::t('backend', 'Error.'));

                return $this->redirect(['/announcement-campaign/create']);
            }
            $currentItem = AnnouncementCampaign::findOne([
                'type' => AnnouncementCampaign::TYPE_POST_NEW,
                'title_en' => $model->title_en,

            ]);
            // var_dump($currentItem->id);die();
            if($model->status != AnnouncementCampaign::STATUS_PUBLIC_AT && $model->status != AnnouncementCampaign::STATUS_UNACTIVE)
            {
                $model->sendNotifyToResidentUser($model->title,$model->title_en,json_decode($model->targets),$currentItem->id);
            }
            if (!empty($model->send_event_at) && 2 == $model->status) {
                // $datetime = DateTime::createFromFormat('H:i d/m/Y', $model->send_event_at);
                $datetime = strtotime($model->send_event_at);
                $save = $model->send_event_at ; 
                $timestamp = $datetime;
                $model->send_event_at = $timestamp;
                // $current_time = time();
                // if($model->send_event_at < $current_time){
                // Yii::$app->session->setFlash('error', Yii::t('backendQltt', 'Ngày công khai không được nhỏ hơn ngày thực tế'));
                    
                //     $model->send_event_at = $save ; 
                //     return $this->render('create', [
                //         'model' => $model,
                //     ]);
                // }
            }
            $model_user->trigger('afterInsert', $event);
            Yii::$app->session->setFlash('success', Yii::t('backendQltt', 'Tạo tin tức thành công.'));
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing AnnouncementCampaign model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $event = new Event(); 
        $model_user = new User(); 
        if ($model->load(Yii::$app->request->post())) {
            $isTrash = Yii::$app->request->get('isTrash');

            if ($isTrash == 'true') {
                $model->status = AnnouncementCampaign::STATUS_UNACTIVE;
            }

            if (gettype($model->is_send_push) === 'array') {
                $model->is_send_push = (int) $model->is_send_push[0];
            }

            if (!empty($model->send_event_at)) {
                $datetime = DateTime::createFromFormat('H:i d/m/Y', $model->send_event_at);
                $timestamp = $datetime->getTimestamp();
                $model->send_event_at = $timestamp;
            }

            if (!empty($model->targets)) {
                $model->targets = json_encode($model->targets);
            }

            if (!$model->save()) {
                Yii::error($model->errors);
                Yii::$app->session->setFlash('error', Yii::t('backend', 'Error.'));

                return $this->render('update', [
                    'model' => $model,
                ]);
            }
            $model_user->trigger('afterEdit', $event);

            Yii::$app->session->setFlash('success', Yii::t('backendQltt', 'Update news successfully'));

            if ($isTrash == 'false') {
                return $this->redirect(['index']);
            }

            return $this->redirect(['update', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing AnnouncementCampaign model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $news = $this->findModel($id);
        $event = new Event(); 
        $model_user = new User(); 
        if ($news->status == AnnouncementCampaign::STATUS_ACTIVE) {
            Yii::$app->session->setFlash('error', Yii::t('backendQltt', 'Bạn không thể xóa tin tức đã công khai'));

            return $this->redirect(['index']);
        }

        $news->delete();
        $model_user->trigger('afterDelete', $event);

        Yii::$app->session->setFlash('success', Yii::t('backendQltt', 'Xóa tin tức thành công'));

        return $this->redirect(['index']);
    }

    /**
     * Finds the AnnouncementCampaign model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AnnouncementCampaign the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AnnouncementCampaign::findOne(['id' => $id, 'type' => AnnouncementCampaign::TYPE_POST_NEW])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('backend', 'The requested page does not exist.'));
    }
}