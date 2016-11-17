<?php
/*
* Function for import data from external
* Author: TTL
* Create time: 17/06/2016 17:12
* Update time: 17/06/2016 17:12
*/
class Amobi_Utilities_Import{
    
    // Ham lay du lieu cho bizlive
    public function getDataForBizlive($data, $user_id, $type) {
        try {
            // Chuan bi tham so            
            $message = '';
            $count = count($data);
            $i = $count;
            $success = 0;
            
            foreach ($data as $item) {
                // Tao message
                $temp_message = '';
                
                // Lay content
                $content = file_get_contents("http://bizlive.vn/utilities/amobi.ashx?code=35-l*y-ske_iop&type=detail&id=" . $item['ID']);
                
                if (!empty($content)) {
                    $temp = array(                    
                        'level'             => $item['Level'],
                        'title'             => $item['Title'],
                        'avatardescription' => $item['AvatarDescription'],
                        'description'       => $item['Sapo'],
                        'content'           => $content,
                        'post_id'           => $item['ID'],
                        'link'              => $item['Link'],
                        'author'            => $item['Author'],
                        'created_time'      => $item['Date'],
                        'published_time'    => $item['Date'],
                        'category'          => $item['Category'],
                        'avatar'            => $item['Avatar']
                    );
                    
                    $getDataResult = $this->__getData($temp, BIZLIVE_APP_ID, $type, $i, $user_id);
                    
                    if ($getDataResult['status'] == 1) {
                        $success++;
                        $i = $i - 1;    
                    }
                    
                    if (!empty($getDataResult['message'])) {
                        $temp_message .= $getDataResult['message'] . '; ';
                    }
                } else {
                    $temp_message .= 'Không có nội dung; ';
                }
                if (!empty($temp_message)) {
                    $message .= 'Post with ID: ' . $item['ID'] . ' ' . $temp_message . '<br>';
                }
            }
            
            $response = array (
                'status'    => 1,
                'total'     => $count,
                'success'   => $success,
                'message'   => $message               
            );
        } catch (Exception $e) {
            $response = array (
                'status'    => 0,
                'message'   => 'Get data for app ' . BIZLIVE_APP_ID . ', type ' . $type . ' failed. Reason: '. $e->getMessage()               
            );
        }
        return $response;
    }
    
    // Ham lay du lieu cho tiepthitieudung
    public function getDataForTttd ($data, $user_id, $type) {
        try {
            // Chuan bi tham so
            $message = '';
            $count = count($data);
            $i = $count;
            $success = 0;
            
            foreach ($data as $item) {                
                $temp = array(                    
                    'level'             => $item['level'],
                    'title'             => $item['title'],
                    'avatardescription' => $item['avatardescription'],
                    'description'       => $item['description'],
                    'content'           => $item['content'],
                    'post_id'           => $item['post_id'],
                    'link'              => $item['link'],
                    'author'            => $item['author'],
                    'created_time'      => $item['created_time'],
                    'published_time'    => $item['date'],
                    'category'          => $item['category'],
                    'tag'               => $item['tags'],
                    'num_view'          => $item['view'],
                    'num_like'          => $item['num_like'],
                    'avatar'            => $item['avatar']
                );
                
                $getDataResult = $this->__getData($temp, TTTD_APP_ID, $type, $i, $user_id);
                
                if ($getDataResult['status'] == 1) {
                    $success++;
                    $i = $i - 1;    
                }
                
                if (!empty($getDataResult['message'])) {
                    $message .= 'Post with ID: ' . $item['post_id'] . ' ' . $getDataResult['message'] . '<br>';
                }
            }
            
            $response = array (
                'status'    => 1,
                'total'     => $count,
                'success'   => $success,
                'message'   => $message               
            );
        } catch (Exception $e) {
            $response = array (
                'status'    => 0,
                'message'   => 'Get data for app ' . TTTD_APP_ID . ', type ' . $type . ' failed. Reason: '. $e->getMessage()               
            );
        }
        return $response;
    }
    
    // Ham lay du lieu cho vtc news
    public function getDataForVtcNews ($data, $user_id, $type) {
        try {
            // Chuan bi tham so
            $message = '';
            $count = count($data);
            $i = $count;
            $success = 0;
            
            foreach ($data as $item) {                
                $temp = array(                    
                    'level'             => 0,
                    'title'             => $item['title'],
                    'avatardescription' => $item['title'],
                    'description'       => $item['description'],
                    'content'           => $item['content'],
                    'post_id'           => $item['post_id'],
                    'link'              => $item['link'],
                    'author'            => $item['author'],
                    'created_time'      => $item['created_time'],
                    'published_time'    => $item['published_time'],
                    'category'          => $item['categorie'],
                    'tag'               => $item['tag'],
                    'num_view'          => $item['views'],
                    'avatar'            => $item['avatar']
                );
                
                $getDataResult = $this->__getData($temp, VTC_NEWS_APP_ID, $type, $i, $user_id);
                if ($getDataResult['status'] == 1) {
                    $success++;
                    $i = $i - 1;    
                }
                
                if (!empty($getDataResult['message'])) {
                    $message .= 'Post with ID: ' . $item['post_id'] . ' ' . $getDataResult['message'] . '<br>';
                }
            }
            
            $response = array (
                'status'    => 1,
                'total'     => $count,
                'success'   => $success,
                'message'   => $message               
            );
        } catch (Exception $e) {
            $response = array (
                'status'    => 0,
                'message'   => 'Get data for app ' . VTC_NEWS_APP_ID . ', type ' . $type . ' failed. Reason: '. $e->getMessage()               
            );
        }
        return $response;
    }
    
