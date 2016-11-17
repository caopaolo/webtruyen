<?php

class Manageapp_AuthorController extends Amobi_Controller_Action {

    protected $_layout = 'manageapp'; // Bien luu layout cua controller

    /**************************************** Begin: Author *******************************************/
    // Action show list danh sach tag 
    public function indexAction() {
        try {
                // Chuan bi tham so
            $AuthorModel = new Model_api_manageapp_Author();
            
            // Lay du lieu tu DB
            $data = $AuthorModel->getListAuthor();
            
            // Truyen bien ra view
            $this->view->data = $data;
        } catch (Exception $exc) {
            $this->view->error = $exc->getMessage();
        }
    }
    
    // Action tra ve danh sach bai tag cho loi goi ajax
    public function getTagAjaxAction(){
        // Tat layout, view
        $this->_helper->layout->disableLayout(true);
		$this->_helper->viewRenderer->setNoRender(true);
        try {            
            // Chuan bi tham so
    		$params = $this->_request->getParams(); // Phai lay du lieu tho khong qua loc vi khi loc bi mat gia tri
                $tagModel = new Model_api_manageapp_Tag();
    		$data_response = array();
    		$totaldata = 0;
    		$recordsFiltered = 0;
    
    		$search = $params['search'];
    		$search_value = $search['value'];
    		$start = intval($params['start']);
    		$length = intval($params['length']);
            
            // Lay thong tin sap xep
            $columns = array('', 'tag', 'tag_code', '', 'created_time');
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
            
            $status_search = $params['columns'][5]['search']['value'];

            // Tao mang them so truy xuat du lieu
    		$data = array(    
                    
    			'search_value'	=> $search_value,
    			'start'			=> $start,
    			'length'        => $length,
    			'order'         => $order,
                'status_search' => $status_search,
                'total'         => 1
    		);
    
    		$result = $tagModel->getList($data);
    		$recordsFiltered = $result[0];
    		$listTag = $result[1];
            $x = 1;
    		foreach ($listTag as $value) {
    			$tag = $value['tag'];
    			$tag_code = $value['tag_code'];
                $created_time = $value['created_time'];
                if (!empty($value['avatar_small'])) {
                    $avatar = "<a target='_blank' href='" . $value['avatar_small'] . "'>";
                    $avatar .= "<img class='avatar_small' src='" . $value['avatar_small'] . "' />";
                    $avatar .= "</a>";    
                } else {
                    $avatar = "<span></span>";
                }
                
                // Status
                if ($value['status'] == STATUS_ON) {
                    $status = "<span class='btn btn-success' _id='" . $value['id'] . "' onclick='changeTagStatus(this, " . STATUS_OFF . ")'>ON</span>";
                } elseif ($value['status'] == STATUS_OFF) {
                    $status = "<span class='btn btn-warning' _id='" . $value['id'] . "' onclick='changeTagStatus(this, " . STATUS_ON . ")'>OFF</span>";
                }
                
                // Operation
                $operation = "<div class='btn-group'>";
                $operation .= "<a class='btn btn-success' href='/manageapp/tag/edit?id=" . $value['id'] . "'>Chỉnh sửa</a>";
                $operation .= "<a class='btn btn-danger' onclick='deleteObject(" . $value['id'] . ", 5)' ><i></i>Xóa</a>";
                $operation .= "</div>";
                   
                $item = array("DT_RowId" => "row_" . $value['id'], $x, $tag, $tag_code, $avatar, $created_time, $status, $operation);
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
            $this->__showJson($response);
        } catch (Exception $e) {
            echo $e->getMessages();
        }
    }

    // Action them the loai cho ung dung
    public function addAction() {
        try {
            // Load them file css, js
            $this->view->headScript()->appendFile("/assets/managerapp/js/jquery.validate.min.js");
            $this->view->headScript()->appendFile("/assets/managerapp/js/form-validation-script.js");
    
            // Chuan bi tham so
            $params = $this->_arrParam;
            $authorModel = new Model_api_manageapp_Author();
            $time = date('Y-m-d H:i:s');
            $data = array();
            
            // Xu ly khi submit form
            if ($this->getRequest()->getMethod() == 'POST') {
                if (!empty($params['author'])) {
                    $author_code = Amobi_Utilities_Input::tenchuan($params['author']);
                    $check_author = $authorModel->getAuthor(array(
                        'author_code'  => $author_code
                       ));
                    // Kiem tra trung lap tag
                    if (empty($check_author)) {
                        // Thong bao thanh cong
                        $data = array(
                            'created_time'  => $time,
                            'author_name'      => $params['author'],
                            'author_code'      => $author_code,
                            'description'   => !empty($params['description']) ? $params['description'] : null,
                            'updated_time'  => $time
                        );
                        $authorModel->insert($data);
                        
                        $this->view->success = "Thêm mới thành công";
                    } else {
                        $data = $params;
                        $this->view->warning = "Tag đã được sử dụng";
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
    public function editAction() {
        try {
            // Load them file css, js
            $this->view->headScript()->appendFile("/assets/managerapp/js/jquery.validate.min.js");
            $this->view->headScript()->appendFile("/assets/managerapp/js/form-validation-script.js");
            
            // Chuan bi tham so
            $params = $this->_arrParam;
            $authorModel = new Model_api_manageapp_Author();
            $time = date('Y-m-d H:i:s');
            
            // Kiem tra id tac gia neu la cap nhat
            if (!empty($params['id'])) {
                $author = $authorModel->getAuthor(array(
                    'id'        => $params['id'], 
                ));
                if (!empty($author)) {
                    // Xu ly khi submit form
                    if ($this->getRequest()->getMethod() == 'POST') {
                        if (!empty($params['author'])) {
                            // Kiem tra trung lap tag
                            $author_code = Amobi_Utilities_Input::tenchuan($params['author']);
                            $check_author = $authorModel->getAuthor(array(
                                'author_code'  => $author_code, 
                                'except_id' => $params['id']));
                            if (empty($check_author)) {                                
                                // Cap nhat
                                $data = array(
                                    'author_name'      => $params['author'],
                                    'author_code'      => $author_code,
                                    'description'      => !empty($params['description']) ? $params['description'] : null,
                                    'updated_time'     => $time
                                );
                                
                                // Cap nhat
                                $where = 'id = ' . $params['id'];
                                $authorModel->update($data, $where);
                                                                
                                // Lay lai thong tin
                                $author = $authorModel->getAuthor(array(
                                    'id'        => $params['id'], 
                                   ));
                                $data = $author;
                                                            
                                // Thong bao thanh cong
                                $this->view->success = "Cập nhật thành công";
                            } else {
                                $data = $author;
                                $this->view->warning = "Tag đã được sử dụng";
                            }
                        } else {
                            $data = $author;
                            $this->view->warning = "Thiếu dữ liệu truyền lên";
                        }
                    } else {
                        $data = $author;
                    }
                    // Truyen bien ra view
                    $this->view->data = $data;
                } else {
                    $this->view->warning = "Không tìm thấy dữ liệu";    
                }
            } else {
                $this->view->warning = "Thiếu id tag";
            }
        } catch (Exception $exc) {
            $this->view->error = $exc->getMessage();
        }
    }

    // Action xoa the loai cua ung dung
    public function deleteAction() {
        // Tat view va layout
        $this->_helper->viewRenderer->setNorender(true);
        $this->_helper->layout->disableLayout(true);
        try {
            // Chuan bi tham so
            $params = $this->_arrParam;
            $image_to_delete = array();
            $authorModel = new Model_api_manageapp_Author();
            
            // Xu ly khi co request
            if ($this->_request->isPost()) {
                if (!empty($params['id'])) {
                    // Kiem tra ton tai ban ghi
                    $author = $authorModel->getAuthor(array(
                        'id'        => $params['id'], 
                        ));
                    if (!empty($author)) {
                        $where = "id = " . $params['id'];
                        $authorModel->delete($where);
                        
                        
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
    
    // Action thay doi trang thai tag
    public function changeStatusAction() {
        // Tat view va layout
        $this->_helper->viewRenderer->setNorender(true);
        $this->_helper->layout->disableLayout(true);
        try {            
            // Chuan bi tham so
            $time = date('Y-m-d H:i:s');
            $params = $this->_arrParam;
            $tagModel = new Model_api_manageapp_Tag();
            
            // Xu ly khi co request
            if ($this->_request->isPost()) {
                if (!empty($params['id']) && isset($params['status']) && is_numeric($params['id']) 
                    && is_numeric($params['status'])
                ) {
                    $where = $tagModel->getDefaultAdapter()->quoteInto("id = ?", $params['id']);
                    $data = array(
                        'status'        => $params['status'],
                        'updated_time'  => $time    
                    );
                    
                    $tagModel->update($data, $where);
                      
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
    
    /**************************************** End: Tag *******************************************/
    
    
    /******************************************** Begin: Cac ham ho tro *******************************************/
    
    
    
    /******************************************** End: Cac ham ho tro *******************************************/
}
