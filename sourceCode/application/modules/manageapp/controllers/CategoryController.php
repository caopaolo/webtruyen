<?php

class Manageapp_CategoryController extends Amobi_Controller_Action
{
    // Bien luu layout cua controller
    protected $_layout = 'manageapp';

    /**
     * ************************************** Begin: Category ******************************************
     */
    // Action show danh sach the loai cua ung dung
    public function indexAction()
    {
        try {
            // Chuan bi tham so
            $categoryModel = new Model_api_manageapp_Dbcategory();
            
            // Lay du lieu tu DB
            $data = $categoryModel->getListCategory();
            
            // Truyen bien ra view
            $this->view->data = $data;
        } catch (Exception $exc) {
            $this->view->error = $exc->getMessage();
        }
    }
    
    // Action them the loai cho ung dung
    public function addAction()
    {
        try {
            // Load them file css, js
            $this->view->headScript()->appendFile("/assets/managerapp/js/jquery.validate.min.js");
            $this->view->headScript()->appendFile("/assets/managerapp/js/form-validation-script.js");
            
            /**
             * ************************** jquery tagit *************************
             */
            $this->view->headLink()->appendStylesheet('/assets/plugins/tagit/css/jquery.tagit.css');
            $this->view->headLink()->appendStylesheet('/assets/plugins/tagit/css/tagit.ui-zendesk.css');
            $this->view->headScript()->appendFile('/assets/plugins/jquery-ui-1.10.4/jquery-ui-1.10.4.min.js');
            $this->view->headScript()->appendFile('/assets/plugins/tagit/js/tag-it.min.js');
            
            // Chuan bi tham so
            $params = $this->_arrParam;
            $categoryModel = new Model_api_manageapp_Dbcategory();
            $time = date('Y-m-d H:i:s');
            $data = array();
            
            // Xu ly khi submit form
            if ($this->getRequest()->getMethod() == 'POST') {
                if (! empty($params['name'])) {
                    // Kiem tra trung lap ten category
                    if ($categoryModel->checkName($params['name'])) {
                       
                        // Xu ly neu co avatar up len
                        if (! empty($_FILES['avatar']) && empty($_FILES['avatar']['error'])) {
                            $file = $_FILES['avatar'];
                            if ($file['name']) { // Neu ten file khac rong
                                $avatar_result = Amobi_Utilities_Image::handleImage(FOLDER_AVA_CATEGORY, REMOTE_FOLDER_AVA_CATEGORY, $file['tmp_name'], '', 1);
                            
                                if ($avatar_result['status'] == 1) {
                                    $params['avatar_large'] = $avatar_result['data']['image_large'];
                                    $params['avatar_medium'] = $avatar_result['data']['image_medium'];
                                    $params['avatar_small'] = $avatar_result['data']['image_small'];
                                }
                            }
                        }
                        
                        // Thong bao thanh cong
                        $data = array(
                            'created_time' => $time,
                            'name' => $params['name'],
                            'description' => ! empty($params['description']) ? $params['description'] : null,
                            'avatar_large' => ! empty($params['avatar_large']) ? $params['avatar_large'] : null,
                            'avatar_medium' => ! empty($params['avatar_medium']) ? $params['avatar_medium'] : null,
                            'avatar_small' => ! empty($params['avatar_small']) ? $params['avatar_small'] : null,
                            'key_words' => ! empty($params['key_words']) ? $params['key_words'] : null,
                            'updated_time' => $time
                        );
                        $categoryModel->insert($data);
                        
                        $this->view->success = "Thêm mới thành công";
                    } else {
                        $data = $params;
                        $this->view->warning = "Tên category đã được sử dụng";
                    }
                } else {
                    $data = $params;
                    $this->view->warning = "Thiếu dữ liệu truyền lên";
                }
            }
            
            // Truyen bien ra view
            $this->view->data = $data;
        } catch (Exception $exc) {
            $this->view->error = $exc->getMessage();
        }
    }
    
