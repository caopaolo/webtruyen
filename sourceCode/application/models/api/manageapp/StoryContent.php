<?php

class Model_api_manageapp_StoryContent extends Zend_Db_Table_Abstract {

    protected $_name = 'story_content';
    protected $_id = 'id';
     // Ham lay danh sach bai post cua the loai do
    public function getListStoryContent($params = null) {
      
        $db = Zend_Db_Table::getDefaultAdapter();
        $mysql = $db->select()
                    ->from(array('s_c' => $this->_name))
//                    ->join(array('s_c' => 'story_category'), 's.id = s_c.story_id')
//                    ->join(array('ca' => 'category'), 's_c.category_id = ca.id', array('category_name' => 'ca.name'))
                    ->join(array('s' => 'story'), 's_c.story_id = s.id', array('story_name' => 's.story_name', 'story_crawler' =>'s.is_crawler'));
        
       
        if (!empty($params['search_value'])) {
            $search_value = '%'.$params['search_value'].'%';
            $where = $db->quoteInto('(s_c.title LIKE ?)', $search_value);
            $mysql->where($where);
        } 
        if (!empty($params['story_id'])) {
            $mysql->where('s.id = ?', $params['story_id']);
        }
        if (!empty($params['status_search'])) {
            $mysql->where('s_c.status = ?', $params['status_search']);
        }
        if (!empty($params['crawler'])) {
            $mysql->where('s_c.is_crawler = ?', $params['crawler']);
        }
        if (!empty($params['order'])) {
            $order = array();
            foreach ($params['order'] as $item) {
                if (!empty($item['column_order'])) {
                    $temp = 's_c.' . $item['column_order'] . ' ' . $item['dir_order'];
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
                            ->from(array('s_c' => $this->_name), array('total'=>'COUNT(*)'))
//                            ->join(array('s_c' => 'story_category'), 's.id = s_c.story_id')
//                            ->join(array('ca' => 'category'), 's_c.category_id = ca.id', array('category_name' => 'ca.name'))
                            ->join(array('s' => 'story'), 's_c.story_id = s.id', array('story_name' => 's.story_name'));
            
           
        
            if (!empty($params['search_value'])) {
                $search_value = '%'.$params['search_value'].'%';
                $where = $db->quoteInto('(s_c.title LIKE ?)', $search_value);
                $totalMysql->where($where);
            }
            if (!empty($params['crawler'])) {
                $mysql->where('s_c.is_crawler = ?', $params['crawler']);
            }
            if (!empty($params['story_id'])) {
            $mysql->where('s.id = ?', $params['story_id']);
            }
            
            if (!empty($params['status_search'])) {
                $totalMysql->where('s_c.status = ?', $params['status_search']);
            }
     
            $total_result = $db->fetchOne($totalMysql);
        
            return array($total_result, $result);     
        } else {
          
            return $result;
        }
        
    }
     // Lay thong tin chi tiet cua category
    public function getContentById($id) {
        $data = $this->select()
                    ->where('id = ?', $id);
        
        $response = $this->fetchRow($data);
        
        if (!empty($response)) {
            return $response->toArray();
        } else {
            return false;
        }
    }
    public function getListContentCrawler($params = null){
        
    }
}