    // Ham lay du lieu cho ThongTanXa
    public function getDataForThongTanXa($data, $type) {
        try {
            // Chuan bi tham so
            $message = '';
            $count = count($data);
            $i = $count;
            $success = 0;
            
            foreach ($data as $item) {                
                $temp = array(                    
                    'level'             => 1,
                    'title'             => $item['title'],
                    'avatardescription' => $item['title'],
                    'description'       => $item['description'],
                    'content'           => $item['description'],
                    'post_id'           => $item['post_id'],
                    'link'              => $item['link_page'],
                    'author'            => 'ThongTanXa',
                    'created_time'      => $item['time'],
                    'published_time'    => $item['time'],
                    'category'          => $item['category'],
                    'avatar'            => $item['img'],
                    'link_video'        => $item['link_video']
                );
                
                $getDataResult = $this->__getData($temp, THONG_TAN_XA_APP_ID, $type, $i);
                
                if ($getDataResult['status'] == 1) {
                    $success++;
                    $i = $i - 1;    
                }
                
                if (!empty($getDataResult['message'])) {
                    $message .= 'Post with ID: ' . $item['post_id'] . ' ' . $getDataResult['message'] . '<br>';
                }
            }
            
            $response = array (
                'status'    => 1,
                'total'     => $count,
                'success'   => $success,
                'message'   => $message               
            );
        } catch (Exception $e) {
            $response = array (
                'status'    => 0,
                'message'   => 'Get data for app ' . THONG_TAN_XA_APP_ID . ', type ' . $type . ' failed. Reason: '. $e->getMessage()               
            );
        }
        return $response;
    }
    
    // Ham lay du lieu cho ThieuNien
    public function getDataForThieuNien($data, $type) {
        try {
            // Chuan bi tham so
            $message = '';
            $count = count($data);
            $i = $count;
            $success = 0;
            
            foreach ($data as $item) {                
                $temp = array(                    
                    'level'             => 1,
                    'title'             => $item['title'],
                    'avatardescription' => $item['title'],
                    'description'       => $item['description'],
                    'content'           => $item['content'],
                    'post_id'           => $item['post_id'],
                    'link'              => $item['link_page'],
                    'author'            => $item['author'],
                    'created_time'      => $item['time'],
                    'published_time'    => $item['time'],
                    'category'          => $item['category'],
                    'avatar'            => $item['img'],
                    'tag'               => $item['tags']
                );
                
                $getDataResult = $this->__getData($temp, THIEU_NIEN_APP_ID, $type, $i);
                
                if ($getDataResult['status'] == 1) {
                    $success++;
                    $i = $i - 1;    
                }
                
                if (!empty($getDataResult['message'])) {
                    $message .= 'Post with ID: ' . $item['post_id'] . ' ' . $getDataResult['message'] . '<br>';
                }
            }
            
            $response = array (
                'status'    => 1,
                'total'     => $count,
                'success'   => $success,
                'message'   => $message               
            );
        } catch (Exception $e) {
            $response = array (
                'status'    => 0,
                'message'   => 'Get data for app ' . THIEU_NIEN_APP_ID . ', type ' . $type . ' failed. Reason: '. $e->getMessage()               
            );
        }
        return $response;
    }
    
    // Ham thuc hien viec reset du lieu cho cac trang ngoai
    public function resetData($app_id, $type) {
        try {
            // Load model can thiet
            $contentModel = new Model_api_manageapp_Dbcontent();
            
            // Tuy thuoc loai du lieu muon lay ma cau hinh tham so tuong ung
            switch ($type) {
                case 1: // Reset sap xep noi bat
                    $data = array(
                        'sort_focus'    => 0,
                        'updated_time'  => date('Y-m-d H:i:s')
                    );
                    break;
                case 2: // Reset sap xep xem nhieu
                    $data = array(
                        'sort_topview'  => 0,
                        'updated_time'  => date('Y-m-d H:i:s')
                    );
                    break;
                case 3: // Reset sap xep xem nhieu va noi bat
                    $data = array(
                        'sort_focus'    => 0,
                        'sort_topview'  => 0,
                        'updated_time'  => date('Y-m-d H:i:s')
                    );
                    break;
                default: // Reset sap xep noi bat
                    $data = array();
                    break;
            }
            if (count($data)) {
                $where = "app_id = '" . $app_id . "'";
                $contentModel->update($data, $where);
                $response = array (
                    'status'    => 1,
                    'message'   => 'Reset data for app ' . $app_id . ', type ' . $type . ' successfully'               
                );
            } else {
                $response = array (
                    'status'    => 0,
                    'message'   => 'Reset data for app ' . $app_id . ', type ' . $type . ' failed. Reason: type invalid'               
                );       
            }
        } catch (Exception $e) {
            $response = array (
                'status'    => 0,
                'message'   => 'Reset data for app ' . $app_id . ', type ' . $type . ' failed. Reason: '. $e->getMessage()               
            );
        }
        return $response;
    }
    
