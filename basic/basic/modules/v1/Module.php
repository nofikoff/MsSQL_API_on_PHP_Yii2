<?php
// Check this namespace:
namespace app\modules\v1;


use Yii;

class Module extends \yii\base\Module
{
    public function init()
    {
        if (isset($_REQUEST['login'])) {
            Yii::$app->db->username = $_REQUEST['login'];
            Yii::$app->db->password = $_REQUEST['password'];
        }
        parent::init();

    }
}
