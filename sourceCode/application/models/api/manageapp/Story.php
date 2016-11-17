<?php

class Model_api_manageapp_Story extends Zend_Db_Table_Abstract {

    protected $_name = 'story';
    protected $_id = 'id';

//    public function getListStory() {
//        $data = $this->select();
//        $data = $this->fetchAll($data)->toArray();
//        return $data;
//    }
    // ham kiem tra ton tai cua story
//    public function getStory($params = null){
//        $db = Zend_Db_Table::getDefaultAdapter();
//        $mysql = $db->select()
//                    ->from(array('t' => $this->_name));
//                    
//        
//        
//        if (!empty($params['author_code'])) {
//            $mysql->where('t.author_code = ?', $params['author_code']);    
//        }
//        $result = $db->fetchRow($mysql);
//        return $result;
//    }
       // Ham lay ra thong tin chi tiet truyen theo id
    public function getStoryById($id) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $mysql = $db->select()
                    ->from(array('s' => $this->_name))
                    ->where('s.id = ?', $id);
      
        
        $response = $db->fetchRow($mysql);
        
        return $response;
    }


    // ham lay list category
    public function getCategoryList ($story_id = null) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $mysql = $db->select()
                    ->from(array('s' => $this->_name))
                    ->join(array('s_c' => 'story_category'), 's.id = s_c.story_id')
                    ->join(array('ca' => 'category'), 's_c.category_id = ca.id', array('category_name' => 'ca.name'));
                    
       
        $mysql->where('s.id = ?', $story_id);    
    
        $result = $db->fetchAll($mysql);
        return $result;
    }
    // Ham lay danh sach bai post cua the loai do
    public function getListStory($params = null) {

        $db = Zend_Db_Table::getDefaultAdapter();
        $mysql = $db->select()
                    ->from(array('s' => $this->_name))
//                    ->join(array('ca' => 'category'), 's_c.category_id = ca.id', array('category_name' => 'ca.name'))
                    ->join(array('au' => 'author'), 's.author_id = au.id', array('author_name' => 'au.author_name'));
        if(!empty($params['category_id'])||!empty($params['category_new_id'])){
            $mysql->join(array('s_c' => 'story_category'), 's_c.story_id = s.id', array());
        }
        if (!empty($params['story_code'])) {
            $mysql->where('s.story_code = ?', $params['story_code']);    
        }
        if (!empty($params['author'])) {
            $mysql->where('s.author_id = ?', $params['author']);
        }
        if (!empty($params['category_id'])) {
            $mysql->where('s_c.category_id = ?', $params['category_id']);
        }
        if (!empty($params['category_new_id'])) {
            $mysql->where('s_c.category_new = ?', $params['category_new_id']);
        }
        if (!empty($params['search_value'])) {
            $search_value = '%'.$params['search_value'].'%';
            $where = $db->quoteInto('(s.story_name LIKE ?)', $search_value);
            $mysql->where($where);
        } 
        
        if (!empty($params['status_search'])) {
            $mysql->where('s.status = ?', $params['status_search']);
        }
        if (!empty($params['crawler'])) {
            $mysql->where('s.is_crawler = ?', $params['crawler']);
        }
        
        if (!empty($params['order'])) {
            $order = array();
            foreach ($params['order'] as $item) {
                if (!empty($item['column_order'])) {
                    $temp = 's.' . $item['column_order'] . ' ' . $item['dir_order'];
                    $order[] = $temp;    
                }  
            }
            $mysql->order($order);
        }
        
        if (isset($params['start']) && isset($params['length'])) {
            $start = $params['start'];
            $length = $params['length'];
            $mysql->limit($length,$start);
        }
   
        $result = $db->fetchAll($mysql);
        
        if (!empty($params['total'])) {
            $totalMysql = $db->select()
                            ->from(array('s' => $this->_name), array('total'=>'COUNT(*)'))
//                            ->join(array('s_c' => 'story_category'), 's.id = s_c.story_id')
//                            ->join(array('ca' => 'category'), 's_c.category_id = ca.id', array('category_name' => 'ca.name'))
                            ->join(array('au' => 'author'), 's.author_id = au.id', array('author_name' => 'au.author_name'));
            
           
            if (!empty($params['crawler'])) {
                $mysql->where('s.is_crawler = ?', $params['crawler']);
            }
            if (!empty($params['author'])) {
                $totalMysql->where('s.author_id = ?', $params['author']);
            } 
            if (!empty($params['category_id'])) {
            $mysql->where('s_c.category_id = ?', $params['category_id']);
            }
            if (!empty($params['category_new_id'])) {
                $mysql->where('s_c.category_new = ?', $params['category_new_id']);
            }
            if (!empty($params['search_value'])) {
                $search_value = '%'.$params['search_value'].'%';
                $where = $db->quoteInto('(s.story_name LIKE ?)', $search_value);
                $totalMysql->where($where);
            }
            
            if (!empty($params['status_search'])) {
                $totalMysql->where('s.status = ?', $params['status_search']);
            }
     
            $total_result = $db->fetchOne($totalMysql);
        
            return array($total_result, $result);     
        } else {
          
            return $result;
        }
        
    }
    
}