    // Action sua the loai cua ung dung
    public function editAction()
    {
        try {
            // Load them file css, js
            $this->view->headScript()->appendFile("/assets/managerapp/js/jquery.validate.min.js");
            $this->view->headScript()->appendFile("/assets/managerapp/js/form-validation-script.js");
            
            /**
             * ************************** jquery tagit *************************
             */
            $this->view->headLink()->appendStylesheet('/assets/plugins/tagit/css/jquery.tagit.css');
            $this->view->headLink()->appendStylesheet('/assets/plugins/tagit/css/tagit.ui-zendesk.css');
            $this->view->headScript()->appendFile('/assets/plugins/jquery-ui-1.10.4/jquery-ui-1.10.4.min.js');
            $this->view->headScript()->appendFile('/assets/plugins/tagit/js/tag-it.min.js');
            
            // Chuan bi tham so
            $params = $this->_arrParam;
            $categoryModel = new Model_api_manageapp_Dbcategory();
            $storyCategoryModel = new Model_api_manageapp_StoryCategory();
            $categoryNewModel = new Model_api_manageapp_CategoryNew();
            $db = Zend_Db_Table::getDefaultAdapter();
            $time = date('Y-m-d H:i:s');
            // lay list category new
            $listCategoryNew  = $categoryNewModel ->getListCategoryNoneMapping();
          
            // Kiem tra id the loai neu la cap nhat
            if (! empty($params['category_id'])) {
                $category = $categoryModel->getDetailCategory($params['category_id']);
                if (! empty($category)) {
                    // Xu ly khi submit form
                    if ($this->getRequest()->getMethod() == 'POST') {
                        if (! empty($params['name'])) {
                            // Kiem tra trung lap ten category
                            if ($categoryModel->checkName($params['name'], $params['category_id'])) {
                                
                                // Xu ly neu co avatar up len
                                if (! empty($_FILES['avatar']) && empty($_FILES['avatar']['error'])) {
                                    $file = $_FILES['avatar'];
                                    if ($file['name']) { // Neu ten file khac rong
                                        $avatar_result = Amobi_Utilities_Image::handleImage(FOLDER_AVA_CATEGORY, REMOTE_FOLDER_AVA_CATEGORY, $file['tmp_name'], '', 1);
                                       
                                        if ($avatar_result['status'] == 1) {
                                            $params['avatar_large'] = $avatar_result['data']['image_large'];
                                            $params['avatar_medium'] = $avatar_result['data']['image_medium'];
                                            $params['avatar_small'] = $avatar_result['data']['image_small'];
                                        }
                                    }
                                }
                                
                                if(isset($params['category_new'])){
                                    $where = array(
                                        $db->quoteInto('id = ?',$params['category_new'])
                                    );
                                    $data = array(
                                        'category_crawler' => $params['category_id']
                                    );
                                    $categoryNewModel->update($data, $where);
                                    $where = array(
                                        $db->quoteInto('category_id = ?', $params['category_id'])
                                    );
                                    $data = array(
                                        'category_new'  => $params['category_new'],
                                        'created_time'  => $time
                                        );

                                    $storyCategoryModel->update($data,$where);
                                }
                                // Cap nhat
                                $data = array(
                                    'name' => $params['name'],
                                    'description' => ! empty($params['description']) ? $params['description'] : null,
                                    'key_words' => ! empty($params['key_words']) ? $params['key_words'] : null,
                                    'updated_time' => $time
                                );
                                
                                $image_to_delete = array();
                                
                                // Them cac du lieu cap nhat neu co
                                // Neu khong, giu nguyen cac gia tri cu
                              
                                
                                if (! empty($params['avatar_small'])) {
                                    $data['avatar_small'] = $params['avatar_small'];
                                    
                                    if (! empty($params['avatar_medium'])) {
                                        $data['avatar_medium'] = $params['avatar_medium'];
                                    } else {
                                        $data['avatar_medium'] = null;
                                    }
                                    
                                    if (! empty($params['avatar_large'])) {
                                        $data['avatar_large'] = $params['avatar_large'];
                                    } else {
                                        $data['avatar_large'] = null;
                                    }
                                    
                                    // Tao image de xoa
                                    if (! empty($category['avatar_small']) && $category['avatar_small'] != NO_IMAGE_SMALL_LINK) {
                                        $image_to_delete[] = $category['avatar_small'];
                                    }
                                    if (! empty($category['avatar_medium']) && $category['avatar_medium'] != NO_IMAGE_MEDIUM_LINK) {
                                        $image_to_delete[] = $category['avatar_medium'];
                                    }
                                    if (! empty($category['avatar_large']) && $category['avatar_large'] != NO_IMAGE_LARGE_LINK) {
                                        $image_to_delete[] = $category['avatar_large'];
                                    }
                                }
                                
                                // Cap nhat
                                $where = 'id = ' . $params['category_id'];
                                $categoryModel->update($data, $where);
                                
                                // Neu cap nhat thanh cong thi xoa anh cu
                                if (! empty($image_to_delete) && count($image_to_delete)) {
                                    $file_object = new Amobi_Utilities_File();
                                    foreach ($image_to_delete as  $value) {
                                        unlink($value);
                                    }
                                }
                                
                                // Lay lai thong tin
                                $category = $categoryModel->getDetailCategory($params['category_id']);
                                $category_new =  $categoryNewModel -> getCategoryNew($params['category_id']);
                               
                                if($category_new){
                                   $this ->view->category_new = $category_new;
                                }
                                $data = $category;
                                
                                // Thong bao thanh cong
                                $this->view->success = "Cập nhật thành công";
                            } else {
                                $data = $category;
                                $this->view->warning = "Tên category đã được sử dụng";
                            }
                        } else {
                            $data = $category;
                            $this->view->warning = "Thiếu dữ liệu truyền lên";
                        }
                    } else {
                        $data = $category;
                    }
                    // Truyen bien ra view
                    $this->view->data = $data;
                     $this->view->listCategory = $listCategoryNew;
                } else {
                    $this->view->warning = "Không tìm thấy dữ liệu";
                }
            } else {
                $this->view->warning = "Thiếu id category";
            }
           
        } catch (Exception $exc) {
            $this->view->error = $exc->getMessage();
        }
    }
    
