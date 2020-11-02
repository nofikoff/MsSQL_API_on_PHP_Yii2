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
                ->createCommand("SET NOCOUNT ON; EXEC al_CLIENT_getClientData '" . Mymodels::getUserCACC() . "'")
                ->queryAll();
        } catch (Exception $e) {
            // ошибка
            return json_encode($e);
        }
        return $result[0];
    }

    // чтобы сквозная нумерация была у поьзоваталеймсли я правильно понял
    static function getCurrentUserMaxTnNumber()
    {
        try {
            $result = Yii::$app->db
                // SQLSTATE[IMSSP]: The active result for the query contains no fields. - SET NOCOUNT ON;
                ->createCommand("SET NOCOUNT ON; EXEC al_CLIENT_GET_NUMBER3 '" . Mymodels::getUserCACC() . "'")
                ->queryAll();
        } catch (Exception $e) {
            // ошибка
            return json_encode($e);
        }
        return $result[0]['MaxNum'];
    }

    static function checkAccessContact4DelUpd($ACC, $contact_id)
    {
        $select = "select COUNT(*) from Client_Klient where [ACC]='" . $ACC . "' and [ClientId]='" . $contact_id . "'";
        $rr = Yii::$app->db
            ->createCommand($select)
            ->queryScalar();

        if ($rr) return true;
        return false;

    }


    static function checkAccessTn4DelUpd($ACC, $tn_id)
    {
        $select = "select COUNT(*) FROM [Client_Main] WHERE [ACC] ='" . $ACC . "' and [Wb_No] = '" . $tn_id . "'";
        $rr = Yii::$app->db
            ->createCommand($select)
            ->queryScalar();

        if ($rr) return true;
        return false;

    }

    
}
