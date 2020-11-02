<?php

namespace app\modules\v1\controllers;

use app\modules\v1\models\Mymodels;
use Yii;
use yii\db\Exception;
use yii\web\Controller;

class TnController extends Controller

{

    public function actionIndex()
    {
        try {
            //http://api.new-dating.com/v1/tn/index/
            $result = Yii::$app->db
                // SQLSTATE[IMSSP]: The active result for the query contains no fields. SET NOCOUNT ON;
                ->createCommand("SET NOCOUNT ON; EXEC al_CLIENT_WB_LIST_WEB " . Mymodels::getUserCACC())
                ->queryAll();
        } catch (Exception $e) {
            // ошибка
            return json_encode($e);
        }
        // выкидываем последний элемент т.к. он служебный типа ВСЕГО - количество элементов в списке SQL результата
        array_pop($result);
        if (isset($result) and sizeof($result)) return json_encode($result);
        else return json_encode("No data");

    }

    public function actionView()
    {
        $id = $_REQUEST['id'];
        try {
            //http://api.new-dating.com/v1/tn/view/?id=10-01683
            $result = Yii::$app->db
                // SQLSTATE[IMSSP]: The active result for the query contains no fields. SET NOCOUNT ON;
                ->createCommand("EXEC al_AGENT_INV_LOOK \"$id\"")
                ->queryAll();
        } catch (Exception $e) {
            // ошибка
            return json_encode($e);
        }
        // выкидываем последний элемент т.к. он служебный типа ВСЕГО - количество элементов в списке SQL результата
        //array_pop($result);
        if (isset($result) and sizeof($result)) return json_encode($result);
        else return json_encode("No data");

    }


    public function actionMyInfo()
    {
        $result = Mymodels::getCurrentUserInfo();
        if (isset($result) and sizeof($result)) return json_encode($result);
        else return json_encode("No data");

    }


}