    // Ham lay du lieu cho phohay.com
    public function getDataForPhoHay($data, $user_id) {
        try {
            // Chuan bi tham so
            $message = '';
            $count = count($data);
            $success = 0;
            
            foreach ($data as $item) {                
                // Khoi tao model
                $contentModel = new Model_api_manageapp_Dbcontent();
                
                // Khoi tao lai de tranh cap nhat anh cua bai cu
                $avatar_large = '';
                $avatar_medium = '';
                $avatar_small = '';
                $temp_message = '';
                // Kiem tra bai post xem da ton tai chua?
                // Neu da ton tai thi bo qua, neu khong thi insert them
                $where = array();
                $where[] = "app_id = '" . PHO_HAY_COM_APP_ID . "'";
                $where[] = "post_id = " . $item['post_id'];
                $old_content = $contentModel->fetchRow($where);
                
                if (empty($old_content)) { // Neu khong ton tai moi xet tiep
                    // Tao category tu category tra ve
                    switch ($item['category']) {
                        case 2: 
                            $category = 15; 
                            break;
                        case 3:
                            $category = 16;
                            break;
                        case 5:
                            $category = 17;
                            break;
                        case 6:
                            $category = 18;
                            break;  
                        default :
                            $category = 0;
                            break;
                    }
                    
                    // Filter
                    $content = trim(html_entity_decode($item['content'], ENT_QUOTES, 'UTF-8'));
                    $content = Normalizer::normalize($content);
                    $content = str_replace('src="/', 'src="http://phohay.com/', $content);
                    $content = str_replace('src="../../../../../', 'src="http://phohay.com/', $content);
                    $description = !empty($item['description']) ? Normalizer::normalize(trim(strip_tags(html_entity_decode($item['description'], ENT_QUOTES, 'UTF-8')))) : null;
                    $title = Normalizer::normalize(trim(strip_tags(html_entity_decode($item['title'], ENT_QUOTES, 'UTF-8'))));
            
                    $temp = array(
                        'app_id'            => PHO_HAY_COM_APP_ID,
                        'post_id'           => $item['post_id'],
                        'title'             => $title,
                        'avatardescription' => $title,
                        'description'       => $description,
                        'content'           => $content,
                        'link'              => $item['url'],
                        'author'            => 'phohay.com',
                        'created_time'      => $item['created_time'],
                        'updated_time'      => $item['created_time'],
                        'published_time'    => $item['created_time'],
                        'category'          => $category
                    );
                    
                    // Tao anh cho bai viet
                    $getAvaResult = $this->__getAvatar(PHO_HAY_COM_APP_ID, $item['post_img']);
                    
                    if ($getAvaResult['status'] == 1) {
                        $temp['avatar_large'] = $getAvaResult['data']['avatar_large'];
                        $temp['avatar_medium'] = $getAvaResult['data']['avatar_medium'];
                        $temp['avatar_small'] = $getAvaResult['data']['avatar_small'];
                    }
                    
                    if (!empty($getAvaResult['message'])) {
                        $temp_message .= $getAvaResult['message'] . '; ';
                    }
                    
                    // Insert noi dung
                    $result = $contentModel->insert($temp);
                    
                    // Xu ly tag
                    if (!empty($item['tag'])) {
                        $resultHandleTag = Amobi_Utilities_Tag::handle($result, $item['tag'], PHO_HAY_COM_APP_ID);
                        
                        if ($resultHandleTag['status'] == 0) {
                            $temp_message .= $resultHandleTag['message'] . '; ';
                        }
                    }
                    
                    // Neu bao duoc cau hinh tao TTS thi tao TTS luon cho bao
                    if (!empty($user_id)) {
                        // Tao audio tu dong
                        $content = Amobi_Utilities_Input::filterStringForTTS($temp['content']);
                        
                        // Tao call back
                        $call_back_url = MAIN_HOST . '/call-back/tts?content_id=' . $result;
                        
                        // Tao mang tham so
                        $array = array(
                            'id'                => $result,
                            'title'             => $temp['title'],
                            'description'       => $temp['description'],
                            'content'           => $content,
                            'call_back_url'     => $call_back_url,
                            'call_back_method'  => 'PUT'
                        );
                        
                        // Xu ly tao voice
                        $response = Amobi_Utilities_Audio::createAudio($array, $user_id);
                        
                        if ($response['status'] == 1) {
                            $data = array(
                                'resource_url'          => $response['data']['resource_url'],
                                'updated_time'          => date('Y-m-d H:i:s')
                            );
                            
                            // Cap nhat DB
                            $where = 'id = ' . $result;
                            $contentModel->update($data, $where);    
                        } else {
                            $temp_message .= 'Lỗi tạo voice thất bại. Lý do: ' . $response['message'] . '; ';
                        }
                    }
                    
                    if ($result) {
                        $success++;
                    } else {
                        $temp_message .= 'Cập nhật không thành công; ';
                    }
                } else {
                    $temp_message .= 'Đã tồn tại; ';
                    $success++;
                }
                if (!empty($temp_message)) {
                    $message .= 'Post with ID: ' . $item['post_id'] . ' ' . $temp_message . '<br>';
                }
                
                // Huy ket noi
                $db = Zend_Db_Table::getDefaultAdapter();         
                $db->closeConnection();
            }  
            
            $response = array (
                'status'    => 1,
                'total'     => $count,
                'success'   => $success,
                'message'   => $message               
            );
        } catch (Exception $e) {
            $response = array (
                'status'    => 0,
                'message'   => 'Get data for app ' . PHO_HAY_COM_APP_ID . ' failed. Reason: '. $e->getMessage()               
            );
        }
        return $response;
    }
    
    // Ham cap nhat bai hot cho phohay.com
    public function updateHotForPhoHay($data) {
        try {
            // Chuan bi tham so
            $contentModel = new Model_api_manageapp_Dbcontent();
            $message = '';
            $focus = $count = count($data);
            $success = 0;
            
            // Reset sort_focus
            $where = array();
            $where[] = "app_id = '" . PHO_HAY_COM_APP_ID . "'";
            $array = array(
                'sort_focus'    => 0,
                'updated_time'  => date('Y-m-d H:i:s')
            );
            $contentModel->update($array, $where);
            
            foreach ($data as $item) {
                $temp = array(
                    'sort_focus'    => $focus,
                    'updated_time'  => date('Y-m-d H:i:s')
                );
                
                $where = array();
                $where[] = "app_id = '" . PHO_HAY_COM_APP_ID . "'";
                $where[] = "post_id = " . $item['post_id'];
                
                $result = $contentModel->update($temp, $where);
                
                if (!empty($result)) {
                    $success++;
                }
                $focus--;
            }
            
            $response = array (
                'status'    => 1,
                'total'     => $count,
                'success'   => $success,
                'message'   => $message               
            );
        } catch (Exception $e) {
            $response = array (
                'status'    => 0,
                'message'   => 'Update data hot for app ' . PHO_HAY_COM_APP_ID . ' failed. Reason: '. $e->getMessage()               
            );
        }
        return $response;
    }
    
