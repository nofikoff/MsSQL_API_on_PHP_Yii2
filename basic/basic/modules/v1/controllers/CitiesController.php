<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\db\Exception;
use yii\web\Controller;

class CitiesController extends Controller


{
    public function actionIndex()
    {
        try {
            //http://api.new-dating.com/v1/cities/index/
            $result = Yii::$app->db
                ->createCommand("SELECT * FROM City")
                ->queryAll();
        } catch (Exception $e) {
            // ошибка
            return json_encode($e);
        }
        // выкидываем последний элемент т.к. он служебный типа ВСЕГО - количество элементов в списке SQL результата
        // здесь не хранимая процедура а чистая таблица и сумм
        // array_pop($result);
        if (isset($result) and sizeof($result)) return json_encode($result);
        else return json_encode("No data");
    }

    public function actionView()
    {
        $id = $_REQUEST['id'];
        //http://api.new-dating.com/v1/cities/view/?id=AAQ
        $result = Yii::$app->db
            ->createCommand("SELECT * FROM City WHERE code = '$id'")
            ->queryAll();
        if (isset($result) and sizeof($result)) return json_encode($result);
        else return json_encode("No data");
    }

}
