<?php

class Manageapp_StoryController extends Amobi_Controller_Action
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
            $params = $this->_arrParam;
            if (!empty($params['author_id'])) {
                $author_id = $params['author_id'];
            } else {
                $author_id = null;
            }
            if(!empty($params['category_id'])){
                $category_id = $params['category_id'];
            }else{
                $category_id = null;
            }
            if(!empty($params['category_new_id'])){
                $category_new_id = $params['category_new_id'];
            }else{
                $category_new_id = null;
            }
          
            if (!empty($params['warning'])) {
                $this->view->warning = $params['warning'];
            }

            // Truyen bien ra view
            $this->view->author_id = $author_id;
            $this->view->category_id = $category_id;
            $this->view->category_new_id = $category_new_id;
          
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
           
            $authorModel = new Model_api_manageapp_Author();

            // Bien thoi gian
            $time = date('Y-m-d H:i:s');

            // Lay danh sach author
            $listAuthor = $authorModel->getListAuthor();

            // Neu chua tao category thi phai tao
            if (!empty($listAuthor) && count($listAuthor)) {
                // Xu ly khi submit form
                if ($this->getRequest()->getMethod() == 'POST') {
           
                        // Kiem tra du lieu gui len
                        if (!empty($params['story_name']) && !empty($_FILES['avatar']) && !empty($params['avatar_description'])
                            && !empty($params['description']) && !empty($params['description']) && !empty($params['author'])
                            && !empty($params['category'])&& !empty($params['introduction']) && isset($params['status'])
                            && isset($params['is_full']) && is_numeric($params['status'])
                            && is_numeric($params['is_full']) 
                        ) {
                            // Xu ly neu co avatar up len
                            if (!empty($_FILES['avatar']) && empty($_FILES['avatar']['error'])
                            ) {
                                $file = $_FILES['avatar'];
                                if ($file['name']) { // Neu ten file khac rong
                                    $avatar_result = Amobi_Utilities_Image::handleImage(FOLDER_AVA_STORY, REMOTE_FOLDER_AVA_STORY, $file['tmp_name'], '', 1);

                                   
                                    if ($avatar_result['status'] == 1) {
                                        $params['avatar_large'] = $avatar_result['data']['image_large'];
                                        $params['avatar_medium'] = $avatar_result['data']['image_medium'];
                                        $params['avatar_small'] = $avatar_result['data']['image_small'];
                                    }
                                }
                            }

                            if (!empty($params['avatar_small'])) {
                                // Mang du lieu luu vao DB
                                $data = array(
                                 
                                    'story_name'             => $params['story_name'],
                                    'avatar_small'           => $params['avatar_small'],
                                    'avatar_medium'          => $params['avatar_medium'],
                                    'avatar_large'           => $params['avatar_large'],
                                    'avatar_description'      => $params['avatar_description'],
                                    'description'            => $params['description'],
                                    'introduction'           => $params['introduction'],
                                    'author_id'              => $params['author'],
                                    'source'              => !empty($params['source']) ? $params['source'] : null,
                                    'key_words'              => !empty($params['key_words']) ? $params['key_words'] : null,
                                    'total_chapter'              => !empty($params['total_chapter']) ? $params['total_chapter'] : null,
                                    'created_time'      => $time,
                                    'updated_time'      => $time,
                                    'published_time'    => $time,
                                    'status'            => $params['status'],
                                
                                    'is_full'           => $params['is_full']
                                   
                                );
                                $id = $storyModel->insert($data);
                              
                                // Xu ly tag
                                if (!empty($params['category'])) {
                                    $resultHandleCategory = Amobi_Utilities_Category::handle($id, $params['category']);

                                    if ($resultHandleCategory['status'] == 0) {
                                        $message = $resultHandleCategory['message'];
                                    }
                                }

                                $this->view->success = "Thêm mới thành công. " . $message;
                            } else {
                                $this->view->warning = !empty($avatar_result['message']) ? $avatar_result['message'] : "Lỗi xử lý avatar";
                            }
                        } else {
                            $this->view->warning = "Thiếu dữ liệu đầu vào";
                        }
                  
                }
          
             
                // Truyen bien ra view
                $this->view->data = $data;
                $this->view->listAuthor = $listAuthor;
            } else {
                $this->_redirect('/manageapp/category/add');
            }
        } catch (Exception $exc) {
            $this->view->error = $exc->getMessage();
        }

        // Tuy thuoc tung loai app ma co phan up noi dung rieng
    
           
       
    }
       // Action sap xep danh sach content  
    public function sortAction()
    {
        try {
            // Load them file css, js
            $this->view->headScript()->appendFile("/assets/managerapp/js/jquery-ui-1.9.2.custom.min.js");
            $this->view->headScript()->appendFile("/assets/plugins/jquery-autocomplete/jquery.auto-complete.js");
            $this->view->headLink()->appendStylesheet('/assets/plugins/jquery-autocomplete/jquery.auto-complete.css');
            $this->view->headScript()->appendFile("/assets/managerapp/js/sort-content.js");

            // Chuan bi tham so
            $storyModel = new Model_api_manageapp_Story();
            $params = $this->_arrParam;
            // Tao mang them so truy xuat du lieu
            $order = array(
                array(
                    'column_order'  => 'sort_focus',
                    'dir_order'     => 'desc'
                ),
                array(
                    'column_order'  => 'published_time',
                    'dir_order'     => 'desc'
                )
            );
            $data = array(    
    			'start'          => 0,
    			'length'         => 50,
    			'order'          => $order
    		);
            // Lay du lieu trong DB
            $listStory = $storyModel->getListStory($data);

            // Truyen bien ra view
            $this->view->listStory = $listStory;
        } catch (Exception $exc) {
            $this->view->error = $exc->getMessage();
        }
    }
       // Action xu ly reset thu tu sap xep content theo muc do noi bat
    public function resetSortStoryAction()
    {
        try {
            // Chuan bi tham so
            $storyModel = new Model_api_manageapp_Story();

            if ($this->_request->isPost()) {
                $data = array(
                    'sort_focus'    => 0,
                    'updated_time'  => date('Y-m-d H:i:s')
                );
                $storyModel->update($data, 1);

                // Lay danh sach content da sap xep
                // Tao mang them so truy xuat du lieu
                $order = array(
                    array(
                        'column_order'  => 'sort_focus',
                        'dir_order'     => 'desc'
                    ),
                    array(
                        'column_order'  => 'published_time',
                        'dir_order'     => 'desc'
                    )
                );
                $data = array(    
        			'start'          => 0,
        			'length'         => 100,
        			'order'          => $order
        		);
                
                // Lay du lieu trong DB
                $listStory = $storyModel->getListStory($data);

                // tao du lieu tra ve
                $result = array('status' => 1, 'data' => $listStory);
            } else {
                // tao du lieu tra ve
                $result = array('status' => 0, 'message' => 'Phương thức truyền dữ liệu ko được hỗ trợ');
            }
        } catch (Exception $e) {
            // tao du lieu tra ve
            $result = array('status' => 0, 'message' => 'Đã xảy ra lỗi');
        }
        exit(json_encode($result));
    }
    // Action tim kiem story
    public function searchAction()
    {
        try {
            $this->_helper->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender();

            // Chuan bi bien 
            $storyModel = new Model_api_manageapp_Story();
            $params = $this->_arrParam;
            if (!empty($params['q'])) {
               
                // Tao mang them so truy xuat du lieu
                $order = array(
                    array(
                        'column_order'  => 'published_time',
                        'dir_order'     => 'desc'
                    )
                );
                $status_search = null;
                $data = array(
                    'search_value' 	=> $params['q'],
                    'order' 		=> $order,
                    'status_search'     => $status_search
                );

                $listStory = $storyModel->getListStory($data);
              
                $data = array();
                foreach ($listStory as $item) {
                    $temp = array(
                        'id'            => $item['id'],
                        'sort_focus'    => $item['sort_focus'],
                        'avatar_small'  => $item['avatar_small'],
                        'story_name'         => $item['story_name']
                    );
                    $data[] = $temp;
                }
                $this->__showJson($data);
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            die;
        }
    }
    // Action xu ly sap xep content theo muc do noi bat
    public function sortContentAction()
    {
        try {
            // Chuan bi tham so
            $storyModel = new Model_api_manageapp_Story();
            $params = $this->_request->getParams(); // Phai lay du lieu goc vi khi filter bi loai bo

            if ($this->_request->isPost()) {
                if (!empty($params['data'])) {
                    // Reset sort 
                    $data = array(
                        'sort_focus'    => 0,
                        'updated_time'  => date('Y-m-d H:i:s')
                    );
                    
                    $storyModel->update($data, 1);
                    $arrayData = array_reverse($params['data']);
                   
                    for ($i=0; $i<count($arrayData); $i++) {
                        $arrayInfo = explode('-', $arrayData[$i]);
                        $id = $arrayInfo[1];
                        $data = array(
                            'sort_focus'    => $i+1,
                            'updated_time'  => date('Y-m-d H:i:s')
                        );
                        $storyModel->update($data, 'id=' . $id);
                    }

                        // Lay danh sach content da sap xep
                        // Tao mang them so truy xuat du lieu
                    $order = array(
                        array(
                            'column_order'  => 'sort_focus',
                            'dir_order'     => 'desc'
                        ),
                        array(
                            'column_order'  => 'published_time',
                            'dir_order'     => 'desc'
                        )
                    );
                    $data = array(    
                                
            			'start'          => 0,
            			'length'         => 100,
            			'order'          => $order
            		);
                    
                    // Lay du lieu trong DB
                    $listStory = $storyModel->getListStory($data);

                    // tao du lieu tra ve
                    $result = array('status' => 1, 'data' => $listStory);
                } else {
                    // tao du lieu tra ve
                    $result = array('status' => 0, 'message' => 'Thiếu tham số');
                }
            } else {
                // tao du lieu tra ve
                $result = array('status' => 0, 'message' => 'Phương thức truyền dữ liệu ko được hỗ trợ');
            }
        } catch (Exception $e) {
            // tao du lieu tra ve
            $result = array('status' => 0, 'message' => 'Đã xảy ra lỗi');
        }
        exit(json_encode($result));
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
    public function getStoryAjaxAction()
    {
        // Tat layout, view
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        try {
            
            
            // Chuan bi tham so
            $params = $this->_request->getParams(); // Phai lay du lieu tho khong qua loc vi khi loc bi mat gia tri
            $storyModel = new Model_api_manageapp_Story();
            $categoryModel = new Model_api_manageapp_Dbcategory();
            $data_response = array();
          
            $totaldata = 0;
            $recordsFiltered = 0;

            $search = $params['search'];
            $search_value = $search['value'];
            $start = intval($params['start']);
            $length = intval($params['length']);
            
            // Lay thong tin sap xep
            
            $columns = array('', 'story_name', '','','published_time');
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
            
                $status_search = $params['columns'][7]['search']['value'];

            // Tao mang them so truy xuat du lieu
    		$data = array(    
                'author'        => !empty($params['author_id']) ? $params['author_id'] : null,
                'category_id'   => !empty($params['category_id']) ? $params['category_id'] : null,
                'category_new_id'   => !empty($params['category_new_id']) ? $params['category_new_id'] : null,
                'search_value'	=> $search_value,
                'start'         => $start,
                'length'        => $length,
                'order'         => $order,
                'status_search' => $status_search,
                'total'         => 1
            );
            
            $result = $storyModel->getListStory($data);
            $recordsFiltered = $result[0];
            $listContent = $result[1];
           
            $x = 1;
            foreach ($listContent as $value) {

                $story_name = '<a href="/manageapp/story-content/index?story_id='.$value['id'].'">'.$value['story_name'].'</a>';
         
//                $category = $value['category_name'];
                $published_time = $value['published_time'];
                if (!empty($value['avatar_small'])) {
                    $avatar = "<a target='_blank' href='"  .HOME_URL. $value['avatar_small'] . "'>";
                    $avatar .= "<img class='avatar_small' src='" .HOME_URL. $value['avatar_small'] . "' />";
                    $avatar .= "</a>";
                } else {
                    $avatar = "<span></span>";
                }

                // Status
                if ($value['status'] == STATUS_ON) {
                    $status = "<span class='btn btn-success' _id='" . $value['id'] . "' onclick='changeValue(this, " . STATUS_OFF . ", 1)'>ON</span>";
                } elseif ($value['status'] == STATUS_OFF) {
                    $status = "<span class='btn btn-warning' _id='" . $value['id'] . "' onclick='changeValue(this, " . STATUS_ON . ", 1)'>OFF</span>";
                }  
                // Is_ads
                if ($value['is_full'] == 1) {
                    $is_full = "<span class='btn btn-success' _id='" . $value['id'] . "' onclick='changeValue(this, " . 0 . ", 4)'>TRUE</span>";
                } else if ($value['is_full'] == 0) {
                    $is_full = "<span class='btn btn-warning' _id='" . $value['id'] . "' onclick='changeValue(this, " . 1 . ", 4)'>FALSE</span>";
                }
                // Ghep cac truong
                $status_group = '<div class="status-group">Status: ' . $status . '</div>';
                $status_group .= '<div class="status-group">FULL: ' . $is_full . '</div>';

                $operation = "<div class='btn-group'>";           
                $operation .= "<a class='btn btn-success' href='/manageapp/story/edit?id=" . $value['id'] . "'>Chỉnh sửa</a>";
                $operation .= "<a class='btn btn-danger' onclick='deleteObject(" . $value['id'] . ", 1)' ><i></i>Xóa</a>";
                $operation .= "</div>";
                $listCategory = $storyModel->getCategoryList($value['id']);
                if (!empty($listCategory)) {
                          $category = '';
                          foreach ($listCategory as $item) {
                              $category .= $item['category_name'] . ',';
                          }
                          $category = trim($category, ',');
                      } else {
                          $category = null;
                      }
                
                    $item = array("DT_RowId" => "row_" . $value['id'], $x, $story_name, $value['author_name'], $category, $published_time, $avatar,$value['lastest_chapter'].'/'.$value['total_chapter'], $status_group, $operation);
                
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
            $authorModel = new Model_api_manageapp_Author();
            // Bien thoi gian
            $time = date('Y-m-d H:i:s');
              // Lay danh sach author
            $listAuthor = $authorModel->getListAuthor();

            // Kiem tra id the loai neu la cap nhat
            if (!empty($params['id'])) {
                $storyInfo = $storyModel->getStoryById($params['id']);
                if (!empty($storyInfo)) {
                    if ($storyInfo['status'] == STATUS_ON || $storyInfo['status'] == STATUS_OFF                        
                    ) {
                        // Xu ly khi submit form
                        if ($this->getRequest()->getMethod() == 'POST') {
                            // Tuy thuoc tung loai app ma co phan xu ly rieng
                            
                                // Kiem tra du lieu gui len
                                 if (!empty($params['story_name']) && !empty($_FILES['avatar']) && !empty($params['avatar_description'])
                                    && !empty($params['description']) && !empty($params['description']) && !empty($params['author'])
                                    && !empty($params['category'])&& !empty($params['introduction']) && isset($params['status'])
                                    && isset($params['is_full']) && is_numeric($params['status'])
                                    && is_numeric($params['is_full']) 
                                ) {
                                    // Xu ly neu co avatar up len
                                    if (!empty($_FILES['avatar']) && empty($_FILES['avatar']['error'])
                                    ) {
                                        $file = $_FILES['avatar'];
                                        if ($file['name']) { // Neu ten file khac rong
                                            $avatar_result = Amobi_Utilities_Image::handleImage(FOLDER_AVA_STORY, REMOTE_FOLDER_AVA_STORY, $file['tmp_name'], '', 1);


                                            if ($avatar_result['status'] == 1) {
                                                $params['avatar_large'] = $avatar_result['data']['image_large'];
                                                $params['avatar_medium'] = $avatar_result['data']['image_medium'];
                                                $params['avatar_small'] = $avatar_result['data']['image_small'];
                                            }
                                        }
                                    }

                                    
                                    // Mang du lieu luu vao DB
                                    $data = array(
                                 
                                    'story_name'             => $params['story_name'],
                                    'avatar_description'      => $params['avatar_description'],
                                    'description'            => $params['description'],
                                    'introduction'           => $params['introduction'],
                                    'author_id'              => $params['author'],
                                    'source'              => !empty($params['source']) ? $params['source'] : null,
                                    'key_words'              => !empty($params['key_words']) ? $params['key_words'] : null,
                                    'total_chapter'              => !empty($params['total_chapter']) ? $params['total_chapter'] : null,
                                    'created_time'      => $time,
                                    'updated_time'      => $time,
                                    'published_time'    => $time,
                                    'status'            => $params['status'],
                                    'is_full'           => $params['is_full']
                                   
                                    );

                                    // Them cac du lieu cap nhat neu co
                                    // Neu khong, giu nguyen cac gia tri cu
                                    if (!empty($params['avatar_small'])) {
                                        $data['avatar_small'] = $params['avatar_small'];

                                        if (!empty($params['avatar_medium'])) {
                                            $data['avatar_medium'] = $params['avatar_medium'];
                                        } else {
                                            $data['avatar_medium'] = null;
                                        }

                                        if (!empty($params['avatar_large'])) {
                                            $data['avatar_large'] = $params['avatar_large'];
                                        } else {
                                            $data['avatar_large'] = null;
                                        }

                                        // Tao image de xoa
                                        $image_to_delete = array();
                                        if (!empty($contentInfo['avatar_small']) && $contentInfo['avatar_small'] != NO_IMAGE_SMALL_LINK) {
                                            $image_to_delete[] = $contentInfo['avatar_small'];
                                        }
                                        if (!empty($contentInfo['avatar_medium']) && $contentInfo['avatar_medium'] != NO_IMAGE_MEDIUM_LINK) {
                                            $image_to_delete[] = $contentInfo['avatar_medium'];
                                        }
                                        if (!empty($contentInfo['avatar_large']) && $contentInfo['avatar_large'] != NO_IMAGE_LARGE_LINK) {
                                            $image_to_delete[] = $contentInfo['avatar_large'];
                                        }
                                    }

                                    // Cap nhat
                                    $storyModel->update($data, 'id = ' . $params['id']);

                                    // Xu ly tag
                                    if (!empty($params['category'])) {
                                        $resultHandleTag = Amobi_Utilities_Category::handle($params['id'], $params['category']);

                                        if ($resultHandleTag['status'] == 0) {
                                            $message = $resultHandleTag['message'];
                                        }
                                    }

                                    // Neu cap nhat thanh cong thi xoa anh cu
                                    if (!empty($image_to_delete) && count($image_to_delete)) {
                                     
                                        foreach ($image_to_delete as $value) {
                                            unlink($value);
                                        }
                                    }

                                    // Lay lai thong tin story
      
                                    $storyInfo = $storyModel->getStoryById($params['id']);
                                    $data = $storyInfo;

                                    if (!empty($avatar_result['message'])) {
                                        $this->view->warning = $avatar_result['message'];
                                    }

                                    $this->view->success = "Cập nhật thành công. " . $message;
                                } else {
                                    $this->view->warning = "Thiếu dữ liệu đầu vào";
                                }
                        } else {
                            $data = $storyInfo;
                        }

                        // Tao truong category tra ve
                        $array = array(
                            'story_id' => $data['id']
                        );
                       
                        $category_array = $categoryModel->getListForStory($array);
                        if (!empty($category_array)) {
                            $category = '';
                            foreach ($category_array as $item) {
                                $category .= $item['name'] . ',';
                            }
                            $category = trim($category, ',');
                        } else {
                            $category = null;
                        }
                        $data['category'] = $category;

                        
                        // Truyen bien ra view
                        $this->view->data = $data;
                        $this->view->id = $params['id'];
                        $this->view->listAuthor = $listAuthor;
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
                    
                        if (!empty($story)) {
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
