<?php

namespace app\modules\v1\controllers;

use app\modules\v1\models\Mymodels;
use Yii;
use yii\db\Exception;
use yii\web\Controller;

class TnController extends Controller

{

    // накладные не принятные новые
    public function actionWait()
    {

        try {
            //http://api.new-dating.com/v1/tn/wait/
            $result = Yii::$app->db
                // SQLSTATE[IMSSP]: The active result for the query contains no fields. SET NOCOUNT ON;
                ->createCommand("EXEC al_CLIENT_WB_LIST_CLIENT " . Mymodels::getUserCACC() . " ")
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

    // прнятые в работе
    public function actionWork()
    {

        try {
            //http://api.new-dating.com/v1/tn/work/
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
        $ACC = Mymodels::getUserCACC();
        // ПРООВЕРКА ПРАВ ДОСТУПА НИЖЕ В ОСНОВНОМ ЗАПРОСЕ
        //if (!Mymodels::checkAccessTn4DelUpd($ACC, $id)) return json_encode("No rules");

        try {
            //http://api.new-dating.com/v1/tn/view/?id=10-01683
            $result = Yii::$app->db
                // SQLSTATE[IMSSP]: The active result for the query contains no fields. SET NOCOUNT ON;
                //->createCommand("EXEC al_AGENT_INV_LOOK \"$id\"") -- этот запрос не показывает новые накладные
                // защита SCode - показывать только накладные данного клиента
                // защита SCode - показывать только накладные данного клиента
                // защита SCode - показывать только накладные данного клиента
                ->createCommand("SELECT * FROM [Client_Main] WHERE Wb_No = '$id' AND SCode = " . $ACC)
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

    public function actionMarkDelete()
    {
        $id = $_REQUEST['id'];
        $ACC = Mymodels::getUserCACC();
        if (!Mymodels::checkAccessTn4DelUpd($ACC, $id)) return json_encode("No rules");

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

    public function actionCreate()
    {

        // защита от одинарных кавычек в запросе
        $_REQUEST = array_map(
            function ($item) {
                return str_replace("'", "`", $item);
            },
            $_REQUEST
        );

        if ($_REQUEST['D_Acc'] == '') {
            $result['error'] = 1;
            $result['description'] = 'Required D_Acc field not specified';
            return json_encode($result);
        }
        if ($_REQUEST['DEST'] == '') {
            $result['error'] = 1;
            $result['description'] = 'Required DEST field not specified';
            return json_encode($result);
        }
        if ($_REQUEST['S_CITY'] == '') {
            $result['error'] = 1;
            $result['description'] = 'Required C_CITY field not specified';
            return json_encode($result);
        }
        if ($_REQUEST['S_Co'] == '') {
            $result['error'] = 1;
            $result['description'] = 'Required S_Co field not specified';
            return json_encode($result);
        }
        if ($_REQUEST['S_Adr'] == '') {
            $result['error'] = 1;
            $result['description'] = 'Required S_Adr field not specified';
            return json_encode($result);
        }
        if ($_REQUEST['R_Co'] == '') {
            $result['error'] = 1;
            $result['description'] = 'Required R_Co field not specified';
            return json_encode($result);
        }
        if ($_REQUEST['R_Adr'] == '') {
            $result['error'] = 1;
            $result['description'] = 'Required R_Adr field not specified';
            return json_encode($result);
        }


        // на всякий случай все параметрц  $_REQUEST
        // и обязательные тоже проверим вдруг в будущем перстанут быть обязательнями
        // инициализация $_REQUEST

        $_REQUEST['D_Acc'] = isset($_REQUEST['D_Acc']) ? $_REQUEST['D_Acc'] : date('Ymd');
        $_REQUEST['ORG'] = isset($_REQUEST['ORG']) ? $_REQUEST['ORG'] : '';
        $_REQUEST['DEST'] = isset($_REQUEST['DEST']) ? $_REQUEST['DEST'] : '';
        $_REQUEST['S_Name'] = isset($_REQUEST['S_Name']) ? $_REQUEST['S_Name'] : '';
        $_REQUEST['S_Tel'] = isset($_REQUEST['S_Tel']) ? $_REQUEST['S_Tel'] : '';
        $_REQUEST['S_Co'] = isset($_REQUEST['S_Co']) ? $_REQUEST['S_Co'] : '';
        $_REQUEST['S_Cnt'] = isset($_REQUEST['S_Cnt']) ? $_REQUEST['S_Cnt'] : '';

        if ($_REQUEST['S_Cnt'] === 'UA') {
            $_REQUEST['S_CountryCode'] = 'УКРАЇНА';
        } else {
            $_REQUEST['S_CountryCode'] = isset($_REQUEST['S_CountryCode']) ? $_REQUEST['S_CountryCode'] : '';
        }

        $_REQUEST['S_CITY'] = isset($_REQUEST['S_CITY']) ? $_REQUEST['S_CITY'] : '';
        $_REQUEST['S_Obl'] = isset($_REQUEST['S_Obl']) ? $_REQUEST['S_Obl'] : '';
        $_REQUEST['S_Adr'] = isset($_REQUEST['S_Adr']) ? $_REQUEST['S_Adr'] : '';
        $_REQUEST['S_Zip'] = isset($_REQUEST['S_Zip']) ? $_REQUEST['S_Zip'] : '';
        $_REQUEST['S_Ref'] = isset($_REQUEST['S_Ref']) ? $_REQUEST['S_Ref'] : '';
        $_REQUEST['Rcode'] = isset($_REQUEST['Rcode']) ? $_REQUEST['Rcode'] : '';
        $_REQUEST['R_Name'] = isset($_REQUEST['R_Name']) ? $_REQUEST['R_Name'] : '';
        $_REQUEST['R_Tel'] = isset($_REQUEST['R_Tel']) ? $_REQUEST['R_Tel'] : '';
        $_REQUEST['R_Co'] = isset($_REQUEST['R_Co']) ? $_REQUEST['R_Co'] : '';
        $_REQUEST['R_CountryCode'] = isset($_REQUEST['R_CountryCode']) ? $_REQUEST['R_CountryCode'] : '';
        $_REQUEST['R_Obl'] = isset($_REQUEST['R_Obl']) ? $_REQUEST['R_Obl'] : '';

        $_REQUEST['R_CITY'] = isset($_REQUEST['R_CITY']) ? $_REQUEST['R_CITY'] : '';
        if ($_REQUEST['R_CITY'] === 'КИЕВ') $_REQUEST['R_CITY'] = 'КИЇВ';
        if ($_REQUEST['R_CITY'] === 'Киев') $_REQUEST['R_CITY'] = 'КИЇВ';

        $_REQUEST['R_Adr'] = isset($_REQUEST['R_Adr']) ? $_REQUEST['R_Adr'] : '';
        $_REQUEST['R_Rajon'] = isset($_REQUEST['R_Rajon']) ? $_REQUEST['R_Rajon'] : '';
        $_REQUEST['R_Zip'] = isset($_REQUEST['R_Zip']) ? $_REQUEST['R_Zip'] : '';
        $_REQUEST['MetPaym'] = isset($_REQUEST['MetPaym']) ? $_REQUEST['MetPaym'] : '';
        $_REQUEST['Payr'] = isset($_REQUEST['Payr']) ? $_REQUEST['Payr'] : 1;
        $_REQUEST['Payer'] = isset($_REQUEST['Payer']) ? $_REQUEST['Payer'] : '';
        $_REQUEST['P_Adr'] = isset($_REQUEST['P_Adr']) ? $_REQUEST['P_Adr'] : '';
        $_REQUEST['T_SRV'] = isset($_REQUEST['T_SRV']) ? $_REQUEST['T_SRV'] : 'EC';
        $_REQUEST['T_PAK'] = isset($_REQUEST['T_PAK']) ? $_REQUEST['T_PAK'] : '';
        $_REQUEST['T_DEL'] = isset($_REQUEST['T_DEL']) ? $_REQUEST['T_DEL'] : '';
        $_REQUEST['PCS'] = isset($_REQUEST['PCS']) ? $_REQUEST['PCS'] : 1;
        $_REQUEST['WT'] = isset($_REQUEST['WT']) ? $_REQUEST['WT'] : 0;
        $_REQUEST['T_Chg'] = isset($_REQUEST['T_Chg']) ? $_REQUEST['T_Chg'] : 0;
        $_REQUEST['Vol_H'] = isset($_REQUEST['Vol_H']) ? $_REQUEST['Vol_H'] : 0;
        $_REQUEST['Vol_L'] = isset($_REQUEST['Vol_L']) ? $_REQUEST['Vol_L'] : 0;
        $_REQUEST['Vol_W'] = isset($_REQUEST['Vol_W']) ? $_REQUEST['Vol_W'] : 0;
        $_REQUEST['Vol_WT'] = isset($_REQUEST['Vol_WT']) ? $_REQUEST['Vol_WT'] : 0;
        $_REQUEST['V_Car'] = isset($_REQUEST['V_Car']) ? $_REQUEST['V_Car'] : 0;
        $_REQUEST['Descr'] = isset($_REQUEST['Descr']) ? $_REQUEST['Descr'] : '';
        $_REQUEST['DTD'] = isset($_REQUEST['DTD']) ? $_REQUEST['DTD'] : '';
        $_REQUEST['Pack_Other'] = isset($_REQUEST['Pack_Other']) ? $_REQUEST['Pack_Other'] : '';
        $_REQUEST['dop_NotWork'] = isset($_REQUEST['dop_NotWork']) ? $_REQUEST['dop_NotWork'] : 0;
        $_REQUEST['ReceiveFromRec'] = isset($_REQUEST['ReceiveFromRec']) ? $_REQUEST['ReceiveFromRec'] : 0;
        $_REQUEST['dop_NotWork_date'] = isset($_REQUEST['dop_NotWork_date']) ? $_REQUEST['dop_NotWork_date'] : null;
        $_REQUEST['dop_NotWork_time'] = isset($_REQUEST['dop_NotWork_time']) ? $_REQUEST['dop_NotWork_time'] : '';
        // поле в базе ограничено 15 знакми
        $_REQUEST['Pack_Other'] = isset($_REQUEST['Pack_Other']) ? mb_substr($_REQUEST['Pack_Other'], 0, 15) : '';


        //    print_r($_REQUEST);
        $mssql_query = "INSERT INTO [Client_Main]
            ([Wb_No]
            ,[D_Acc]
            ,[ORG]
            ,[DEST]
            ,[SCode]
            ,[S_Name]
            ,[S_Tel]
            ,[S_Co]
            ,[S_Cnt]
            ,[S_CountryCode]            
            ,[S_CITY]
            ,[S_Obl]
            ,[S_Adr]
            ,[S_Zip]
            ,[S_Ref]
            ,[Rcode]
            ,[R_Name]
            ,[R_Tel]
            ,[R_Co]
            ,[R_CountryCode]
            ,[R_Obl]
            ,[R_CITY]
            ,[R_Adr]
            ,[R_Rajon]
            ,[R_Zip]
            ,[MetPaym]
            ,[Payr]
            ,[Payer]
            ,[P_Adr]
            ,[ACC]
            ,[T_SRV]
            ,[T_PAK]
            ,[T_DEL]
            ,[PCS]
            ,[WT]
            ,[T_Chg]
            ,[Vol_H]
            ,[Vol_L]
            ,[Vol_W]
            ,[Vol_WT]
            ,[V_Car]          
            ,[Descr]        
            ,[DTD]
            ,[dop_NotWork]
            
            ,[ReceiveFromRec]
            ,[dop_NotWork_date]
            ,[dop_NotWork_time]             
            ,[Pack_Other]             
            )
            
            VALUES  
            ('" . Mymodels::getCurrentUserMaxTnNumber() . "'
            ,'" . $_REQUEST['D_Acc'] . "' 
            ,'" . $_REQUEST['ORG'] . "'
            ,'" . $_REQUEST['DEST'] . "'
            ,'" . Mymodels::getUserCACC() . "'
            
            
            
            ,UPPER ('" . $_REQUEST['S_Name'] . "')
            ,'" . $_REQUEST['S_Tel'] . "'
            ,'" . $_REQUEST['S_Co'] . "'           
            ,'" . $_REQUEST['S_Cnt'] . "'     
            ,'" . $_REQUEST['S_CountryCode'] . "'      
            ,UPPER('" . $_REQUEST['S_CITY'] . "') 
            ,UPPER('" . $_REQUEST['S_Obl'] . "')
            ,UPPER('" . $_REQUEST['S_Adr'] . "')
            ,'" . $_REQUEST['S_Zip'] . "'
            ,'" . $_REQUEST['S_Ref'] . "'
            
            
            ,'" . $_REQUEST['Rcode'] . "'
            ,UPPER('" . $_REQUEST['R_Name'] . "')
            ,'" . $_REQUEST['R_Tel'] . "'
            ,'" . $_REQUEST['R_Co'] . "'
            ,'" . $_REQUEST['R_CountryCode'] . "'
            ,UPPER('" . $_REQUEST['R_Obl'] . "')
            ,UPPER('" . $_REQUEST['R_CITY'] . "')
            ,UPPER('" . $_REQUEST['R_Adr'] . "')
            ,UPPER('" . $_REQUEST['R_Rajon'] . "')
            ,'" . $_REQUEST['R_Zip'] . "'
            
            
            ,'" . $_REQUEST['MetPaym'] . "'
            ,'" . $_REQUEST['Payr'] . "'
            ,'" . $_REQUEST['Payer'] . "'
            ,'" . $_REQUEST['P_Adr'] . "'
         
            
            ,'" . Mymodels::getUserCACC() . "'

            
            ,'" . $_REQUEST['T_SRV'] . "'
            ,'" . $_REQUEST['T_PAK'] . "'
            ,'" . $_REQUEST['T_DEL'] . "'
            
            
            ,'" . $_REQUEST['PCS'] . "'
            ,'" . $_REQUEST['WT'] . "'
            ,'" . $_REQUEST['T_Chg'] . "'
            
                    
            ,ROUND('" . $_REQUEST['Vol_H'] . "',0)
            ,ROUND('" . $_REQUEST['Vol_L'] . "',0)
            ,ROUND('" . $_REQUEST['Vol_W'] . "',0)
            
           ,'" . $_REQUEST['Vol_WT'] . "'
           ,'" . $_REQUEST['V_Car'] . "'
           ,'" . $_REQUEST['Descr'] . "'
           
           
           ,'" . $_REQUEST['DTD'] . "'
           ,'" . $_REQUEST['dop_NotWork'] . "'
           
           ,'" . $_REQUEST['ReceiveFromRec'] . "'
           ,nullif('" . $_REQUEST['dop_NotWork_date'] . "','')
           ,nullif('" . $_REQUEST['dop_NotWork_time'] . "','')
           ,'" . $_REQUEST['Pack_Other'] . "'
            )
            ";


        Yii::$app->db
            ->createCommand($mssql_query)
            ->execute();

        $lastInsertID = Yii::$app->db->getLastInsertID();

        // отобразим текущий контакт по $_REQUEST[id]
        $_REQUEST['id'] = $lastInsertID;
        return $this->actionView();


    }

}
