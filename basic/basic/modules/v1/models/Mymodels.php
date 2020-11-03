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

    // парметр Vol_WT обьемный вес рассчитвается динамически из БД проекта запосом
    static function actionGetVol_WT()
    {

        $l = $_REQUEST['Vol_L'];
        $h = $_REQUEST['Vol_H'];
        $w = $_REQUEST['Vol_W'];
        $date = $_REQUEST['D_Acc'];
        $acc = $_REQUEST['ACC'];

        $query = "EXEC al_CLIENT_CountVol_Wt $l, $h, $w, '$date', '$acc'";
        try {
            $result = Yii::$app->db
                ->createCommand($query)
                ->queryOne();

        } catch (Exception $e) {
            print_r($e);
        }
        return $result['VolWt'];
    }


    // триф доставки из бд достаем
    static function actionCalc_T_Chg()
    {
        //[dbo].[al_Tariff_Calc_WEB] -----===========   20190417 For count tariff in client invoice
        //@ORG varchar(5),    -- код "откуда"
        //@DEST varchar(5),   -- код "куда"
        //@T_SRV varchar(2),  -- услуга
        //--@tariff dec(12,2)output, -- тариф
        //--@Deliv int output,         -- срок доставки
        //--@MT_Srv int output,     -- используется  при занесении ТН
        //--@Zone int output,         -- зона
        //@WT dec(12,2)=0.5,      --вес
        //@debug bit=0
        $ORG = $_REQUEST['ORG'];
        $DEST = $_REQUEST['DEST'];
        $ACC = $_REQUEST['ACC'];
        $SRV = $_REQUEST['T_SRV'];
        $WT = $_REQUEST['WT'];
        if ($_REQUEST['V_Car'] != '') $VCAR = $_REQUEST['V_Car']; else $VCAR = 0;
        if ($_REQUEST['V_Car'] != '') $RECFROMREC = $_REQUEST['ReceiveFromRec']; else $RECFROMREC = 0;

        $query = "EXEC al_Tariff_Calc2D_WEB2 '$ORG', '$DEST', '$SRV', $WT, '$ACC', $VCAR, $RECFROMREC";
        try {
            $result = Yii::$app->db
                ->createCommand($query)
                ->queryOne(); //
        } catch (Exception $e) {
            print_r($e);
        }
        return $result['Tariff'];
    }
}