    // Ham lay thong tin chi tiet video cho TTX
    public function getDetailVideoForTTX($url) {
        // Khoi ta bien
        $data = array();
        
        // Lay thong tin post_id
        preg_match('~(.+)\-(\d+)\.html~i', $url, $tmp_array);
        $data['post_id'] = $tmp_array[2];
        
        $html = file_get_html($url);
        // Lay thong tin des
        $data['description'] = $html->find("meta#descriptionID", 0)->content;
        
        // Lay thong tin title, category, time
        $wrapper = $html->find('div.vn-box-video-playing', 0);
        $data['title'] = $wrapper->find('div.title', 0)->plaintext;
        $data['category'] = $wrapper->find('a._category', 0)->plaintext;
        $data['time'] = $wrapper->find('span.time', 0)->plaintext;
        
        // Lay thong tin video
        $video_wrapper = $wrapper->find('div.player', 0)->outertext;
        preg_match('~\{file:\s*(\'|\")((.+?).mp4)(\'|\")\}~i', $video_wrapper, $tmp_video);
        $data['link_video'] = $tmp_video[2];
        
        preg_match('~image:\s*(\'|\")(.+?)(\'|\")\,~i', $video_wrapper, $tmp_image);
        $data['img'] = $tmp_image[2];
        
        // Xoa doi tuong
        $html->clear();
        unset($html);
        
        // Tra lai du lieu
        if (!empty($data['post_id']) && !empty($data['title']) && !empty($data['description'])
            && !empty($data['category']) && !empty($data['time']) && !empty($data['img'])    
        ) {
            return $data;    
        } else {
            return false;
        }
    }
    
    // Ham lay thong tin chi tiet bai viet cho Thieu Nien
    public function getDetailPostForThieuNien($url) {
        // Khoi ta bien
        $data = array();
        
        // Lay thong tin post_id
        preg_match('~http\:\/\/thieunien\.vn\/(\d+)\-(.+)\/(\d+)\-(.+)\.html~i', $url, $tmp_array);
        $data['category'] = $tmp_array[1];
        $data['post_id'] = $tmp_array[3];
        $html = file_get_html($url);
        
        // Lay thong tin avatar
        $avatar = '';
        $meta_array = $html->find('meta');
        foreach ($meta_array as $item) {
            if ($item->property == 'og:image' || $item->itemprop == 'image') {
                $avatar = $item->content;
            }
        }
        $data['img'] = $avatar;
        
        // Lay thong tin title
        $data['title'] = trim($html->find("div.news-detail-name", 0)->plaintext);
        $data['time'] = trim($html->find("div.news-detail-calendar", 0)->plaintext);
        $data['description'] = trim($html->find("div.bb-news-detail-meta", 0)->find("div.bb-description", 0)->find("blockquote", 0)->plaintext);
        
        $temp_content = $html->find("div.news-detail-content", 0)->childNodes();
        $content = '';
        $tags = '';
        $author = '';
        
        // Dao nguoc mang
        $temp_content = array_reverse($temp_content);
        
        // Lay tag
        if ($temp_content[0]->find('a', 0) && preg_match("~http\:\/\/thieunien\.vn\/search\.html\?keyword\=(.+)~", $temp_content[0]->find('a', 0)->href)) {
            $tags_array = $temp_content[0]->find('a');
            foreach ($tags_array as $item) {
                $tags .= trim($item->plaintext) . ',';
            } 
        }
        $tags = trim($tags, ',');
        $data['tags'] = $tags;
        unset($temp_content[0]);
        
        // Lay author
        foreach ($temp_content as $item) {
            if ($item->class == 'the-article-body') { // Neu co lop the-article-body thi tac gia nam trong do
                $temp_array = array_reverse($item->childNodes());
                foreach ($temp_array as $node) {
                    if ($node->style == "text-align: right;") {
                        $author .= trim($node->plaintext) . ' - ';
                        $node->outertext = '';
                        break;
                    } 
                }
            } else {
                if ($item->style == "text-align: right;") {
                    $author .= trim($item->plaintext) . ' - ';
                    $item->outertext = '';
                    break;
                }    
            }
        }
        $author = trim ($author, ' - ');
        $data['author'] = $author;
        
        // Lay content
        foreach (array_reverse($temp_content) as $item) {
            $img_array = $item->find('img');
            foreach ($img_array as $img) {
                if (!preg_match("~https?\:\/\/(.+)~", $img->src)) {
                    $img->src = 'http://thieunien.vn' . $img->src;
                }
            }
            $content .= $item->outertext;
        }
        $data['content'] = $content;
        
        // Xoa doi tuong
        $html->clear();
        unset($html);
        
        if (empty($data['author'])) {
            $data['author'] = 'thieunien.vn';
        }
        
        // Tra lai du lieu
        if (!empty($data['category']) && !empty($data['post_id']) && !empty($data['img']) 
            && !empty($data['title']) && !empty($data['time']) && !empty($data['description']) 
            && !empty($data['author']) && !empty($data['content'])
        ) {
            return $data;    
        } else {
            return false;
        }
    }
    
    // Ham chuyen doi thoi gian cho TTX
    public function convertTimeForTTX($time) {
        $time = preg_replace('/\s+/', '', $time);
        $arr_time1 = explode('-', $time);
        $d_m_y = explode('/', $arr_time1[1]);
        $y_m_d = $d_m_y [2] . '-' . $d_m_y [1] . '-' . $d_m_y[0];
        $H_i_s = $arr_time1 [0] . ':00';
        $time_c = $y_m_d . ' ' . $H_i_s;
        return $time_c;
    }
    
    /******************************************** Begin: Cac ham ho tro *******************************************/
    
