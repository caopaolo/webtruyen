<?php

class Manageapp_StoryContentController extends Amobi_Controller_Action
{
    // Bien luu layout cua controller
    protected $_layout = 'manageapp';

    /**
     * ************************************** Begin: StoryContent ******************************************
     */
    // Action show danh sach chuong
    public function indexAction()
    {
        try {
            // Chuan bi tham so
            $params = $this->_arrParam;
            if (!empty($params['story_id'])) {
                $story_id = $params['story_id'];
            } else {
                $story_id = null;
            }

            if (!empty($params['warning'])) {
                $this->view->warning = $params['warning'];
            }

            // Truyen bien ra view
            $this->view->story_id = $story_id;
          
        } catch (Exception $exc) {
            $this->view->error = $exc->getMessage();
        }
    }
    
    // Action them bai post
    public function addAction()
    {
        try {
            // Load them file css, js
            $this->view->headScript()->appendFile("/assets/managerapp/js/jquery.validate.min.js");
            $this->view->headScript()->appendFile('/assets/ckeditor/ckeditor.js');
            $this->view->headScript()->appendFile('/assets/ckfinder/ckfinder.js?v=3.3');
            $this->view->headScript()->appendFile("/assets/managerapp/js/form-validation-script.js");
            $this->view->headScript()->appendFile("/assets/managerapp/js/handle-tag.js");

            /**************************** bootstrap tokenfield **************************/
            $this->view->headLink()->appendStylesheet('/assets/js/bootstrap-tokenfield/css/tokenfield-typeahead.css');
            $this->view->headLink()->appendStylesheet('/assets/js/bootstrap-tokenfield/css/bootstrap-tokenfield.css');
            $this->view->headScript()->appendFile('/assets/js/bootstrap-tokenfield/bootstrap-tokenfield.js');
            $this->view->headScript()->appendFile("/assets/js/bootstrap-tokenfield/typeahead.bundle.js");

            /**************************** jquery.auto-complete **************************/
            $this->view->headScript()->appendFile("/assets/plugins/jquery-autocomplete/jquery.auto-complete.js");
            $this->view->headLink()->appendStylesheet('/assets/plugins/jquery-autocomplete/jquery.auto-complete.css');
                        /* ************************** jquery tagit *************************
             */
            $this->view->headLink()->appendStylesheet('/assets/plugins/tagit/css/jquery.tagit.css');
            $this->view->headLink()->appendStylesheet('/assets/plugins/tagit/css/tagit.ui-zendesk.css');
            $this->view->headScript()->appendFile('/assets/plugins/jquery-ui-1.10.4/jquery-ui-1.10.4.min.js');
            $this->view->headScript()->appendFile('/assets/plugins/tagit/js/tag-it.min.js');
            
            // Chuan bi tham so
            $params = $this->_arrParam;
            $message = ''; // Bien luu thong tin can dua ra view
            $data = array();

            // Bo sung tham so content vi khi loc bi xoa di
            $params['content'] = trim($this->_request->getParam('content'));
            $categoryModel = new Model_api_manageapp_Dbcategory();
            $storyModel = new Model_api_manageapp_Story();
            $storyContentModel = new Model_api_manageapp_StoryContent();
            $authorModel = new Model_api_manageapp_Author();

            // Bien thoi gian
            $time = date('Y-m-d H:i:s');

            // Lay danh sach story
            $listStory = $storyModel->getListStory();

            // Neu chua tao category thi phai tao
            if (!empty($listStory) && count($listStory)) {
                // Xu ly khi submit form
                if ($this->getRequest()->getMethod() == 'POST') {
                
                        // Kiem tra du lieu gui len
                        if (!empty($params['title']) && !empty($params['description'])
                            && !empty($params['content'])&& !empty($params['status'])
                            && !empty($params['story_id'])&& isset($params['content'])
                            && is_numeric($params['status'])
                        ) {
                    
                                $lastchapter = $storyModel->getStoryById($params['story_id']);
                                $chapter_number = $lastchapter['lastest_chapter'] + 1;
                                // Mang du lieu luu vao DB
                                $data = array(
                                    'title'             => $params['title'],
                                    'description'       => $params['description'],
                                    'key_words'         => !empty($params['key_words']) ? $params['key_words'] : null,
                                    'chapter_number'    => $chapter_number,
                                    'story_id'          => $params['story_id'],
                                    'created_time'      => $time,
                                    'updated_time'      => $time,
                                    'published_time'    => $time,
                                    'status'            => $params['status'],
                                );
                                $id = $storyContentModel->insert($data);                             
                                if($id){
                                    $data_update = array(
                                      'lastest_chapter' => $chapter_number,
                                      'updated_time'    => $time
                                    );
                                    $storyModel->update($data_update, 'id = ' . $params['story_id']);
                                }
                                $this->view->success = "Thêm mới thành công. " . $message;
                                $this->view->listStory = $listStory;
                        } else {
                            $this->view->warning = "Thiếu dữ liệu đầu vào";
                        }
                  
                }
          
             
                // Truyen bien ra view
                $this->view->data = $data;
                $this->view->listStory = $listStory;
            } else {
                $this->_redirect('/manageapp/story-content/add');
            }
        } catch (Exception $exc) {
            $this->view->error = $exc->getMessage();
        }

        // Tuy thuoc tung loai app ma co phan up noi dung rieng
    
           
       
    }
      // Action tra ve danh sach tag dang json
    public function categoryJsonAction()
    {
        try {
            $this->_helper->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender();

            // Chuan bi bien 
            $categoryModel = new Model_api_manageapp_Dbcategory();
            $listCategory = $categoryModel->getListForApp(array(
               
                'order'     => 'created_time'
            ));
            $data = array();
            foreach ($listCategory as $item) {
                $temp = array(
                    'category'       => $item['name'],
                  
                );
                $data[] = $temp;
            }
            $this->__showJson($data);
        } catch (Exception $e) {
            echo $e->getMessage();
            die;
        }
    }
    // Action thay doi trang thai chương
    public function changeStatusAction() {
        // Tat view va layout
        $this->_helper->viewRenderer->setNorender(true);
        $this->_helper->layout->disableLayout(true);
        try {            
            // Chuan bi tham so
            $time = date('Y-m-d H:i:s');
            $params = $this->_arrParam;
            $storyContentModel = new Model_api_manageapp_StoryContent();
            
            // Xu ly khi co request
            if ($this->_request->isPost()) {
                if (!empty($params['id']) && isset($params['status']) && is_numeric($params['id']) 
                    && is_numeric($params['status'])
                ) {
                    $where = $storyContentModel->getDefaultAdapter()->quoteInto("id = ?", $params['id']);
                    $data = array(
                        'status'        => $params['status'],
                        'updated_time'  => $time    
                    );
                    
                    $storyContentModel->update($data, $where);
                      
                    $response = array(
                        'status'    => 1,
                        'message'   => 'Thay đổi thành công'
                    );
                } else {
                    $response = array(
                        'status'    => 0,
                        'message'   => 'Thiếu tham số'
                    );
                }    
            } else {
                $response = array(
                    'status'    => 0,
                    'message'   => 'Phương thức không được hỗ trợ'
                );
            }
        } catch (Exception $exc) {
            $response = array(
                'status'    => 0,
                'message'   => 'Lỗi xử lý server'
            );
        }
        
        $this->__showJson($response);
    }
    public function getStoryContentAjaxAction()
    {
        // Tat layout, view
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        try {
            // Chuan bi tham so
            $params = $this->_request->getParams(); // Phai lay du lieu tho khong qua loc vi khi loc bi mat gia tri
            $storyModel = new Model_api_manageapp_Story();
            $categoryModel = new Model_api_manageapp_Dbcategory();
            $storyContentModel = new Model_api_manageapp_StoryContent();
            $data_response = array();
          
            $totaldata = 0;
            $recordsFiltered = 0;

            $search = $params['search'];
            $search_value = $search['value'];
            $start = intval($params['start']);
            $length = intval($params['length']);
            $story_id = $params['story_id'];

            // Lay thong tin sap xep
            
            $columns = array('', 'story_id', 'chapter_number','title','published_time');
            $orders = $params['order'];
            $order = $orders[0];
            $column_order = $order['column'];
            $column_order = $columns[$column_order];
            $dir_order = $order['dir'];
            $order = array(
                array(
                    'column_order'  => $column_order,
                    'dir_order'     => $dir_order
                )
            );

            // Lay du lieu status can filter
      
//            $status_search = "";
            
                $status_search = $params['columns'][5]['search']['value'];

            // Tao mang them so truy xuat du lieu
    		$data = array(    
                'story_id'        => $story_id,
                'search_value'	=> $search_value,
                'start'         => $start,
                'length'        => $length,
                'order'         => $order,
                'status_search' => $status_search,
                'total'         => 1
            );
            
            $result = $storyContentModel->getListStoryContent($data);
            $recordsFiltered = $result[0];
            $listContent = $result[1];
       
            $x = 1;
            foreach ($listContent as $value) {
            
                $story_name = $value['story_name'];
//                $category = $value['category_name'];
                $published_time = $value['published_time'];
                $chapter = $value['chapter_number'];
                $title = $value['title'];
                // Status
                if ($value['status'] == STATUS_ON) {
                    $status = "<span class='btn btn-success' _id='" . $value['id'] . "' onclick='changeStoryContentStatus(this, " . STATUS_OFF . ")'>ON</span>";
                } elseif ($value['status'] == STATUS_OFF) {
                    $status = "<span class='btn btn-warning' _id='" . $value['id'] . "' onclick='changeStoryContentStatus(this, " . STATUS_ON . ")'>OFF</span>";
                }  
                
                // Ghep cac truong
                $status_group = '<div class="status-group">Status: ' . $status . '</div>';
             

                $operation = "<div class='btn-group'>";           
                $operation .= "<a class='btn btn-success' href='/manageapp/story-content/edit?id=" . $value['id'] . "'>Chỉnh sửa</a>";
                $operation .= "<a class='btn btn-danger' onclick='deleteObject(" . $value['id'] . ", 6)' ><i></i>Xóa</a>";
                $operation .= "</div>";
               
                
                    $item = array("DT_RowId" => "row_" . $value['id'], $x, $story_name, $chapter, $title, $published_time, $status_group, $operation);
                
                array_push($data_response, $item);
                $x++;
            }

            $totaldata = sizeof($data_response);
            
    		$response = array(
                "draw"            => intval( $params['draw'] ),
                "recordsTotal"    => intval( $totaldata ),
                "recordsFiltered" => intval( $recordsFiltered ),
                "data"            => $data_response
            );
            //Zend_Debug::dump($response);die;
            $this->__showJson($response);
        } catch (Exception $e) {
            echo $e->getMessages();
        }
    }

