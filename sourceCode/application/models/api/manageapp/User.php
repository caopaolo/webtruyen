<?php

class Model_api_manageapp_User extends Zend_Db_Table_Abstract {

    protected $_name = 'user';
    protected $_id = 'id';

    public function getUser($params) {
        $db = Zend_Registry::get('connectDB2');
        $mysql = $db->select()
                        ->from('user');
        if (!empty($params['search_user_name'])) {
            $mysql->where('user_name LIKE ?', $params['search_user_name'] . '%');    
        }
        
        if (!empty($params['user_id'])) {
            $mysql->where('id = ?', $params['user_id'] . '%');
            $result = $db->fetchRow($mysql);
            return $result;
        }
        $result = $db->fetchAll($mysql);
        return $result;
    }
}
