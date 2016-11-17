<?php

class Manageapp_CrawlerController extends Amobi_Controller_Action
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
                $author_id = $params['story_id'];
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
    public function getStoryAction(){
        try{
            $time = date('Y-m-d H:i:s');
            $this->_helper->viewRenderer->setNoRender('true');
            require 'InputModel.php';
            require 'default_simple_html_dom.php';
            $inputModel = new InputModel();
            $authorModel = new Model_api_manageapp_Author();
            $storyModel = new Model_api_manageapp_Story();
            $xml = file_get_contents(PUBLIC_PATH."/xml/sstruyen.xml") or die("Error: không thể tạo đối tượng");
            preg_match_all('~http\:\/\/sstruyen\.com\/([^\/m\/].+?)\/(\d+)\.html~i', $xml, $matches);
            $index = 0;
            $m = 0; // bien story
            $list_story = $matches[0];
            foreach ($matches[0] as $value) {
                echo 'link truyen: '.$value."</br>";
                $link =$value;
                $dom = file_get_html($link);
                $des = $dom->find('.story_description',0);
                $des = $des->outertext;
                $story_name  = $dom ->find('h1.title a',0)->plaintext;
            
                $story_code = Amobi_Utilities_Input::tenchuan($story_name);
            
                $author = $dom -> find('ul.item li');
                $author_name = '';
                $total_chapter = 0;
                $is_full =0 ;
                $category = '';
              
                foreach ($author as $key =>$au){
                    if($au->getAttribute('class') == 'bg'){
                        if($au->getAttribute('itemprop')=='author'){
                            $author_name = $au->children(1)->plaintext;
                        }
                        $total_chapter = $au->children(1)->plaintext;
                    }
                    if($key==3){
                        if($au->children(1)->plaintext == 'FULL'){
                            $is_full = 1;
                        }
                    }
                    if($key == 1){
                        $category = $au->children(1)->plaintext;
                    }
                }
                
                echo 'Ten truyen: '.$story_name."<br>";
                echo 'loi mo dau: '.$des."<br>";
                echo 'so chuong: '. $total_chapter."</br>";
                echo 'tac gia: '. $author_name."</br>";
                echo 'the loai: '.$category."</br>";
                echo 'trang thai:' . $is_full."</br>";
                echo "<hr>";
                
                // kiem tra tac gia co trung lap ko
//                $author_name = trim(html_entity_decode($author_name, ENT_QUOTES, 'UTF-8'));
//                $author_name = Normalizer::normalize($author_name);
                $author_code = Amobi_Utilities_Input::tenchuan(trim($author_name));
               
        
                    $check_author = $authorModel->getAuthor(array(
                        'author_code'  => $author_code
                       ));
                    
                    // Kiem tra trung lap tag
                    if (empty($check_author)) {
                        // Thong bao thanh cong
                        $data = array(
                            'created_time'  => $time,
                            'author_name'      => $author_name,
                            'author_code'      => $author_code,
                            'updated_time'  => $time
                        );
                        $author_id = $authorModel->insert($data);
                    
                    } else {
                        $author_id = $check_author['id'];
                        
                    }
                    $check_story = $storyModel->getListStory(array(
                            'story_code'  => $story_code
                       ));
                       
                        if(empty($check_story)){
                            $data_story = array(
                            'author_id'             => $author_id,
                            'story_name'            => $story_name,
                            'story_code'            => $story_code,
                            'introduction'          => $des,
                            'avatar_description'    => $story_name,
                            'source'                => $link,
                            'total_chapter'         => $total_chapter,
                            'is_full'               => $is_full,
                            'is_crawler'            => '0',
                            'status'                => '1',
                            'created_time'          => $time,
                            'updated_time'          => $time,
                            'published_time'        => $time,
                        );
                        $story_id = $storyModel->insert($data_story);

                        // Xu ly category
                        if (!empty($category)) {
                            $resultHandleCategory = Amobi_Utilities_Category::handle($story_id, $category);

                            if ($resultHandleCategory['status'] == 0) {
                                $message = $resultHandleCategory['message'];
                            }
                        }
                        }   
                    
                                
                           
  //                  $x = 0;// bien chapter
                    
//                    $link_story = array();
//                    $link_story[$m]['name'] = $link;
//                    $link_story[$m]['des'] = $des->outertext;
              /*  $linkpage = $dom->find('.page-split',0);
                $max_page = $linkpage->last_child ();
                    preg_match('~.*page-(\d+)\.html~', $max_page->href,$max_page_number);
                    $page_max = $max_page_number[1];
                  
                   
                    for ($i = 0; $i < $page_max; $i++) {
                        $url_chapter = $link_get_page.'/page-'.$i.'.html';
                        $dom1 = file_get_html($url_chapter);
                        $linkpage1 = $dom1->find('.page-split',0);
                        $content = $linkpage1->parent()->parent();
                        foreach ($content->children() as $child){
                            if($child->getAttribute('class') == 'chuongmoi'){

                                foreach ($child->children() as $chapter){
                                    global $index;
                                    $index ++;
                                    global $x;

//                                    $link_story[$m]['chapter'][$x]['link'] = $chapter->children(0)->href;
//                                    //load vao chap
//                                    $dom2 = file_get_html('http://sstruyen.com'.$chapter->children(0)->href);
//                                    //tach lay id va time
//                                    preg_match('~var nChaptId = ([0-9]+);~',$dom2, $subject);
//                                    preg_match('~var szChapterTime = \"([^"]*)\"~',$dom2, $subject1);
//                                    $szChapterTime = preg_replace("/(-|\:| )/", '', $subject1[1]);
//                                    $nChaptId = $subject[1];
//                                    // link ajax
//                                    $link_get_content = "http://sstruyen.com/doc-truyen/index.php?ajax=ct&id=".$nChaptId."&t=".$szChapterTime;
//                                    $content_chapter = file_get_contents($link_get_content);
//                                    $link_story[$m]['chapter'][$x]['content'] = $content_chapter;
                                    $x++;
                                    
                                   
                                }
                            }
                        }
                       
                }
           */
                 
            }
        } catch (Exception $e) {
              echo $e->getMessage();die();

        }
    }
    public function getStoryAjaxAction(){
            $time = date('Y-m-d H:i:s');
            $this->_helper->viewRenderer->setNoRender('true');
            $params = $this->_arrParam;
            require 'InputModel.php';
            require 'default_simple_html_dom.php';
            $inputModel = new InputModel();
            $authorModel = new Model_api_manageapp_Author();
            $storyModel = new Model_api_manageapp_Story();
            $crawlerModel = new Model_api_manageapp_Crawler();
         
            if ($this->_request->isPost()) {
                if (!empty($params['site_id'])) {
                    if($params['site_id'] ==1){
                        $story_offset = $crawlerModel -> getStoryOffset($params['site_id']); // lay ra vi tri truyen da duoc tong hop
                        $xml = file_get_contents(PUBLIC_PATH."/xml/sstruyen.xml") or die("Error: không thể tạo đối tượng");
                        preg_match_all('~http\:\/\/sstruyen\.com\/([^\/m\/].+?)\/(\d+)\.html~i', $xml, $matches); // lay ra danh sach url de tong hop
                        $index = 0;
                        $m = 0; // bien story
                        $list_story = $matches[0];// list cac url
                        $total_story = sizeof($list_story);// tong so story trong xml
                        if(empty($story_offset['story_offset'])){
                            $current_story = 0;// neu null -> 0
                        }else{
                             $current_story = $story_offset['story_offset']+1;// story dang crawler
                        }

                        while(empty($story_id)){// tong hop that bai => quay lai tong hop tiep phan tu tiep theo
                            if($index == 0){
                                $current_story = $current_story;
                            }
                            else {
                                $current_story ++;
                            }
                            if($current_story == $total_story){ // neu full truyen thi thoat
                                break;
                            }
                            $link =$list_story[$current_story];//lay den truyen tiep theo
                            $dom = file_get_html($link);// load url
                            $linkpage = $dom->find('.page-split',0);// tim den phan phan trang
                            $max_page = $linkpage->last_child ();
                            preg_match('~.*page-(\d+)\.html~', $max_page->href,$max_page_number);
                            $page_max = $max_page_number[1];
                            $des = $dom->find('.story_description',0);// tim class chua thong tin truyen
                            $des = $des->outertext;
                            $story_name  = $dom ->find('h1.title a',0)->plaintext; // ten truyen

                            $story_code = Amobi_Utilities_Input::tenchuan($story_name);// chuyen ten truyen ve ham ten chuan de 
                                                                                      // so sanh voi cac truyen trong db

                            $author = $dom -> find('ul.item li');// tim tac gia
                            $author_name = '';
                            $total_chapter = 0;
                            $is_full =0 ;
                            $category = '';

                            foreach ($author as $key =>$au){
                                if($au->getAttribute('class') == 'bg'){
                                    if($au->getAttribute('itemprop')=='author'){
                                        $author_name = $au->children(1)->plaintext;//tac gia
                                    }
                                    $total_chapter = $au->children(1)->plaintext;// tong chuong
                                }
                                if($key==3){
                                    if($au->children(1)->plaintext == 'FULL'){
                                        $is_full = 1;// tinh trang truyen
                                    }
                                }
                                if($key == 1){
                                    $category = $au->children(1)->plaintext;//  the loai
                                }
                            }
                            // chuyen ten tac gia ve ham chuan de so sanh trong db
                            $author_code = Amobi_Utilities_Input::tenchuan(trim($author_name));


                            $check_author = $authorModel->getAuthor(array(// kiem tra xem ton tai tac gia chua
                                'author_code'  => $author_code
                               ));


                            if (empty($check_author)) {// neu ko ton tai thi
                                // Thong bao thanh cong
                                $data = array(// tao du lieu moi cho tac gia
                                    'created_time'      => $time,
                                    'author_name'       => $author_name,
                                    'author_code'       => $author_code,
                                    'updated_time'      => $time
                                );
                                $author_id = $authorModel->insert($data);//them vao db

                            } else {
                                $author_id = $check_author['id'];// neu ko co thi lay id cua tac gia ton tai co trong db

                            }
                            $check_story = $storyModel->getListStory(array(// kiem tra truyen co ton tai trong db chua
                                'story_code'  => $story_code
                            ));

                            if(empty($check_story)){// neu ko ton tai
                                $data_story = array(// tao du lieu moi
                                'author_id'             => $author_id,
                                'crawler_id'            => $story_offset['id'],
                                'story_name'            => $story_name,
                                'story_code'            => $story_code,
                                'introduction'          => $des,
                                'avatar_description'    => $story_name,
                                'source'                => $link,
                                'total_chapter'         => $total_chapter,
                                'is_full'               => $is_full,
                                'total_page'            => $page_max,
                                'is_crawler'            => CRAWLER_OFF,
                                'status'                => STATUS_ON,
                                'created_time'          => $time,
                                'updated_time'          => $time,
                                'published_time'        => $time,
                                );
                                $story_id = $storyModel->insert($data_story);// them vao db

                                // Xu ly category
                                if (!empty($category)) {// neu co category
                                    $resultHandleCategory = Amobi_Utilities_Category::handle($story_id, $category);// ham xu ly category

                                    if ($resultHandleCategory['status'] == 0) {
                                        $message = $resultHandleCategory['message'];
                                    }// tra ve message
                                }
                                // xy ly crawler
                                $data_crawler = array(// tao du lieu offset da luu moi
                                    'story_offset' => $current_story
                                );
                                $where = 'id = ' . $params['site_id'];
                                $crawlerModel ->update($data_crawler, $where);
                                $response = array(
                                    'current' => $current_story + 1,
                                    'total'   => $total_story,
                                );
                                $result = array('status' => 1, 'data' => $response);
                            }  
                            $index ++;// bien dem neu that bai tong hop du lieu thi tang len 1

                        }
                        if(empty($story_id)){
                            $result = array('status' => 0, 'message' => 'Tối đa truyện');
                        }
                    }
                    else if($params['site_id'] ==2){
                        $story_offset = $crawlerModel -> getStoryOffset($params['site_id']); // lay ra vi tri truyen da duoc tong hop
                        $xml = file_get_contents(PUBLIC_PATH."/xml/sstruyen.xml") or die("Error: không thể tạo đối tượng");
                        preg_match_all('~http\:\/\/sstruyen\.com\/([^\/m\/].+?)\/(\d+)\.html~i', $xml, $matches); // lay ra danh sach url de tong hop
                        $index = 0;
                        $m = 0; // bien story
                        $list_story = $matches[0];// list cac url
                        $total_story = sizeof($list_story);// tong so story trong xml
                        if(empty($story_offset['story_offset'])){
                            $current_story = 0;// neu null -> 0
                        }else{
                             $current_story = $story_offset['story_offset']+1;// story dang crawler
                        }

                        while(empty($story_id)){// tong hop that bai => quay lai tong hop tiep phan tu tiep theo
                            if($index == 0){
                                $current_story = $current_story;
                            }
                            else {
                                $current_story ++;
                            }
                            if($current_story == $total_story){ // neu full truyen thi thoat
                                break;
                            }
                            $link =$list_story[$current_story];//lay den truyen tiep theo
                            $dom = file_get_html($link);// load url
                            $linkpage = $dom->find('.page-split',0);// tim den phan phan trang
                            $max_page = $linkpage->last_child ();
                            preg_match('~.*page-(\d+)\.html~', $max_page->href,$max_page_number);
                            $page_max = $max_page_number[1];
                            $des = $dom->find('.story_description',0);// tim class chua thong tin truyen
                            $des = $des->outertext;
                            $story_name  = $dom ->find('h1.title a',0)->plaintext; // ten truyen

                            $story_code = Amobi_Utilities_Input::tenchuan($story_name);// chuyen ten truyen ve ham ten chuan de 
                                                                                      // so sanh voi cac truyen trong db

                            $author = $dom -> find('ul.item li');// tim tac gia
                            $author_name = '';
                            $total_chapter = 0;
                            $is_full =0 ;
                            $category = '';

                            foreach ($author as $key =>$au){
                                if($au->getAttribute('class') == 'bg'){
                                    if($au->getAttribute('itemprop')=='author'){
                                        $author_name = $au->children(1)->plaintext;//tac gia
                                    }
                                    $total_chapter = $au->children(1)->plaintext;// tong chuong
                                }
                                if($key==3){
                                    if($au->children(1)->plaintext == 'FULL'){
                                        $is_full = 1;// tinh trang truyen
                                    }
                                }
                                if($key == 1){
                                    $category = $au->children(1)->plaintext;//  the loai
                                }
                            }
                            // chuyen ten tac gia ve ham chuan de so sanh trong db
                            $author_code = Amobi_Utilities_Input::tenchuan(trim($author_name));


                            $check_author = $authorModel->getAuthor(array(// kiem tra xem ton tai tac gia chua
                                'author_code'  => $author_code
                               ));


                            if (empty($check_author)) {// neu ko ton tai thi
                                // Thong bao thanh cong
                                $data = array(// tao du lieu moi cho tac gia
                                    'created_time'      => $time,
                                    'author_name'       => $author_name,
                                    'author_code'       => $author_code,
                                    'updated_time'      => $time
                                );
                                $author_id = $authorModel->insert($data);//them vao db

                            } else {
                                $author_id = $check_author['id'];// neu ko co thi lay id cua tac gia ton tai co trong db

                            }
                            $check_story = $storyModel->getListStory(array(// kiem tra truyen co ton tai trong db chua
                                'story_code'  => $story_code
                            ));

                            if(empty($check_story)){// neu ko ton tai
                                $data_story = array(// tao du lieu moi
                                'author_id'             => $author_id,
                                'story_name'            => $story_name,
                                'story_code'            => $story_code,
                                'introduction'          => $des,
                                'avatar_description'    => $story_name,
                                'source'                => $link,
                                'total_chapter'         => $total_chapter,
                                'is_full'               => $is_full,
                                'total_page'            => $page_max,
                                'is_crawler'            => CRAWLER_OFF,
                                'status'                => STATUS_ON,
                                'created_time'          => $time,
                                'updated_time'          => $time,
                                'published_time'        => $time,
                                );
                                $story_id = $storyModel->insert($data_story);// them vao db

                                // Xu ly category
                                if (!empty($category)) {// neu co category
                                    $resultHandleCategory = Amobi_Utilities_Category::handle($story_id, $category);// ham xu ly category

                                    if ($resultHandleCategory['status'] == 0) {
                                        $message = $resultHandleCategory['message'];
                                    }// tra ve message
                                }
                                // xy ly crawler
                                $data_crawler = array(// tao du lieu offset da luu moi
                                    'story_offset' => $current_story
                                );
                                $where = 'id = ' . $params['site_id'];
                                $crawlerModel ->update($data_crawler, $where);
                                $response = array(
                                    'current' => $current_story + 1,
                                    'total'   => $total_story,
                                );
                                $result = array('status' => 1, 'data' => $response);
                            }  
                            $index ++;// bien dem neu that bai tong hop du lieu thi tang len 1

                        }
                        if(empty($story_id)){
                            $result = array('status' => 0, 'message' => 'Tối đa truyện');
                        }
                    }
  
                } else {
                    // tao du lieu tra ve
                    $result = array('status' => 0, 'message' => 'Thiếu tham số');
                }
            } else {
                // tao du lieu tra ve
                $result = array('status' => 0, 'message' => 'Phương thức truyền dữ liệu ko được hỗ trợ');
            }
        $this->_helper->json($result);
        //exit(json_encode($result));
    }
    public function getChapterAction(){
        try {
        $time = date('Y-m-d H:i:s');
        require 'default_simple_html_dom.php';
        $this->_helper->viewRenderer->setNoRender('true');
        $storyModel = new Model_api_manageapp_Story();
        $storyContentModel = new Model_api_manageapp_StoryContent();
        
        $listStory  = $storyModel->getListStory(array(
                'crawler'  => CRAWLER_FLASE
        ));
        $num_story = 0;
      
        foreach ($listStory as $value) {
            
            if(isset($value['source'])){
            echo $value['story_name'];
            $num_story ++;// dem so truyen hoan thanh crawler
            echo $value['source'];
            preg_match('~(.*\d+)\.html~', $value['source'],$link_arr);
            $link_get_page = $link_arr[1];
            $dom = file_get_html($value['source']);
            $linkpage = $dom->find('.page-split',0);
            $max_page = $linkpage->last_child ();
            preg_match('~.*page-(\d+)\.html~', $max_page->href,$max_page_number);
            $page_max = $max_page_number[1];
            $chapter_index = 1;
                for ($i = 0; $i < $page_max+1; $i++) {
                
                    $url_chapter = $link_get_page.'/page-'.$i.'.html';
                    $dom1 = file_get_html($url_chapter);
                    $linkpage1 = $dom1->find('.page-split',0);
                    $content = $linkpage1->parent()->parent();
                   
                    foreach ($content->children() as $child){
                        if($child->getAttribute('class') == 'chuongmoi'){
                            foreach ($child->children() as  $chapter){
                                if($chapter_index <= $value['lastest_chapter']){
                                    $chapter_index ++;
                                }else {
                                echo $chapter->children(0)->href."</br>";
                                echo $chapter_index."</br><hr>";
                              //load vao chap
                                $dom2 = file_get_html('http://sstruyen.com'.$chapter->children(0)->href);
                                $title = $dom2->find('.detail-content span h3',0)->plaintext;
                                preg_match('~.*:(.*)~', $title,$title_chap);
                                if(!empty($title_chap)){
                                $db_title = ($title_chap[1]);
                                }
                                else {
                                    $db_title = $chapter_index;
                                }
                                //tach lay id va time
                                preg_match('~var nChaptId = ([0-9]+);~',$dom2, $subject);
                                preg_match('~var szChapterTime = \"([^"]*)\"~',$dom2, $subject1);
                                $szChapterTime = preg_replace("/(-|\:| )/", '', $subject1[1]);
                                $nChaptId = $subject[1];
                                // link ajax
                                $link_get_content = "http://sstruyen.com/doc-truyen/index.php?ajax=ct&id=".$nChaptId."&t=".$szChapterTime;
                                $content_chapter = file_get_contents($link_get_content);


                                $data = array(
                                    'story_id'            => $value['id'],
                                    'status'              => '1',
                                    'chapter_number'      => $chapter_index,
                                    'source'              => $link_get_content,
                                    'is_crawler'          => '1',
                                    'title'               => !empty($db_title) ? $db_title : $chapter_index,
                                    'content'             => $content_chapter,
                                    'created_time'        => $time,
                                    'updated_time'        => $time,
                                    'published_time'      => $time,

                                );
                                $storyContentModel->insert($data);
                                // cap nhat chuong moi nhat 
                                $data_story = array(
                                    'lastest_chapter' => $chapter_index
                                );
                                $where = 'id = ' . $value['id'];
                                $storyModel ->update($data_story, $where);
                                $chapter_index ++;
                                die('1c');
                                }
                            }
                           
                        }

                    }
                       
                }
           
            }
            // hoan thanh 1 truyen => update co is_crawler trong bang story
            $data_story_crawler = array(
                'is_crawler' => '1'
            );
            $where = 'id = ' . $value['id'];
            $storyModel ->update($data_story_crawler, $where);
            $response = array(
                    'status' => 0,
                    'message' => 'Phương thức không được hỗ trợ'
            );    
        }
        $response = array(
                    'status' => 1,
                    'num_story' => $num_story,
                    'message' => 'Phương thức không được hỗ trợ'
                );
            
        }catch (Exception $exc) {
            $response = array(
                'status' => 0,
                'message' => 'Lỗi xử lý server'
            );
        }


        
        
    }
    public function getChapterAjaxAction(){// function  get chapter per page 
        try {
        $time = date('Y-m-d H:i:s');
        require 'default_simple_html_dom.php';
        $this->_helper->viewRenderer->setNoRender('true');
        $storyModel = new Model_api_manageapp_Story();
        $storyContentModel = new Model_api_manageapp_StoryContent();
        $params = $this->_arrParam;
        if ($this->_request->isPost()){
            if (!empty($params['site_id'])) {
                $listStory  = $storyModel->getListStory(array( // lay ds truyen chua tong hop trang
                'crawler'    => CRAWLER_OFF,
                'crawler_id' => $params['site_id'],   
                ));
                if(!empty($listStory)){
                    $num_story = 0;// bien dem truyen
                    $value = $listStory[0];// lay phan tu dau tien trong ds 
                    if(isset($value['source'])){// neu co source
                    preg_match('~(.*\d+)\.html~', $value['source'],$link_arr);// tach lay url  phan trang
                    $link_get_page = $link_arr[1];// lay link chuan phan trang
                    if(!isset($value['page_crawler'])){// bien luu vi tri page da crawler

                        $value['page_crawler'] = -1;
                    }
                    $page_max = $value['total_page'];// bien phan trang lon nhat
                    if($value['page_crawler']<$page_max){// neu chua quet het trang
                        $next_page = $value['page_crawler']+1;// bien trang quet lan nay
                        $url_chapter = $link_get_page.'/page-'.$next_page.'.html'; // tao url de quet
                        $dom1 = file_get_html($url_chapter);// load vao trang
                        $linkpage1 = $dom1->find('.page-split',0);//tim den phan phan trang
                        $content = $linkpage1->parent()->parent();// tim den phan chua cac danh sach chuong
                        foreach ($content->children() as $child){
                            if($child->getAttribute('class') == 'chuongmoi'){                        
                                foreach ($child->children() as  $chapter){
                                    if($value['lastest_chapter'] < $value['total_chapter']){                      
                                        $link_chapter = 'http://sstruyen.com'.$chapter->children(0)->href;
                                        $data = array(
                                            'story_id'            => $value['id'],
                                            'status'              => STATUS_OFF,
                                            'chapter_number'      => $value['lastest_chapter']+1,
                                            'source'              => $link_chapter,
                                            'is_crawler'          => CRAWLER_OFF,
                                            'created_time'        => $time,
                                            'updated_time'        => $time,
                                            'published_time'      => $time,
                                        );
                                        $storyContentModel->insert($data);
                                        // cap nhat chuong moi nhat 
                                        $data_story = array(
                                            'lastest_chapter' => $value['lastest_chapter']+1,
                                            'page_crawler'    => $next_page,
                                        );
                                        $where = 'id = ' . $value['id'];
                                        $storyModel ->update($data_story, $where);
                                        $value['lastest_chapter']++;
                                    } 
                                }
                            }
                        }
                        // done 1 page
                        $response = array(
                                'status' => 1,
                                'message' => 'truyện '.$value['story_name'].' tổng hợp đến trang thứ '. $next_page.'/'.$page_max,
                        );  
                    }
                    else if($value['page_crawler'] == $page_max){
                        // hoan thanh 1 truyen => update co is_crawler trong bang story
                        $data_story_crawler = array(
                            'is_crawler' => '1'
                        );
                        $where = 'id = ' . $value['id'];
                        $storyModel ->update($data_story_crawler, $where);
                        $response = array(
                                'status' => 0,
                                'message' => 'truyện '.$value['story_name'].' hoàn thành',
                        );  
                    }   
                    }else{
                    $response = array(
                                'status' => 0,
                                'message' => 'Truyện không có đường dẫn tổng hợp'
                            );
                    }
                }else{
                    $response = array(
                                'status' => 0,
                                'message' => 'Đã tổng hợp hết truyện'
                            );
                }
            }
            
            
        }            
        else{
            // tao du lieu tra ve
            $response = array('status' => 0, 'message' => 'Phương thức truyền dữ liệu ko được hỗ trợ');
        }
        
        }catch (Exception $exc) {
            $response = array(
                'status' => 0,
                'message' => 'Lỗi xử lý server'
            );
        }
        exit(json_encode($response));
    }
    public function getChapterContentAjaxAction(){
        try {
        $time = date('Y-m-d H:i:s');
        require 'default_simple_html_dom.php';
        $this->_helper->viewRenderer->setNoRender('true');
        $storyModel = new Model_api_manageapp_Story();
        $storyContentModel = new Model_api_manageapp_StoryContent();
        $params = $this->_arrParam;
        if ($this->_request->isPost()){
            $listStoryContent  = $storyContentModel->getListStoryContent(array(
                'crawler'  => CRAWLER_OFF
            ));
          
            $num_chapter= 0; // bien dem so chuong duoc tong hop
            $value = $listStoryContent[0];// tong hop phan tu dau tien
            if(isset($value['source'])){
                //load vao chap
                $dom2 = file_get_html($value['source']);
                $title = $dom2->find('.detail-content span h3',0)->plaintext;
                preg_match('~.*:(.*)~', $title,$title_chap);
                if(!empty($title_chap)){
                $db_title = ($title_chap[1]);
                }
                else {
                    if($value['chapter_number'] == 1)
                    {
                        $db_title = ' Mở đầu';
                    }
                    else{
                    $db_title = $value['chapter_number'];
                    }
                }
                //tach lay id va time
                preg_match('~var nChaptId = ([0-9]+);~',$dom2, $subject);
                preg_match('~var szChapterTime = \"([^"]*)\"~',$dom2, $subject1);
                $szChapterTime = preg_replace("/(-|\:| )/", '', $subject1[1]);
                $nChaptId = $subject[1];
                // link ajax
                $link_get_content = "http://sstruyen.com/doc-truyen/index.php?ajax=ct&id=".$nChaptId."&t=".$szChapterTime;
                $content_chapter = file_get_contents($link_get_content);


                $data = array(
                    'status'              => '1',                   
                    'is_crawler'          => CRAWLER_ON,
                    'title'               => $db_title,
                    'content'             => $content_chapter,
                    'updated_time'        => $time,
                    'published_time'      => $time,

                );
                $where = 'id = ' . $value['id'];
                $storyContentModel->update($data,$where);
                $response = array(
                        'status' => 1,
                        'message' => 'Tổng hợp thành công truyện :'.$value['story_name'].' chương:'.$value['chapter_number'].' '. $db_title,
                );
            }else{
            $response = array(
                        'status' => 0,
                        'message' => 'Truyện không có đường dẫn tổng hợp'
                    );
            }
        }
        else{
            // tao du lieu tra ve
            $response = array('status' => 0, 'message' => 'Phương thức truyền dữ liệu ko được hỗ trợ');
        }
        
        }catch (Exception $exc) {
            $response = array(
                'status' => 0,
                'message' => 'Lỗi xử lý server'
            );
        }
        exit(json_encode($response));
        
    }
}