<?php

class Model_api_manageapp_Author extends Zend_Db_Table_Abstract {

    protected $_name = 'author';
    protected $_id = 'id';

    public function getListAuthor() {
        $data = $this->select();
        $data = $this->fetchAll($data)->toArray();
        return $data;
    }
    public function getAuthor ($params=null) {

        $db = Zend_Db_Table::getDefaultAdapter();
        $mysql = $db->select()
                    ->from(array('t' => $this->_name));
                    
        if (!empty($params['id'])) {
            $mysql->where('t.id = ?', $params['id']);    
        }
        
        if (!empty($params['except_id'])) {
            $mysql->where('t.id != ?', $params['except_id']);    
        }
        
        if (!empty($params['author_code'])) {
            $mysql->where('t.author_code = ?', $params['author_code']);    
        }
        $result = $db->fetchRow($mysql);
        return $result;
    }
}
