<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\db\Exception;
use yii\web\Controller;

class SiteController extends Controller


{
    public function actionIndex()
    {
        return "<h1>API интерфейс компании Добра Доставка</h1>За документацией и доступом обратитесь в наш отдел продаж";
    }

}
