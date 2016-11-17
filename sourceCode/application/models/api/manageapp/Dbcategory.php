<?php

class Model_api_manageapp_Dbcategory extends Zend_Db_Table_Abstract {

    protected $_name = 'category';
    protected $_id = 'id';

    // Lay danh sach category cua app
    public function getListCategory() {
        $data = $this->select();
     
        $data = $this->fetchAll($data)->toArray();
        return $data;
    }

    // Ham lay danh sach category cua mot story
    public function getListForStory ($params) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $mysql = $db->select()
                    ->from(array('s_c' => 'story_category'), array())
                    ->join(array('c' => 'category'), 'c.id = s_c.category_id', array('id', 'name'));
        
        if (!empty($params['story_id'])) {
            $mysql->where('s_c.story_id =?', $params['story_id']);
        }
        
        $result = $db->fetchAll($mysql);
        return $result;
    }
    // Ham lay danh sach tag cua mot app
    public function getListForApp ($params) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $mysql = $db->select()
                    ->join(array('c' => $this->_name), array('id', 'name'))
                    ->where('c.status = ?', STATUS_ON);
        
        
        if (!empty($params['order'])) {
           
                $mysql->order('c.' . $params['order'] . ' DESC');
            
        }
            
        $result = $db->fetchAll($mysql);
        return $result;
    }
    public function getCategories() {
        $db = Zend_Db_Table::getDefaultAdapter();
        $mysql = $db->select()
            ->from(array('db_ca' => $this->_name))
            ->join(array('a_a_i' => 'app_amobi_id'), 'a_a_i.app_id = db_ca.app_id', array('app_name' => 'a_a_i.app_name'))
            ->where('a_a_i.type = ?', 1);

        $data = $db->fetchAll($mysql);
        return $data;
    }
    
    // Lay danh sach category cua app bao tong hop
    public function getListCategorySynthesis($app_type) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $mysql = $db->select()
                    ->from(array('db_ca' => $this->_name))
                    ->join(array('a_a_i' => 'app_amobi_id'), 'a_a_i.app_id = db_ca.app_id', array('app_name' => 'a_a_i.app_name'))
                    ->where('a_a_i.type = ?', $app_type)
                    ->where('a_a_i.app_id = ?', BAO_TONG_HOP_APP_ID);
        
        $data = $db->fetchAll($mysql);
        return $data;
    }
    
    // Lay thong tin chi tiet cua category
    public function getDetailCategory($id) {
        $data = $this->select()
                    ->where('id = ?', $id);
        
        $response = $this->fetchRow($data);
        
        if (!empty($response)) {
            return $response->toArray();
        } else {
            return false;
        }
    }
    
    // Ham kiem tra trung lap ten category
    public function checkName($name, $category_id=null) {
        $data = $this->select()
                    ->where('name = ?', $name);
        // Neu co $category_id
        // Bo qua kiem tra chinh category_id do
        if (!empty($category_id)) {
            $data->where('id != ?', $category_id);
        }
        $response = $this->fetchRow($data);
        
        if (!empty($response)) {
            return false;
        } else {
            return true;
        }
    }
}
