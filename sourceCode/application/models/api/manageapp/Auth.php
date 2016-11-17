<?php

class Model_api_manageapp_Auth extends Zend_Db_Table_Abstract {

    protected $_name = 'app_user_manager';
    protected $_id = 'id';

    public function getUserInfo($user_id) {
        $db = Zend_Registry::get('connectDB');
        $sql = $db->select()
                ->from(array('a_u_m' => 'app_user_manager'))
                ->where('user_id = ?', $user_id);
        $data = $db->fetchRow($sql);
        return $data;
    }
}
