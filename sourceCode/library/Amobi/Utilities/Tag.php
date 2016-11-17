<?php
/*
* Function for handle tag
* Author: TTL
* Create time: 11/07/2016 17:12
* Update time: 11/07/2016 17:12
*/
class Amobi_Utilities_Tag{
    
    public static function handle ($content_id, $tag_string, $app_id) {
        try {
            if (!empty($tag_string)) {
                // Chuan bi bien
                $time = date('Y-m-d H:i:s');
                $tagModel = new Model_api_manageapp_Tag();
                $contentTagsModel = new Model_api_manageapp_ContentTags();
                $db = Zend_Db_Table::getDefaultAdapter();
                $tag_array = explode(',', $tag_string);
                $tag_array_id = array();
               
                
                // Lay thong tin tag cu cua content
                $old_tag_array = $tagModel->getListForContent(array('content_id' => $content_id));
                Zend_Debug::dump($old_tag_array);
                // Duyet mang tag de them vao
                foreach ($tag_array as $tag) {
                    $tag = trim($tag);
                    
                    if (!empty($tag)) {
                        // Tao ma code cho tag
                        $tag_code = Amobi_Utilities_Input::tenchuan($tag);
                    
                        // Kiem tra su ton tai tag
                        $where = array(
                            $db->quoteInto('tag_code = ?', $tag_code),
                            $db->quoteInto('app_id = ?', $app_id)
                        );
                        $check = $tagModel->fetchRow($where);
                        if (!empty($check)) { // Neu da ton tai
                            $tag_info = $check->toArray();
                            
                            // Kiem tra su ton tai moi lien ket
                            $where = array(
                                $db->quoteInto('content_id = ?', $content_id),
                                $db->quoteInto('tag_id = ?', $tag_info['id'])
                            );
                            $check_relation = $contentTagsModel->fetchRow($where);
                            if (!empty($check_relation)) { 
                                $tag_array_id[] = $tag_info['id'];
                            } else {
                                $data = array( // Neu chua ton tai moi them vao
                                    'content_id'    => $content_id,
                                    'tag_id'        => $tag_info['id'],
                                    'created_time'  => $time
                                );
                                
                                $contentTagsModel->insert($data);
                            }
                        } else { // Neu chua ton tai
                            $data = array(
                                'tag'           => $tag,
                                'tag_code'      => $tag_code,
                                'app_id'        => $app_id,
                                'created_time'  => $time
                            );
                            
                            $tag_id = $tagModel->insert($data);
                            
                            $data = array(
                                'content_id'    => $content_id,
                                'tag_id'        => $tag_id,
                                'created_time'  => $time
                            );
                            
                            $contentTagsModel->insert($data);
                        }
                    }
                }
                
                // So sanh tag cu voi tag moi
                // Neu tag nao khong nam trong tag moi
                // thi xoa trong bang lien ket di
                if (!empty($old_tag_array)) {
                    // Duyet mang tag
                    foreach ($old_tag_array as $old_tag) {                        
                        if (!in_array($old_tag['id'], $tag_array_id)) {
                            // Xoa moi lien ket
                            $where = array(
                                $db->quoteInto('content_id = ?', $content_id),
                                $db->quoteInto('tag_id = ?', $old_tag['id'])
                            );
                            $contentTagsModel->delete($where);                   
                        }
                    }    
                }
                
                // Tao du lieu tra ve
                $response = array(
                    'status'    => 1,
                    'message'   => 'Xử lý tag thành công'
                );
            } else {
                $response = array(
                    'status'    => 0,
                    'message'   => 'Tag rỗng'
                );
            }
        } catch (Exception $e) {
            $response = array(
                'status'    => 0,
                'message'   => $e->getMessage()
            );
        }
        
        return $response;
    }
}