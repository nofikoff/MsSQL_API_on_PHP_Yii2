<?php
namespace app\modules\v1\controllers;

use app\modules\v1\models\Mymodels;
use Yii;
use yii\db\Exception;
use yii\rest\ActiveController;
use yii\web\Controller;

class ContactsController extends Controller

{

    public function actionIndex()
    {
        try {
            //http://api.new-dating.com/v1/contacts/index/
            $result = Yii::$app->db
                ->createCommand("EXEC al_CLIENT_CLIENT_LIST ".Mymodels::getUserCACC())
                ->queryAll();
        } catch (Exception $e) {
            // ошибка
            return json_encode($e);
        }
        // выкидываем последний элемент т.к. он служебный типа ВСЕГО - количество элементов в списке SQL результата
        //array_pop($result);
        if (isset($result) AND sizeof($result)) return json_encode($result);
        else return json_encode("No data");
    }


}