    // Ham lay du lieu noi chung
    private function __getData ($item, $app_id, $type, $i, $user_id=null) {
        // Khoi tao chuoi message
        $temp_message = '';
        try {            
            // Khoi tao lai de tranh cap nhat anh cua bai cu
            $avatar_large = '';
            $avatar_medium = '';
            $avatar_small = '';
            
            // Tao category tu category tra ve
            $category_id = $this->__getCategoryFromText($app_id, $item['category'], $item['link']);
            
            // Kiem tra neu thuoc cac category ho tro thi moi lay
            if (!empty($category_id)) {
                // Tao level 
                switch ($item['level']) {
                    case 'Bình thường':
                        $level = 1;
                        break;
                    case 'Nổi bật':
                        $level = 2;
                        break;
                    case 'Tiêu điểm':
                        $level = 3;
                        break;
                    default:
                        $level = 1;
                        break;
                }
                
                $content = trim(html_entity_decode($item['content'], ENT_QUOTES, 'UTF-8'));
                $content = Normalizer::normalize($content);
                $description = !empty($item['description']) ? Normalizer::normalize(trim(strip_tags(html_entity_decode($item['description'], ENT_QUOTES, 'UTF-8')))) : null;
                $title = Normalizer::normalize(trim(strip_tags(html_entity_decode($item['title'], ENT_QUOTES, 'UTF-8'))));
                $avatardescription = !empty($item['avatardescription']) ? Normalizer::normalize(trim(strip_tags(html_entity_decode($item['avatardescription'], ENT_QUOTES, 'UTF-8')))) : $title;
                
                // Sua phan video cho vtcnews
                if ($app_id == VTC_NEWS_APP_ID) {
                    $content = preg_replace('~\<iframe ([^>]*) height=\"450\" ([^>]*)\>\<\/iframe\>~', '<iframe $1 height="350" $2></iframe>', $content);
                }
                
                $temp = array(
                    'app_id'            => $app_id,
                    'post_id'           => $item['post_id'],
                    'title'             => $title,
                    'avatardescription' => $avatardescription,
                    'description'       => $description,
                    'content'           => $content,
                    'link'              => $item['link'],
                    'author'            => $item['author'],
                    'created_time'      => $item['created_time'],
                    'updated_time'      => date('Y-m-d H:i:s'),
                    'published_time'    => $item['published_time'],
                    'category'          => $category_id,
                    'category_extend'   => $item['category'],
                    'level'             => $level,
                    'original_avatar'   => $item['avatar']
                );
                
                // Neu co link_video thi kiem tra
                // va them du lieu video
                if (!empty($item['link_video'])) {
                    $temp['original_video'] = $item['link_video'];
                }
                
                // Tuy thuoc loai du lieu ma tao truong tuong ung
                if ($type == 1) { // Bai trang chu
                    $temp['sort_focus'] = $i;
                } else if ($type == 2) { // Bai top view
                    $temp['sort_topview'] = $i;
                } else if ($type == 3) { // Vua bai trang chu, vua bai top view
                    $temp['sort_focus'] = $i;
                    $temp['sort_topview'] = $i;
                }
                
                // Khoi tao model
                $contentModel = new Model_api_manageapp_Dbcontent();
                
                // Lay thong tin bai post
                $old_content = $contentModel->detailPost($app_id, $item['post_id'], 0, null, 0);
                
                // Mang chua danh sach anh cu can xoa
                $image_to_delete = array();
                
                // Neu da ton tai thi cap nhat thong tin
                // va xoa anh cu
                // Neu chua ton tai thi them moi
                if (!empty($old_content)) {
                    // Kiem tra neu anh moi trung anh cu thi giu nguyen
                    // Neu khong thi thay doi
                    if ($item['avatar'] != $old_content['original_avatar']) {
                        // Tao anh cho bai viet
                        $getAvaResult = $this->__getAvatar($app_id, $item['avatar']);
                        
                        if ($getAvaResult['status'] == 1) {
                            $temp['avatar_large'] = $getAvaResult['data']['avatar_large'];
                            $temp['avatar_medium'] = $getAvaResult['data']['avatar_medium'];
                            $temp['avatar_small'] = $getAvaResult['data']['avatar_small'];
                            
                            // Them anh can xoa
                            if (!empty($old_content['avatar_small']) && $old_content['avatar_small'] != NO_IMAGE_SMALL_LINK) {
                                $image_to_delete[] = $old_content['avatar_small'];
                            }
                            if (!empty($old_content['avatar_medium']) && $old_content['avatar_medium'] != NO_IMAGE_MEDIUM_LINK) {
                                $image_to_delete[] = $old_content['avatar_medium'];
                            }
                            if (!empty($old_content['avatar_large']) && $old_content['avatar_large'] != NO_IMAGE_LARGE_LINK) {
                                $image_to_delete[] = $old_content['avatar_large'];
                            }
                        }
                        
                        if (!empty($getAvaResult['message'])) {
                            $temp_message .= $getAvaResult['message'] . '; ';
                        }
                    }
                    
                    // Neu co video va video la moi thi phai cap nhat lai
                    if (!empty($item['link_video']) && $item['link_video'] != $old_content['original_video'] 
                        && $old_content['status'] == STATUS_ON
                    ) {
                        $temp['status'] = STATUS_FILE_NEED_DOWN;
                    }
                    
                    $where = "id = " . $old_content['id'];
                    
                    // Cap nhat vao CSDL
                    $result = $contentModel->update($temp, $where);
                        
                    // Xu ly tag
                    if (!empty($item['tag'])) {
                        $resultHandleTag = Amobi_Utilities_Tag::handle($old_content['id'], $item['tag'], $app_id);
                        
                        if ($resultHandleTag['status'] == 0) {
                            $temp_message .= $resultHandleTag['message'] . '; ';
                        }
                    }
                    
                    $insert = 0; // Bien danh dau la update noi dung 
                } else {
                    // Tao anh cho bai viet
                    $getAvaResult = $this->__getAvatar($app_id, $item['avatar']);
                    
                    if ($getAvaResult['status'] == 1) {
                        $temp['avatar_large'] = $getAvaResult['data']['avatar_large'];
                        $temp['avatar_medium'] = $getAvaResult['data']['avatar_medium'];
                        $temp['avatar_small'] = $getAvaResult['data']['avatar_small'];
                    }
                    
                    if (!empty($getAvaResult['message'])) {
                        $temp_message .= $getAvaResult['message'] . '; ';
                    }
                    
                    if ($type == 0) {
                        // Cap nhat avatar cho category
                        // Khoi tao model
                        $categoryModel = new Model_api_manageapp_Dbcategory();
                        $category = $categoryModel->getDetailCategory($category_id, $app_id);
                        if (!empty($category)) {                            
                            $result_copy = $this->__copyAvaFromContentToCategory($temp);
                            
                            if ($result_copy['status'] == 1) {
                                $category_data = $result_copy['data'];
                                if (!empty($category['avatar_small']) && $category['avatar_small'] != NO_IMAGE_SMALL_LINK) {
                                    $image_to_delete[] = $category['avatar_small'];
                                }
                                if (!empty($category['avatar_medium']) && $category['avatar_medium'] != NO_IMAGE_MEDIUM_LINK) {
                                    $image_to_delete[] = $category['avatar_medium'];
                                }
                                if (!empty($category['avatar_large']) && $category['avatar_large'] != NO_IMAGE_LARGE_LINK) {
                                    $image_to_delete[] = $category['avatar_large'];
                                }
                                if (!empty($category_data)) {
                                    $where = 'id = ' . $category_id;
                                    $categoryModel->update($category_data, $where);    
                                }         
                            }                            
                        }
                    }
                    
                    // Dat trang thai can down video
                    if (!empty($item['link_video'])) {
                        $temp['status'] = STATUS_FILE_NEED_DOWN;
                    }
                    
                    // Neu la them moi thi moi su dung tham so luot view va like
                    $temp['num_view'] = !empty($item['num_view']) ? $item['num_view'] : 0;
                    $temp['num_like'] = !empty($item['num_like']) ? $item['num_like'] : 0;
                    
                    // Them vao CSDL
                    $result = $contentModel->insert($temp);   
                    
                    // Xu ly tag
                    if (!empty($item['tag'])) {
                        $resultHandleTag = Amobi_Utilities_Tag::handle($result, $item['tag'], $app_id);
                        
                        if ($resultHandleTag['status'] == 0) {
                            $temp_message .= $resultHandleTag['message'] . '; ';
                        }
                    }
                    
                    $insert = 1; // Bien danh dau la insert them noi dung                            
                }
                
                // Neu ket qua tra ve la rong thi nghia la
                // khong update duoc, cung ko insert duoc,
                // bao loi ra. Neu khac rong thi giam chi so di 1
                if (!empty($result)) {
                    // Neu la insert va bao duoc cau hinh tao TTS thi tao TTS luon cho bao
                    if (!empty($insert) && !empty($user_id)) {
                        // Tao audio tu dong
                        $content = Amobi_Utilities_Input::filterStringForTTS($temp['content']);
                        
                        // Tao call back
                        $call_back_url = MAIN_HOST . '/call-back/tts?content_id=' . $result;
                        
                        // Tao mang tham so
                        $array = array(
                            'id'                => $result,
                            'title'             => $temp['title'],
                            'description'       => $temp['description'],
                            'content'           => $content,
                            'call_back_url'     => $call_back_url,
                            'call_back_method'  => 'PUT'
                        );
                        
                        // Xu ly tao voice
                        $response = Amobi_Utilities_Audio::createAudio($array, $user_id);
                        
                        if ($response['status'] == 1) {
                            $data = array(
                                'resource_url'          => $response['data']['resource_url'],
                                'updated_time'          => date('Y-m-d H:i:s')
                            );
                            
                            // Cap nhat DB
                            $where = 'id = ' . $result;
                            $contentModel->update($data, $where); 
                        } else {
                            $temp_message .= 'Lỗi tạo voice thất bại. Lý do: ' . $response['message'] . '; ';
                        }
                    }
                    
                    // Neu co anh can xoa thi xoa
                    if (!empty($image_to_delete) && count($image_to_delete)) {
                        // Xoa file tren Resource Server
                        $result_del = $file_object->deleteMulti($image_to_delete);
                        if ($result_del['status'] == 1) {
                            if ($result_del['data']['success'] != $result_del['data']['total']) {
                                $temp_message .= 'Lỗi xóa file cũ. Tỷ lệ: ' . $result_del['data']['success'] . '/' . $result_del['data']['total'] . '; ';
                            }
                        } else {
                            $temp_message .= 'Lỗi xóa file cũ trên Resource Server; ';
                        }
                    }
                    
                    // Tao du lieu tra ve
                    $response = array(
                        'status'    => 1,
                        'message'   => $temp_message
                    );
                } else {
                    $temp_message .= 'Cập nhật không thành công; '; 
                    
                    // Tao du lieu tra ve
                    $response = array(
                        'status'    => 0,
                        'message'   => $temp_message
                    );   
                }
                // Huy ket noi
                $db = Zend_Db_Table::getDefaultAdapter();     
                $db->closeConnection(); 
            } else {
                $temp_message .= 'Có category ' . $item['category'] . ' không thuộc category nào; ';
                // Tao du lieu tra ve
                $response = array(
                    'status'    => 0,
                    'message'   => $temp_message
                );
            }
        } catch (Exception $e) {
            $temp_message .= $e->getMessage() . '; ' ;
            // Tao du lieu tra ve
            $response = array(
                'status'    => 0,
                'message'   => $temp_message
            );
        }
        return $response;
    }
    
