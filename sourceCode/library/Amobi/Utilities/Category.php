<?php
/*
* Function for handle category
* Author: CPL
* Create time: 11/10/2016 17:12
* Update time: 11/10/2016 17:12
*/
class Amobi_Utilities_Category{
    
    public static function handle ($story_id, $category_string) {
        try {
            if (!empty($category_string)) {
                // Chuan bi bien
                $time = date('Y-m-d H:i:s');
                $storyModel = new Model_api_manageapp_Story();
                $categoryModel = new Model_api_manageapp_Dbcategory();
                $storyCategoryModel = new Model_api_manageapp_StoryCategory();
                $db = Zend_Db_Table::getDefaultAdapter();
                $category_array = explode(',', $category_string);
                $category_array_id = array();
        
                
                // Lay thong tin category cu cua story
                $old_category_array = $categoryModel->getListForStory(array('story_id' => $story_id));
              
                // Duyet mang category de them vao
                foreach ($category_array as $category) {
                    $category = trim($category);
                   
                    if (!empty($category)) {
                       
                    
                        // Kiem tra su ton tai category
                        $where = array(
                            $db->quoteInto('name = ?', $category)
                            
                        );
                        $check = $categoryModel->fetchRow($where);
                        
                        if (!empty($check)) { // Neu da ton tai
                            $category_info = $check->toArray();
                            
                            // Kiem tra su ton tai moi lien ket
                            $where = array(
                                $db->quoteInto('story_id = ?', $story_id),
                                $db->quoteInto('category_id = ?', $category_info['id'])
                            );
                            $check_relation = $storyCategoryModel->fetchRow($where);
                            if (!empty($check_relation)) { 
                                $category_array_id[] = $category_info['id'];
                            } else {
                                $data = array( // Neu chua ton tai moi them vao
                                    'story_id'    => $story_id,
                                    'category_id'        => $category_info['id'],
                                    'created_time'  => $time
                                );
                                
                                $storyCategoryModel->insert($data);
                            }
                        } else { // Neu chua ton tai
                            $data_add = array(
                                'name'           => $category,
                                'created_time'  => $time,
                                'updated_time'  => $time,
                                
                            );
                         
                            $category_id = $categoryModel->insert($data_add);
                
                            $data = array(
                                'story_id'    => $story_id,
                                'category_id'        => $category_id,
                                'created_time'  => $time
                            );
                            
                            $storyCategoryModel->insert($data);
                          
                        }
                    }
                }
                
                // So sanh category cu voi category moi
                // Neu category nao khong nam trong category moi
                // thi xoa trong bang lien ket di
                if (!empty($old_category_array)) {
                    // Duyet mang category
                    foreach ($old_category_array as $old_category) {                        
                        if (!in_array($old_category['id'], $category_array_id)) {
                            // Xoa moi lien ket
                            $where = array(
                                $db->quoteInto('story_id = ?', $story_id),
                                $db->quoteInto('category_id = ?', $old_category['id'])
                            );
                            $storyCategoryModel->delete($where);                   
                        }
                    }    
                }
                
                // Tao du lieu tra ve
                $response = array(
                    'status'    => 1,
                    'message'   => 'Xử lý category thành công'
                );
            } else {
                $response = array(
                    'status'    => 0,
                    'message'   => 'Category rỗng'
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