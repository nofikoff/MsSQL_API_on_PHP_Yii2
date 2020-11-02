<?php

namespace app\modules\v1\controllers;

use app\modules\v1\models\Mymodels;
use Yii;
use yii\db\Exception;
use yii\web\Controller;

class ContactsController extends Controller

{

    // список контактов
    public function actionIndex()
    {
        try {
            //http://api.new-dating.com/v1/contacts/index/
            $result = Yii::$app->db
                ->createCommand("EXEC al_CLIENT_CLIENT_LIST " . Mymodels::getUserCACC())
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


    public function actionView()
    {
        try {

            //http://api.new-dating.com/v1/contacts/view/
            $id = $_REQUEST['id'];
            $ACC = Mymodels::getUserCACC();
            // ПРООВЕРКА ПРАВ ДОСТУПА НИЖЕ В ОСНОВНОМ ЗАПРОСЕ
            //if (!Mymodels::checkAccessContact4DelUpd($ACC, $id)) return json_encode("No rules");

            $result = Yii::$app->db
                // ACC - добавил чтобы нельзя было увидеть чужие контакт не принадлежаещие пользователю
                // ACC - добавил чтобы нельзя было увидеть чужие контакт не принадлежаещие пользователю
                // ACC - добавил чтобы нельзя было увидеть чужие контакт не принадлежаещие пользователю
                ->createCommand("select ck.*,c.ruscnt from Client_Klient CK join Country C on c.CCode=CK.CountryCode where ClientId='$id' AND ACC = " . $ACC)
                ->queryOne();
        } catch (Exception $e) {
            // ошибка
            return json_encode($e);
        }
        // выкидываем последний элемент т.к. он служебный типа ВСЕГО - количество элементов в списке SQL результата
        //array_pop($result);
        if (isset($result)) return json_encode($result);
        else return json_encode("No data");
    }

    public function actionMarkDelete()
    {
        $id = $_REQUEST['id'];
        $ACC = Mymodels::getUserCACC();
        if (!Mymodels::checkAccessContact4DelUpd($ACC, $id)) return json_encode("No rules");

        $set = "UPDATE Client_Klient SET  
                      NeedDel = 1
                    where ClientId= " . $id;
        try {
            //http://api.new-dating.com/v1/contacts/update/
            Yii::$app->db
                ->createCommand($set)
                ->query();
        } catch (Exception $e) {
            // ошибка
            return json_encode($e);
        }
        // отобразим текущий контакт по _GET[id]
        return $this->actionView();

    }

    public function actionUpdate()
    {

        // защита от одинарных кавычек в запросе
        $_REQUEST = array_map(
            function ($item) {
                return str_replace("'", "`", $item);
            },
            $_REQUEST
        );


        $id = $_REQUEST['id'];
        $ACC = Mymodels::getUserCACC();
        if (!Mymodels::checkAccessContact4DelUpd($ACC, $id)) return json_encode("No rules");

        // возможно укр мову не пропускает!!!
        $collate = 'collate SQL_Ukrainian_CP1251_CI_AS';
        //запрещено менять город, ID назначения и страну здесь этих полне нет
        $set = "UPDATE Client_Klient SET  
                    C_Name=UPPER('" . $_REQUEST['C_Name'] . "' " . $collate . "),
                    C_CO=UPPER('" . $_REQUEST['C_CO'] . "' " . $collate . "),
                    C_Tel='" . $_REQUEST['C_Tel'] . "' " . $collate . ",
                    C_OBL=UPPER('" . $_REQUEST['C_OBL'] . "' " . $collate . "),
                    Type_UL='" . $_REQUEST['Type_UL'] . "' " . $collate . ", 
                    C_ZIP='" . $_REQUEST['C_ZIP'] . "' " . $collate . ",
                    C_RAJON=UPPER('" . $_REQUEST['C_RAJON'] . "' " . $collate . "),
                    C_Adr=UPPER('" . $_REQUEST['C_Adr'] . "' " . $collate . ")  
                    where ClientId= " . $id
            . "; ";

        try {
            //http://api.new-dating.com/v1/contacts/update/
            Yii::$app->db
                ->createCommand($set)
                ->query();
        } catch (Exception $e) {
            // ошибка
            return json_encode($e);
        }
        // отобразим текущий контакт по _GET[id]
        return $this->actionView();
    }


    public function actionCreate()
    {

        // защита от одинарных кавычек в запросе
        $_REQUEST = array_map(
            function ($item) {
                return str_replace("'", "`", $item);
            },
            $_REQUEST
        );


        $result = array();

        if (isset($_REQUEST['Loc'])) $Loc = $_REQUEST['Loc']; else $Loc = '';
        if (isset($_REQUEST['CountryCode'])) $CountryCode = $_REQUEST['CountryCode']; else $CountryCode = '';
        if (isset($_REQUEST['C_CITY'])) $C_CITY = $_REQUEST['C_CITY']; else $C_CITY = '';

        if (isset($_REQUEST['C_Name'])) $C_Name = $_REQUEST['C_Name']; else $C_Name = '';
        if (isset($_REQUEST['C_CO'])) $C_CO = $_REQUEST['C_CO']; else $C_CO = '';
        if (isset($_REQUEST['C_Tel'])) $C_Tel = $_REQUEST['C_Tel']; else $C_Tel = '';
        if (isset($_REQUEST['C_OBL'])) $C_OBL = $_REQUEST['C_OBL']; else $C_OBL = '';

        if (isset($_REQUEST['Type_UL'])) $Type_UL = $_REQUEST['Type_UL']; else $Type_UL = '';
        if (isset($_REQUEST['C_ZIP'])) $C_ZIP = $_REQUEST['C_ZIP']; else $C_ZIP = '';
        if (isset($_REQUEST['C_RAJON'])) $C_RAJON = $_REQUEST['C_RAJON']; else $C_RAJON = '';
        if (isset($_REQUEST['C_Adr'])) $C_Adr = $_REQUEST['C_Adr']; else $C_Adr = '';


        $result['error'] = '0';
        $ACC = Mymodels::getUserCACC();

        if ($Loc == '') {
            $result['error'] = 1;
            $result['description'] = 'Required $Loc field not specified';
            return json_encode($result);
        }

        if ($C_Name == '') {
            $result['error'] = 1;
            $result['description'] = 'Required $C_Name field not specified';
            return json_encode($result);
        }

        if ($C_Tel == '') {
            $result['error'] = 1;
            $result['description'] = 'Required $C_Tel field not specified';
            return json_encode($result);
        }

        if ($C_CO == '') {
            $result['error'] = 1;
            $result['description'] = 'Required $C_CO field not specified';
            return json_encode($result);
        }

        if ($C_Adr == '') {
            $result['error'] = 1;
            $result['description'] = 'Required $C_Adr field not specified';
            return json_encode($result);
        }

        if ($C_CITY == '') {
            $result['error'] = 1;
            $result['description'] = 'Required $C_CITY field not specified';
            return json_encode($result);
        }

        if ($C_OBL == '') {
            $result['error'] = 1;
            $result['description'] = 'Required $C_OBL field not specified';
            return json_encode($result);
        }

        if ($CountryCode == '') {
            $result['error'] = 1;
            $result['description'] = 'Required $CountryCode field not specified';
            return json_encode($result);
        }


        // проверяем дубли
        $select = "select COUNT(*) from Client_Klient where [ACC]='" . $ACC . "'
                       and [Loc]='" . $Loc . "'
                       and [C_CO]='" . $C_CO . "'
                       and [C_CITY]='" . $C_CITY . "'
                       and [C_Adr]='" . $C_Adr . "'
                       ";

        $rr = Yii::$app->db
            ->createCommand($select)
            ->queryScalar();

        if ($rr > 0) {
            $result['error'] = 1;
            $result['description'] = 'CONTACT ALREADY EXIST';
            return json_encode($result);
        }


        $insert = "INSERT
                INTO[Client_Klient]([ACC],[CACC],[Type_UL],[C_RAJON],[Loc],[C_Name],[C_Tel],[C_CO],[C_Adr],[C_ZIP],[C_CITY],[C_OBL],[CountryCode])
                VALUES('" . $ACC . "','XXX','" . $Type_UL . "','" . $C_RAJON . "','" . $Loc . "','" . $C_Name . "','" . $C_Tel . "','" . $C_CO . "','" . $C_Adr . "','" . $C_ZIP . "','" . $C_CITY . "','" . $C_OBL . "','" . $CountryCode . "')";

        Yii::$app->db
            ->createCommand($insert)
            ->query();

        $lastInsertID = Yii::$app->db->getLastInsertID();

        // отобразим текущий контакт по $_REQUEST[id]
        $_REQUEST['id'] = $lastInsertID;
        return $this->actionView();


    }


}