    // Ham lay avatar
    private function __getAvatar ($app_id, $avatar) {
        // Khoi tao chuoi message
        $temp_message = '';
        try {
            $image = Amobi_Utilities_Url::standardImageUrl($avatar);
            if (!empty($image)) {
                $resultAvatar = Amobi_Utilities_Image::getImageFromLink($image, TMP_FOLDER_AVA_CONTENT);
                if ($resultAvatar['status'] == 1) {   
                    $avatar_result = Amobi_Utilities_Image::handleImage(FOLDER_AVA_CONTENT, REMOTE_FOLDER_AVA_CONTENT , $resultAvatar['url'], $app_id, '', 1);
                    if ($avatar_result['status'] == 1) {
                        $avatar_large = $avatar_result['data']['image_large'];
                        $avatar_medium = $avatar_result['data']['image_medium'];
                        $avatar_small = $avatar_result['data']['image_small'];
                    } else {
                        $temp_message .= $image . ': ' . $avatar_result['message'] . '; ';    
                    }
                } else {
                    $temp_message .= $image . ': ' . $resultAvatar['message'] . '; ';
                }    
            } else {
                $temp_message .= 'Không lấy được ảnh; ';
            }
            
            $temp = array();
            if (!empty($avatar_small)) {
                $temp['avatar_large'] = !empty($avatar_large) ? $avatar_large : null;
                $temp['avatar_medium'] = !empty($avatar_medium) ? $avatar_medium : null;
                $temp['avatar_small'] = $avatar_small;
            } else {
                $temp['avatar_large'] = NO_IMAGE_LARGE_LINK;
                $temp['avatar_medium'] = NO_IMAGE_MEDIUM_LINK;
                $temp['avatar_small'] = NO_IMAGE_SMALL_LINK;
            }
            // Tao du lieu tra ve
            $response = array(
                'status'    => 1,
                'data'      => $temp,
                'message'   => $temp_message
            );
        } catch (Exception $e) {
            $temp_message .= $e->getMessage() . '; ' ;
            
            // Tao du lieu tra ve
            $response = array(
                'status'    => 0,
                'message'   => $temp_message
            );
        }
        return $response;
    }
    