    // Action xoa the loai cua ung dung
    public function deleteAction()
    {
        // Tat view va layout
        $this->_helper->viewRenderer->setNorender(true);
        $this->_helper->layout->disableLayout(true);
        try {
            // Chuan bi tham so
            $params = $this->_arrParam;
            $image_to_delete = array();
            $categoryModel = new Model_api_manageapp_Dbcategory();
            
            // Xu ly khi co request
            if ($this->_request->isPost()) {
                if (! empty($params['id'])) {
                    // Kiem tra ton tai ban ghi
                    $category = $categoryModel->getDetailCategory($params['id']);
                    if (! empty($category)) {
                        $where = "id = " . $params['id'];
                        $categoryModel->delete($where);
                        
                        // Xoa anh tren storage
                        if (! empty($category['avatar_small']) && $category['avatar_small'] != NO_IMAGE_LARGE_LINK) {
                            $image_to_delete[] = $category['avatar_small'];
                        }
                        if (! empty($category['avatar_medium']) && $category['avatar_medium'] != NO_IMAGE_MEDIUM_LINK) {
                            $image_to_delete[] = $category['avatar_medium'];
                        }
                        if (! empty($category['avatar_large']) && $category['avatar_large'] != NO_IMAGE_SMALL_LINK) {
                            $image_to_delete[] = $category['avatar_large'];
                        }
                        if (! empty($category['icon'])) {
                            $image_to_delete[] = $category['icon'];
                        }
                        // Neu xoa du lieu thanh cong thi xoa luon anh tuong ung
                        if (! empty($image_to_delete) && count($image_to_delete)) {
                            foreach ($image_to_delete as  $value) {
                                unlink($value);
                            }
                        }
                        
                        // Tao du lieu tra ve
                        $response = array(
                            'status' => 1,
                            'message' => 'Xóa thành công'
                        );
                    } else {
                        $response = array(
                            'status' => 0,
                            'message' => 'Không tồn tại bản ghi'
                        );
                    }
                } else {
                    $response = array(
                        'status' => 0,
                        'message' => 'Thiếu tham số'
                    );
                }
            } else {
                $response = array(
                    'status' => 0,
                    'message' => 'Phương thức không được hỗ trợ'
                );
            }
        } catch (Exception $exc) {
            $response = array(
                'status' => 0,
                'message' => 'Lỗi xử lý server'
            );
        }
        
        $this->__showJson($response);
    }

    public function updateAjaxAction()
    {
        $this->_helper->viewRenderer->setNorender(true);
        $this->_helper->layout->disableLayout(true);
        try {
            // Chuan bi tham so
            $params = $this->_arrParam;
            $categories_data = json_decode($params["categories_data"]);
            $categoryModel = new Model_api_manageapp_Dbcategory();
            foreach ($categories_data as $id => $category_syn) {
                $categoryModel->update(array(
                    "synthesis_category" => $category_syn
                ), "id = " . $id);
            }
            $response = array(
                'status' => 1,
                'message' => "Thay đổi thành công!"
            );
        } catch (Exception $exc) {
            $response = array(
                'status' => 0,
                'message' => 'Lỗi xử lý server'
            );
        }
        
        $this->__showJson($response);
    }

/**
 * ************************************** End: Category ******************************************
 */

/**
 * ****************************************** Begin: Cac ham ho tro ******************************************
 */

/**
 * ****************************************** End: Cac ham ho tro ******************************************
 */
}
