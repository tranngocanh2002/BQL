<?php
namespace backendQltt\models;

use common\models\AnnouncementTemplate;
use Yii;
use yii\base\Model;
use Exception;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use backendQltt\models\LoggerUser;
use backendQltt\models\LogBehavior;

class AnnouncementTemplateForm extends Model
{
    public $name;
    public $name_en;
    public $content_email;
    public $image;

    public function rules(): array
    {
        return [
            [['name', 'name_en', 'content_email'], 'required'],
            [['content_email', 'image'], 'string'],
            ['name', 'string', 'max' => 255],
            ['name_en', 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'name' => Yii::t('backendQltt', 'Tiêu đề'),
            'name_en' => Yii::t('backendQltt', 'Tiêu đề (EN)'),
            'content_email' => Yii::t('backendQltt', 'Nội dung'),
            'image' => Yii::t('backendQltt', 'Ảnh đại diện'),
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'log' => [
                'class' => LogBehavior::class,
            ],
        ];
    }
    public function store()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $model = new AnnouncementTemplate();
            $model->name = $this->name;
            $model->name_en = $this->name_en;
            $model->content_email = $this->content_email;
            $model->type = AnnouncementTemplate::TYPE_POST_NEWS;
            $model->image = $this->image;

            if ($model->save()) {
                $transaction->commit();

                return $model;
            }

            Yii::error($model->errors);
            $transaction->rollBack();

            return false;
        } catch (Exception $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage());
            
            return false;
        }
    }

    public function findModel($id)
    {
        if (($model = AnnouncementTemplate::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('frontend', 'The requested page does not exist.'));
    }
}