    // Ham chuyen doi category_name tu doi tac tra ve thanh category_id tren he thong content
    private function __getCategoryFromText ($app_id, $category_name, $link) {
        $arraySubCategory = array(
            BIZLIVE_APP_ID => array(
                '26'    => array('Thời sự', 'Chính trị - Xã hội', 'Hàng hóa - Nguyên liệu', 'Kinh tế - Đầu tư'),
                '27'    => array('Bảo hiểm', 'Vàng - Tiền', 'Ngân hàng', 'Chứng khoán', 'Tài chính'),
                '28'    => array('Quy hoạch', 'Địa ốc', 'Dự án', 'Thị trường', 'Nhà đẹp'),
                '29'    => array('Thương trường', 'Doanh nghiệp', 'Câu chuyện kinh doanh', 'Kinh doanh'),
                '30'    => array('Thế giới', 'Kinh doanh quốc tế', 'Thời sự'),
                '31'    => array('BizLIFE', 'Sống', 'Nhân vật', 'BizTALK'),
                '32'    => array('Thiết bị số', 'Phương tiện', 'Công nghệ', 'Nội dung số'),
                '33'    => array('Nhịp cầu doanh nghiệp'),
                '34'    => array('24/7', 'Zoom In', 'BizNEWS', 'CEO Chat', 'Don’t Miss', 'In-depth', 'Feedback'), 
                '35'    => array('Tuyển dụng')
            ),
            TTTD_APP_ID => array(
                '77'    => array('Thị trường', 'Hàng thật & Hàng giả', 'Tư vấn tiêu dùng'),
                '78'    => array('Tài chính', 'Giá vàng', 'Chứng khoán', 'Doanh nghiệp', 'Doanh nhân'),
                '79'    => array('Bất động sản', 'Thông tin dự án', 'Chính sách', 'Không gian đẹp'),
                '80'    => array('Thời trang', 'Bí quyết mặc đẹp', 'Thời trang sao'),
                '81'    => array('Làm đẹp', 'Bí quyết đẹp', 'Spa thư giãn'),
                '82'    => array('Sức khỏe', 'Mẹ và bé', 'Sức khỏe gia đình', 'Sức khỏe sinh sản', 'Bí quyết sống khỏe'),
                '83'    => array('Ẩm thực', 'Món ngon nhà hàng', 'Món ngon gia đình', 'Bí quyết nấu ăn'),
                '84'    => array('Du lịch', 'Trong nước', 'Quốc tế', 'Tour du lịch'),
                '85'    => array('Đời sống', 'Hài Hước', 'Nhật ký Cafe Sáng', 'Phi Thường'), 
                '86'    => array('Công nghệ', 'Điện thoại', 'Máy tính bảng', 'Kinh nghiệm-Thủ thuật'),
                '87'    => array('Giáo dục', 'Du học', 'Nuôi dạy con'),
                '88'    => array('Xe 360', 'Ô tô', 'Ô tô tải', 'Xe máy', 'Phương tiện khác'),
                '89'    => array('Video hot'),
                '90'    => array('Thể thao', 'Hậu trường', 'Cách chơi'),
                '91'    => array('Giải trí', 'Việt Nam', 'Quốc tế'),
                '92'    => array('Khuyến mại', 'Sản phẩm', 'Dịch vụ')
            ),
            THONG_TAN_XA_APP_ID => array(
                '94'    => array('Tin tức tổng hợp'),
                '95'    => array('Bản tin thời sự'),
                '96'    => array('Lăng kính phóng viên'),
                '97'    => array('Văn hóa toàn cảnh'),
                '98'    => array('Hành tinh thể thao'),
                '99'    => array('Thông điệp lịch sử'),
                '100'   => array('Tiêu điểm kinh tế'),
                '101'   => array('Nhịp cầu cộng đồng'),
                '102'   => array('Thế giới 360 độ'), 
                '103'   => array('Kinh tế thế giới'),
                '104'   => array('Không gian pháp ngữ'),
                '105'   => array('Truyền hình công đoàn'),
                '106'   => array('Y tế và Cuộc sống', 'Câu chuyện sức khỏe'),
                '107'   => array('Đồng hành cùng Thông tấn'),
                '108'   => array('Văn hóa giao thông'),
                '109'   => array('Biên giới biển đảo quê hương'),
                '110'   => array('Giáo dục và Đào tạo'),
                '111'   => array('Nhật trình kinh tế'),
                '112'   => array('Cộng đồng Asean'),
                '113'   => array('Người dân và Chính phủ'),
                '114'   => array('Khoa học và Công nghệ'),
                '115'   => array('Dư luận thế giới về Việt Nam'),
                '116'   => array('News in English'),
                '117'   => array('Le Journal Hebdomadaire'),
                '118'   => array('Noticiero Semanal'),
                '119'   => array('越通社新闻'),
                '147'   => array('Chính phủ điện tử'),
                '187'   => array('Góc ngày mới')
            ),
            VTC_NEWS_APP_ID => array(
                '120'   => array('27', '38', '39', '40', '41', '42', '43', '44', '45', '46'),
                '121'   => array('28', '47', '48', '75'),
                '122'   => array('29', '50', '51', '52', '83'),
                '123'   => array('30', '49', '53', '54', '55'),
                '124'   => array('31', '56', '57', ''),
                '125'   => array('32', '58', '59'),
                '126'   => array('33', '60', '61', '62', '63', '64'),
                '127'   => array('34', '65', '66'),
                '128'   => array('35', '67', '68', '69'), 
                '129'   => array('36', '70', '71'),
                '130'   => array('37', '72', '73'),
                '148'   => array('94'),
                '149'   => array('82'),
                '151'   => array('74'),
                '154'   => array('78', '79'),
                '172'   => array('96')
            ),
            THIEU_NIEN_APP_ID => array(
                '131'   => array('30', '31'),
                '132'   => array('33', '34', '35', '36', '37'),
                '133'   => array('39', '40', '41', '42', '43'),
                '134'   => array('72', '73', '74', '75'),
                '135'   => array('45', '46', '47', '48', '49'),
                '136'   => array('61', '66', '67', '70'),
                '137'   => array('51', '52'),
                '138'   => array('55', '56'),
                '139'   => array('58', '59', '60'), 
                '140'   => array('62', '63', '64')
            )
        );
        $category = null;
        // Xet truong hop nhap nhang
        if ($category_name == 'Thời sự' && $app_id == BIZLIVE_APP_ID) {
            if (substr($link, 18, 16) == 'thoi-su-the-gioi') {
                $category = 30;
            } else {
                $category = 26;
            }
        } else if ($category_name == 'Quốc tế' && $app_id == TTTD_APP_ID) {
            if (strpos($link, 'http://tttd.vn/giai-tri-quoc-te') === 0) {
                $category = 91;
            } else {
                $category = 84;
            }
        } else {
            foreach ($arraySubCategory[$app_id] as $key=>$value) {
                if (in_array($category_name, $value)) {
                    $category = $key;
                    // Neu tim duoc category roi thi dung
                    break;
                }
            }    
        }
        return $category;
    }
    
