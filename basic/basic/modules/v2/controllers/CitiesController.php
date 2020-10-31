<?php

namespace app\modules\v2\controllers;


use Yii;
use app\models\Cities;
use app\models\CitiesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class CitiesController extends Controller
{
    public function actionIndex()
    {
        return "TEST API modele v2";
    }

}
