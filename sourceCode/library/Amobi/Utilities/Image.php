<?php //
/*
* Function for image
* Author: TTL
* Create time: 21/05/2016 17:12
* Update time: 21/05/2016 17:12
*/
class Amobi_Utilities_Image{
    
    // Ham xu ly image
    // Input mang chua thong tin file image
    // Output: mang thong tin loi hoac mang duong dan image
    public static function handleImage ($path, $ftp_path, $file, $sub_dir, $change_size) {
        try {            
        
            // Xu ly file anh
            $image_size = getimagesize($file);
          
            if (!empty($image_size)) { // Neu ko doc duoc kich thuoc anh thi bao loi
                $img_path = "";
                $width = $image_size[0];
                $height = $image_size[1];
                $tmp = $file;
                // Tim lai dung dinh dang cua anh
                $type = $image_size['mime'];
                $type_array = explode('/', $type);
                $ext = $type_array[1];
                
        		$valid_formats = array("png", "bmp", "jpeg", "gif");
                
        		if (in_array($ext, $valid_formats)) {   
                    
                    // Tuy thuoc tham so dau vao co thay doi kich thuoc anh hay ko?
                    if (!empty($change_size)) {
                        $tmp_image_name = uniqid(time()) . "." . $ext;
                        if (substr($tmp, 0, 5) == '/tmp/') {
                            if (move_uploaded_file($tmp, $path . 'tmp/' . $tmp_image_name)) {
                                $tmp_image = $path . 'tmp/' . $tmp_image_name;
                               
                            }
                        } else {
                            $tmp_image = $tmp;
                        }
                        
                        // Neu up thanh cong thi chuyen size
                        if (!empty($tmp_image)) {
                            // Doi het ten anh thanh jpg
                            $actual_image_name = uniqid(time()) . "." . $ext;
                            $out_large = str_replace("//", '/', 'assets'. $path . '/large/' . $actual_image_name);
                            $out_medium = str_replace("//", '/','assets'. $path . '/medium/' . $actual_image_name);
                            $out_small = str_replace("//", '/','assets'. $path . '/small/' . $actual_image_name);
                            
                            // Duong dan cac file video tren FTP SERVER
                            $file_uri_large = str_replace("//", '/', $ftp_path . '/large/' . $actual_image_name);
                            $file_uri_medium = str_replace("//", '/', $ftp_path .  '/medium/' . $actual_image_name);
                            $file_uri_small = str_replace("//", '/', $ftp_path .  '/small/' . $actual_image_name);
                            
    						// Kiem tra kich thuoc anh 
                            // de tao cac anh kich thuoc nho hon
                            if ($width >= 720) { // Neu anh lon hon 720 thi tao 3 kich thuoc cho avatar
                                // Tao anh large
                                exec("ffmpeg -i " . $tmp_image . " -vf scale=720:-1 " . $out_large);
                                
                                // Tao anh medium
                                exec("ffmpeg -i " . $tmp_image . " -vf scale=480:-1 " . $out_medium);
                                
                                // Tao anh small
                                exec("ffmpeg -i " . $tmp_image . " -vf scale=320:-1 " . $out_small);
                            } else if ($width >= 480) { // Neu anh lon hon 720 thi tao 2 kich thuoc cho avatar                                
                                // Tao anh medium
                                exec("ffmpeg -i " . $tmp_image . " -vf scale=480:-1 " . $out_medium);
                                
                                // Tao anh small
                                exec("ffmpeg -i " . $tmp_image . " -vf scale=320:-1 " . $out_small);
                            } else { // Neu anh nho hon 720 thi tao 1 kich thuoc cho avatar
                                // Tao anh small
                                exec("ffmpeg -i " . $tmp_image . " -vf scale=320:-1 " . $path . 'small/' . $actual_image_name);
                            }
                            
                            // Xoa file anh goc
                            if (file_exists($tmp_image)) {
                                unlink($tmp_image);
                            }
                            
                            $check = true;
                            if (file_exists($out_large)) {
                               
                                $result_upload = rename(PUBLIC_PATH.str_replace("assets",'',$out_large),PUBLIC_PATH.$file_uri_large);
                                if ($result_upload == 1){
                                    // Xoa file tam
                                   
                                    $image_large ='assets'.$file_uri_large ;
                         
                                } else {
                                    $check = false;
                                }
                            }
                            
                            if (file_exists($out_medium)) {
                                $result_upload = rename(PUBLIC_PATH.str_replace("assets",'',$out_medium),PUBLIC_PATH.$file_uri_medium);
                                if ($result_upload == 1) {
                                    // Xoa file tam
                                 
                                    $image_medium = 'assets'.$file_uri_medium;
                                } else {
                                    $check = false;
                                }
                            }
                            
                            if (file_exists($out_small)) {
                                $result_upload = rename(PUBLIC_PATH.str_replace("assets",'',$out_small),PUBLIC_PATH.$file_uri_small);
                                if ($result_upload == 1) {
                                    // Xoa file tam
                                  
                                    $image_small = 'assets'.$file_uri_small;
                                } else {
                                    $check = false;
                                }
                            }
                            
                            if ($check) {
                                // Tao du lieu tra ve
                                $response = array(
                                    'status'    => 1,
                                    'data'      => array(
                                        'image_large'   => !empty($image_large) ? $image_large : null,
                                        'image_medium'  => !empty($image_medium) ? $image_medium : null,
                                        'image_small'   => !empty($image_small) ? $image_small : null,
                                    )
                                ); 
                            } else {
                                // Xoa cac file bi up loi
                                if (file_exists($out_large)) {
                                    // Xoa file tam
                                    unlink($out_large);
                                }
                                
                                if (file_exists($out_medium)) {
                                    // Xoa file tam
                                    unlink($out_medium);
                                }
                                
                                if (file_exists($out_small)) {
                                    // Xoa file tam
                                    unlink($out_small);
                                }
                                
                                // Xoa file tren Resource Server
                                $array_delete = array($file_url_large, $file_url_medium, $file_url_small);
                                foreach ($array_delete as $value) {
                                    unset($value);
                                }
                                
                                $response = array(
                                    'status'    => 0,
                                    'message'   => "Up ảnh lên kho không thành công"
                                );
                            }   
                        } else {
                            $response = array(
                                'status'    => 0,
                                'message'   => "Upload ảnh lỗi"
                            );
                        }
                    } else {
                        $actual_image_name = uniqid(time()).".". $ext;
                        
                        // Chuan bi duong dan
                        $file_uri = str_replace("//", '/', $ftp_path . '/' . $sub_dir . '/' . $actual_image_name);
                        
                        // Up file len server Resource Server
                        $result_upload = rename($tmp,PUBLIC_PATH.$file_uri);
                        if ($result_upload == 1) {
                            $response = array(
                                'status'    => 1,
                                'data'      => 'assets'.$file_uri
                            );
                            
                        } else {
                            $response = array(
                                'status'    => 0,
                                'message'   => "Upload ảnh lỗi"
                            );
                        }
        			}
        		} else {
                    $response = array(
                        'status'    => 0,
                        'message'   => "Định dạng ảnh không được hỗ trợ"
                    );
        		}   
            } else {
                $response = array(
                    'status'    => 0,
                    'message'   => "Không đọc được thông tin ảnh"
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
    
    // Ham tai anh tu link tren mang ve
    public static function getImageFromLink ($link, $path) {
        try {
            $name_arr = explode(".", $link);
            if (count($name_arr) > 1) {
                $ext = array_pop($name_arr);    
            } else {
                $ext = 'tmp';
            }
            $unique_id = uniqid(time());
    		$actual_image_name = $unique_id . "." . $ext;
            $img_url = $path . $actual_image_name;
            $img_url = str_replace('//', '/', $img_url);
            
            // Tai anh
            $contentOrFalseOnFailure   = file_get_contents($link);
            
            // Kiem tra co get duoc anh ko?
            if (substr($http_response_header[0], 9, 3) == '200') {
                $byteCountOrFalseOnFailure = file_put_contents($img_url, $contentOrFalseOnFailure);
                // Kiem tra tao thanh cong
                if (is_file($img_url)) {
                    // Doi lai ten file cho dung
                    $image_size = getimagesize($img_url);
                    if (!empty($image_size)) { // Neu ko doc duoc kich thuoc anh thi bao loi
                        $type = $image_size['mime'];
                        $type_array = explode('/', $type);
                        $new_ext = $type_array[1];
                        
                        if ($new_ext != $ext) {
                            $old_img_url = $img_url;
                            $new_actual_image_name = $unique_id . "." . $new_ext;
                            $img_url = $path . $new_actual_image_name;
                            $img_url = str_replace('//', '/', $img_url);
                            
                            rename($old_img_url, $img_url);    
                        }
                        
                        if (!empty($img_url)) {
                            $response = array(
                                'status'    => 1,
                                'url'       => $img_url
                            );
                        } else {
                            $response = array(
                                'status'    => 0,
                                'message'   => "Can't rename image"
                            );
                        }
                    } else {
                        $response = array(
                            'status'    => 0,
                            'message'   => "Can't read image info"
                        );
                    }
                } else {
                    $response = array(
                        'status'    => 0,
                        'message'   => "Can't create image"
                    );
                }    
            } else {
                $response = array(
                    'status'    => 0,
                    'message'   => "File not found"
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