    // Action sua bai post
    public function editAction()
    {
        try {
            // Load them file css, js
            $this->view->headScript()->appendFile("/assets/managerapp/js/jquery.validate.min.js");
            $this->view->headScript()->appendFile("/assets/managerapp/js/form-validation-script.js");
            $this->view->headScript()->appendFile('/assets/ckeditor/ckeditor.js');
            $this->view->headScript()->appendFile('/assets/ckfinder/ckfinder.js?v=3.3');
            $this->view->headScript()->appendFile("/assets/managerapp/js/handle-tag.js");

            /**************************** bootstrap tokenfield **************************/
            $this->view->headLink()->appendStylesheet('/assets/js/bootstrap-tokenfield/css/tokenfield-typeahead.css');
            $this->view->headLink()->appendStylesheet('/assets/js/bootstrap-tokenfield/css/bootstrap-tokenfield.css');
            $this->view->headScript()->appendFile('/assets/js/bootstrap-tokenfield/bootstrap-tokenfield.js');
            $this->view->headScript()->appendFile("/assets/js/bootstrap-tokenfield/typeahead.bundle.js");

            /**************************** jquery.auto-complete **************************/
            $this->view->headScript()->appendFile("/assets/plugins/jquery-autocomplete/jquery.auto-complete.js");
            $this->view->headLink()->appendStylesheet('/assets/plugins/jquery-autocomplete/jquery.auto-complete.css');


            // Chuan bi tham so
            $params = $this->_arrParam;
            $message = ''; // Bien luu thong tin can dua ra view
            $data = array();

            // Bo sung tham so content vi khi loc bi xoa di                        
            $params['content'] = trim($this->_request->getParam('content'));
            $categoryModel = new Model_api_manageapp_Dbcategory();
            $storyModel = new Model_api_manageapp_story();
            $storyContentModel = new Model_api_manageapp_StoryContent();
            // Bien thoi gian
            $time = date('Y-m-d H:i:s');

            // Lay danh sach story
            $listStory = $storyModel->getListStory();
          

            // Kiem tra id the loai neu la cap nhat
            if (!empty($params['id'])) {
                $storyContentInfo = $storyContentModel->getContentById($params['id']);
              
                if (!empty($storyContentInfo)) {
                    if ($storyContentInfo['status'] == STATUS_ON || $storyContentInfo['status'] == STATUS_OFF                        
                    ) {
                        // Xu ly khi submit form
                        if ($this->getRequest()->getMethod() == 'POST') {
                            // Tuy thuoc tung loai app ma co phan xu ly rieng
                            
                                // Kiem tra du lieu gui len
                                if (!empty($params['title']) && !empty($params['description'])
                                    && !empty($params['content'])&& !empty($params['status'])
                                    && isset($params['content'])&& is_numeric($params['status'])
                                ){
                             
                                    
                                    // Mang du lieu luu vao DB
                                    $data = array(
                                    'story_name'             => $params['story_name'],
                                    'description'            => $params['description'],
                                    'source'              => !empty($params['source']) ? $params['source'] : null,
                                    'key_words'              => !empty($params['key_words']) ? $params['key_words'] : null,
                                    'created_time'      => $time,
                                    'updated_time'      => $time,
                                    'published_time'    => $time,
                                    'status'            => $params['status'],
                                    'is_full'           => $params['is_full']
                                   
                                    );

                                    // Them cac du lieu cap nhat neu co
                                    // Neu khong, giu nguyen cac gia tri cu
                                   

                                    // Cap nhat
                                    $storyContentModel->update($data, 'id = ' . $params['id']);

                                    // Lay lai thong tin story
      
                                    $storyContentInfo = $storyContentModel->getContentById($params['id']);
                                    $data = $storyContentInfo;

                                    if (!empty($avatar_result['message'])) {
                                        $this->view->warning = $avatar_result['message'];
                                    }

                                    $this->view->success = "Cập nhật thành công. " . $message;
                                } else {
                                    $this->view->warning = "Thiếu dữ liệu đầu vào";
                                }
                        } else {
                            $data = $storyContentInfo;
                        }

                        // Truyen bien ra view
                        $this->view->data = $data;
                        $this->view->id = $params['id'];
                     
                    } else {
                        $this->view->warning = "Nội dung đang được xử lý, vui lòng quay lại sau";
                    }
                } else {
                    $this->view->warning = "Không tìm thấy dữ liệu";
                }
            } else {
                $this->view->warning = "Thiếu id bài post";
            }
        } catch (Exception $exc) {
            $this->view->error = $exc->getMessage();
        }
   
    }
 
