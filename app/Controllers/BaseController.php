<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;


use TechAPI\Api\SendBrandnameOtp;
use TechAPI\Exception as TechException;
use TechAPI\Auth\AccessToken;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = [];

    /**
     * Constructor.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = \Config\Services::session();
    }

    //CHUYỂN ĐỔI SĐT TỪ 11 SỐ SANG 10 SỐ
    public function convertPhoneDigit($phone){
        $d_convert = [
            '0120' => '070',
            '0121' => '079',
            '0122' => '077',
            '0126' => '076',
            '0128' => '078',
            '0123' => '083',
            '0124' => '084',
            '0125' => '085',
            '0127' => '081',
            '0129' => '082',
            '0162' => '032',
            '0163' => '033',
            '0164' => '034',
            '0165' => '035',
            '0166' => '036',
            '0167' => '037',
            '0168' => '038',
            '0169' => '039',
            '0186' => '056',
            '0188' => '058',
            '0199' => '059'
        ];

        if (strlen($phone) > 10){
            $phone_digit = substr($phone,0,4);
            foreach (array_keys($d_convert) as $dc){
                if ($dc == $phone_digit){
                    $new_phone = $d_convert[$dc].substr($phone,4);

                    return $new_phone;
                }
            }
        } else {
            return $phone;
        }
    }


    public function getBeforeLoginLayout($data, $title, $content){
		$data['title'] 		= $title;
        $data['header'] 	= view('BeforeLogin/layout/header', $data);
        $data['footer'] 	= view('BeforeLogin/layout/footer');
		$data['content'] 	= view($content);

		return $data;
	}

    public function getAfterLoginLayout($data, $title, $content){
		$data['title'] 		= $title;
        $data['header'] 	= view('AfterLogin/layout/header', $data);
        $data['footer'] 	= view('AfterLogin/layout/footer');
        $data['topbar'] 	= view('AfterLogin/layout/topbar', $data);
		$data['content'] 	= view($content);

		return $data;
	}

    function sendSMS($phone,$brandName,$msg){
					
		// Khởi tạo các tham số của tin nhắn.
		$arrMessage = array(
			'Phone'      => $phone,
			'BrandName'  => $brandName,
			// 'Message'    => base64_encode($msg)
			'Message'    => $msg
		);
		
		// Khởi tạo đối tượng API với các tham số phía trên.
		$apiSendBrandname = new SendBrandnameOtp($arrMessage);
			
		try {
			// Lấy đối tượng Authorization để thực thi API
			$oGrantType      = getTechAuthorization();
			
			// Thực thi API
			$arrResponse     = $oGrantType->execute($apiSendBrandname);
			
			// kiểm tra kết quả trả về có lỗi hay không
			if (! empty($arrResponse['error']))
			{
				// Xóa cache access token khi có lỗi xảy ra từ phía server
				AccessToken::getInstance()->clear();
				
				// quăng lỗi ra, và ghi log
				throw new TechException($arrResponse['error_description'], $arrResponse['error']);
			}
			
			// echo '<pre>';
			// print_r($arrResponse);
			// echo '</pre>';
		} catch (\Exception $ex) {
			// echo sprintf('<p>Có lỗi xảy ra:</p>');
			// echo sprintf('<p>- Mã lỗi: %s</p>', $ex->getCode());
			// echo sprintf('<p>- Mô tả lỗi: %s</p>', $ex->getMessage());
			return array(
				'status' => 0,
				'msg' => 'Error !',
				'error_code' => $ex->getCode(),
				'error_desc' => $ex->getMessage()
			);
		}
	}

    function apiLogin($data){
		$username = $data['username'];
		$password = $data['password'];

		// Gọi API tạo Transaction mới
		$url = 'http://localhost:8080/openmrs/ws/rest/v1/session';

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
		$result = curl_exec($ch);
		curl_close($ch);
		$rs = json_decode($result, true);

		return $rs;
	}

    function apiMarketingNotification($data){

        $data_string = json_encode($data, true);

        // Gọi API tạo Transaction mới
        $url = 'http://localhost:8080/openmrs/ws/rest/v1/marketingNotification';

        // Login to get COOKIE
        $dataLogin = array(
            'username' => ADMIN_USERNAME,
            'password' => ADMIN_PASSWORD
        );
        $login = $this->apiLogin($dataLogin);
        // print_r($login['sessionId']);

        $ch = curl_init($url);
        $strCookie = 'JSESSIONID='.$login['sessionId'].'; Path=/openmrs; HttpOnly;';
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_COOKIE, $strCookie );
        $result = curl_exec($ch);
        curl_close($ch);
        $rs = json_decode($result, true);

        return $rs;
    }

    function registerPatient($user){
		// Chuẩn bị $userData để truyền vào cho API
		$userData = array(
			'username' 		=> $user['username'],
			'phoneNumber' 	=> $user['phoneNumber'],
			'password' 		=> $user['password'],
			'person' 		=> array(
				'names' 		=> array(
					'givenName' 	=> $user['givenName'],
				),
				'birthdate' 	=> $user['birthdate'],
				'gender' 		=> $user['gender'],
			),
			'role' 			=> $user['role'],
			'providerRole' 	=> $user['providerRole'],
		);

		$data_string = json_encode($userData, true);

		// Gọi API tạo User mới
		$url = 'http://localhost:8080/openmrs/ws/rest/v1/register';

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		$result = curl_exec($ch);
		curl_close($ch);
		$rs = json_decode($result, true);

		return $rs;
	}

    public function checkUserApi($data){
        return $this->user->where($data)->first();
    }

    public function getDeviceToken($data){
        return $this->token->where($data)->find();
    }

    public function send_notification($sdt, $content){

        // Dùng số điện thoại để lấy thông tin user
        $dataUser = array(
            'username' => $sdt,
            'status' => 'ACTIVE'
        );
        $user = $this->checkUserApi($dataUser);

        // Lấy tất cả Device Token (ứng với tất cả các điện thoại user đó đã dùng để đăng nhập app)
        $dataCheckDevice = array(
            'creator'   => $user['user_id'],
            'voided'    => 0
        );
        $rs_token = $this->getDeviceToken($dataCheckDevice);

        // Trường hợp tồn tại ít nhất 1 Device Token => gửi Notification
        if(count($rs_token)>0){
            foreach($rs_token as $val){
                $this->content_notification($val['token'],$val['os_type'], $content, $sdt);

            }
            return true;
        }else{
            return false;
        }

    }

    public function content_notification($regId, $osType, $content, $sdt){

        //Lấy tiêu đề & nội dung thông báo theo Cấu hình Notification

        $NotificationArray = array();
        $NotificationArray["content_available"] = true;
        $NotificationArray["title"] = 'Thông báo mới từ Doctor4U';
        $NotificationArray["body"]  = $content;
        // $NotificationArray["sound"] = "default";
        $NotificationArray["sound"] = "doctor4u.wav";
        $NotificationArray["android_channel_id"] = "sound-channel-id";
        // $NotificationArray["badge"] = 1;
        $data=array(
            'data' => $sdt,
            'type' => 'notification'
        );

        $result = $this->send_push_notification($regId, $NotificationArray, $osType,$data);
    }

    public function send_push_notification($regId, $notification, $device_type,$data){
        $url = 'https://fcm.googleapis.com/fcm/send';
        if ($device_type == "android") {
            $fields = array(
                'content_available' => true,
                'to'                => $regId,
                'notification'      => $notification,
                'data'              => $data
            );
        } else {
            $fields = array(
                'content_available' => true,
                'to'                => $regId,
                'notification'      => $notification,
                'data'              => $data
            );
        }
        // Your Firebase Server API Key
        $headers = array(
            'Authorization:key=AAAAgcNqugA:APA91bE0b225pJnZFw6y80xd8-KEt5QSGY5Oac1ETCkbvip4In2MsEo05Jus1brSmdUqLY_gRIoYsQYT_S67A46Sk-zL3H-shnThnAcdQArMaapGBz4DWg5xEvsEXIYKkEEabLyzAfmr',
            'Content-Type:application/json'
        );
        // Open curl connection
        $ch = curl_init();
        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);

        // return $result;

        if ($result === false) {
            die('Curl failed: ' . curl_error($ch));
        }
        curl_close($ch);
    }

}