    // Ham chuyen anh tu content sang category
    private function __copyAvaFromContentToCategory ($params) {
        try {
            // Khoi tao bien
            $search_partern = 'image/content/content/';
            $replace_partern = 'image/content/category/';
            $file_object = new Amobi_Utilities_File();
            $partern = '~' . preg_quote(RESOURCES_HOST) . '(.*)~';
            $data = array();            
            
            
            if (!empty($params['avatar_large'])) {
                // Chuyen anh sang thu muc category
                if (preg_match($partern, $params['avatar_large'], $result)) {
                    $file_uri = $result[1];
                    $new_file_uri = str_replace($search_partern, $replace_partern, $file_uri);
                    
                    $result_copy = $file_object->copy($new_file_uri, $file_uri);
                    if ($result_copy['status'] == 1) {
                        $avatar_large = $result_copy['data']['url'];
                    }
                }  
            }
            
            if (!empty($params['avatar_medium'])) {
                // Chuyen anh sang thu muc category
                if (preg_match($partern, $params['avatar_medium'], $result)) {
                    $file_uri = $result[1];
                    $new_file_uri = str_replace($search_partern, $replace_partern, $file_uri);
                    
                    $result_copy = $file_object->copy($new_file_uri, $file_uri);
                    if ($result_copy['status'] == 1) {
                        $avatar_medium = $result_copy['data']['url'];
                    }
                }
            }
            
            if (!empty($params['avatar_small'])) {
                // Chuyen anh sang thu muc category
                if (preg_match($partern, $params['avatar_small'], $result)) {
                    $file_uri = $result[1];
                    $new_file_uri = str_replace($search_partern, $replace_partern, $file_uri);
                    
                    $result_copy = $file_object->copy($new_file_uri, $file_uri);
                    if ($result_copy['status'] == 1) {
                        $avatar_small = $result_copy['data']['url'];
                    }
                }
            }
            
            if (!empty($avatar_small)) {
                $data['avatar_large'] = !empty($avatar_large) ? $avatar_large : null;
                $data['avatar_medium'] = !empty($avatar_medium) ? $avatar_medium : null;
                $data['avatar_small'] = $avatar_small;
            } else {
                $data['avatar_large'] = NO_IMAGE_LARGE_LINK;
                $data['avatar_medium'] = NO_IMAGE_MEDIUM_LINK;
                $data['avatar_small'] = NO_IMAGE_SMALL_LINK;
            }
            
            $response = array(
                'status'    => 1,
                'data'      => $data
            );
        } catch (Exception $e) {
            $response = array(
                'status'    => 0,
                'message'   => $e->getMessage()
            );
        }
        return $response;
    }
    
    /******************************************** End: Cac ham ho tro *******************************************/
}