    // Action thay doi gia tri các truong cua noi dung
    public function changeValueAction()
    {
       
        // Tat view va layout
        $this->_helper->viewRenderer->setNorender(true);
        $this->_helper->layout->disableLayout(true);
        try {
            // Chuan bi tham so
            $time = date('Y-m-d H:i:s');
            $params = $this->_arrParam;
            $storyModel = new Model_api_manageapp_Story();
         
           
            // Xu ly khi co request
            if ($this->_request->isPost()) {
                if (!empty($params['id']) && isset($params['status']) && !empty($params['type'])
                    && is_numeric($params['id']) && is_numeric($params['status']) && is_numeric($params['type'])
                ) {
                    $where = $storyModel->getDefaultAdapter()->quoteInto("id = ?", $params['id']);
                        $data = array(
                            'updated_time' => $time
                        );

                        switch ($params['type']) {
                            case 1:
                                $data['status'] = $params['status'];
                                break;
                            case 2:
                                $data['is_ads'] = $params['status'];
                                break;
                            case 3:
                                $data['is_only_detail'] = $params['status'];
                                break;
                            case 4:
                                $data['is_full'] = $params['status'];
                                break;
                            default:
                                $data = array();
                                break;
                        }

                        if (!empty($data)) {
                            $storyModel->update($data, $where);

                            $response = array(
                                'status'    => 1,
                                'message'   => 'Thay đổi thành công'
                            );
                        } else {
                            $response = array(
                                'status'    => 0,
                                'message'   => 'Dữ liệu không hợp lệ'
                            );
                        }
                    
                } else {
                    $response = array(
                        'status'    => 0,
                        'message'   => 'Thiếu tham số'
                    );
                }
            } else {
                $response = array(
                    'status'    => 0,
                    'message'   => 'Phương thức không được hỗ trợ'
                );
            }
        } catch (Exception $exc) {
            $response = array(
                'status'    => 0,
                'message'   => 'Lỗi xử lý server'
            );
        }

        $this->__showJson($response);
    }
    
