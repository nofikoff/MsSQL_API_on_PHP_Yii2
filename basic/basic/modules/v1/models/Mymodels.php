<?php

namespace app\modules\v1\models;

use Yii;
use yii\base\Model;
use yii\db\Exception;


class Mymodels extends Model
{
    static function getUserCACC()
    {
        try {
            $result = Yii::$app->db
                // SQLSTATE[IMSSP]: The active result for the query contains no fields. - SET NOCOUNT ON;
                ->createCommand("SET NOCOUNT ON; EXEC al_CLIENT_getCACC " . Yii::$app->db->username)
                ->queryAll();
        } catch (Exception $e) {
            // ошибка
            return json_encode($e);
        }
        return $result[0]['CACC'];
    }

    static function getCurrentUserInfo()
    {
        try {
            $result = Yii::$app->db
                // SQLSTATE[IMSSP]: The active result for the query contains no fields. - SET NOCOUNT ON;
                ->createCommand("SET NOCOUNT ON; EXEC al_CLIENT_getClientData '" . Mymodels::getUserCACC()."'")
                ->queryAll();
        } catch (Exception $e) {
            // ошибка
            return json_encode($e);
        }
        return $result[0];
    }
}
