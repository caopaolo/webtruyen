<?php

class Model_api_manageapp_Tag extends Zend_Db_Table_Abstract {

    protected $_name = 'tags';
    protected $_id = 'id';
    
    // Ham lay danh sach tag cua mot content
    public function getListForContent ($params) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $mysql = $db->select()
                    ->from(array('c_t' => 'content_tags'), array())
                    ->join(array('t' => 'tags'), 't.id = c_t.tag_id', array('id', 'tag', 'tag_code'));
        
        if (!empty($params['content_id'])) {
            $mysql->where('c_t.content_id =?', $params['content_id']);
        }
        
        $result = $db->fetchAll($mysql);
        return $result;
    }
    
    // Ham lay danh sach tag cua mot app
    public function getListForApp ($params) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $mysql = $db->select()
                    ->join(array('t' => $this->_name), array('id', 'tag', 'tag_code'))
                    ->where('t.status = ?', STATUS_ON);
        
        if (!empty($params['app_id'])) {
            $mysql->where('t.app_id =?', $params['app_id']);
        }
        
        if (!empty($params['order'])) {
            if ($params['order'] == 'length_tag_code') {
                $mysql->order('LENGTH(t.tag_code) DESC');
            } else {
                $mysql->order('t.' . $params['order'] . ' DESC');
            }
        }
            
        $result = $db->fetchAll($mysql);
        return $result;
    }
    
    // Ham lay danh sach tag
    public function getList($params = null) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $mysql = $db->select()
                    ->from(array('t' => $this->_name));
        
        if (!empty($params['app_id'])) {
            $mysql->where('t.app_id = ?', $params['app_id']);
        }
        
        if (!empty($params['search_value'])) {
            $search_value = '%'.$params['search_value'].'%';
            $where = $db->quoteInto('(t.tag LIKE ?)', $search_value);
            $mysql->where($where);
        }
        
        if (!empty($params['status_search'])) {
            $mysql->where('t.status = ?', $params['status_search']);
        }
        
        if (!empty($params['order'])) {
            $order = array();
            foreach ($params['order'] as $item) {
                if (!empty($item['column_order'])) {
                    $temp = 't.' . $item['column_order'] . ' ' . $item['dir_order'];
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
                            ->from(array('t' => $this->_name), array('total'=>'COUNT(*)'));
            
            if (!empty($params['app_id'])) {
                $totalMysql->where('t.app_id = ?', $params['app_id']);
            }
    
            if (!empty($params['search_value'])) {
                $search_value = '%'.$params['search_value'].'%';
                $where = $db->quoteInto('(t.tag LIKE ?)', $search_value);
                $totalMysql->where($where);
            }
            
            if (!empty($params['status_search'])) {
                $totalMysql->where('t.status = ?', $params['status_search']);
            }
    
            $total_result = $db->fetchOne($totalMysql);
    
            return array($total_result, $result);     
        } else {
            return $result;
        }
    }
    
    // Ham lay thong tin tag
    public function getTag ($params=null) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $mysql = $db->select()
                    ->from(array('t' => $this->_name));
                    
        if (!empty($params['app_id'])) {
            $mysql->where('t.app_id =?', $params['app_id']);
        }
        
        if (!empty($params['id'])) {
            $mysql->where('t.id = ?', $params['id']);    
        }
        
        if (!empty($params['except_id'])) {
            $mysql->where('t.id != ?', $params['except_id']);    
        }
        
        if (!empty($params['tag_code'])) {
            $mysql->where('t.tag_code = ?', $params['tag_code']);    
        }
        
        $result = $db->fetchRow($mysql);
        return $result;
    }
}