   public function deleteAction()
    {
        // Tat view va layout
        $this->_helper->viewRenderer->setNorender(true);
        $this->_helper->layout->disableLayout(true);
        try {
            // Chuan bi tham so
            $params = $this->_arrParam;
            $image_to_delete = array();
            $storyModel = new Model_api_manageapp_Story();
      
            // Xu ly khi co request
            if ($this->_request->isPost()) {
                if (!empty($params['id'])) {
                    $where = "id = " . $params['id'];
               
                        // Kiem tra ton tai ban ghi
                        $story = $storyModel->fetchRow($where);
                        if (!empty($content)) {
                            $story = $story->toArray();
                            $storyModel->delete($where);

                            // Xoa anh tren storage
                            if (!empty($content['avatar_small']) && strpos($content['avatar_small'], NO_IMAGE_SMALL_LINK) !== 0) {
                                $image_to_delete[] = $content['avatar_small'];
                            }
                            if (!empty($content['avatar_medium']) && strpos($content['avatar_medium'], NO_IMAGE_MEDIUM_LINK) !== 0) {
                                $image_to_delete[] = $content['avatar_medium'];
                            }
                            if (!empty($content['avatar_large']) && strpos($content['avatar_large'], NO_IMAGE_LARGE_LINK) !== 0) {
                                $image_to_delete[] = $content['avatar_large'];
                            }

                            // Neu xoa du lieu thanh cong thi xoa luon anh tuong ung
                            if (!empty($image_to_delete) && count($image_to_delete)) {
                                foreach ($image_to_delete as  $value) {
                                    unlink($value);
                                }
                               
                            }

                            // Tao du lieu tra ve
                            $response = array(
                                'status'    => 1,
                                'message'   => 'Xóa thành công'
                            );
                        } else {
                            $response = array(
                                'status'    => 0,
                                'message'   => 'Không tồn tại bản ghi'
                            );
                        }
                 
                } else {
                    $response = array(
                        'status'    => 0,
                        'message'   => 'Thiếu tham số'
                    );
                }
            } else {
                $response = array(
                    'status'    => 0,
                    'message'   => 'Phương thức không được hỗ trợ'
                );
            }
        } catch (Exception $exc) {
            $response = array(
                'status'    => 0,
                'message'   => 'Lỗi xử lý server'
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
