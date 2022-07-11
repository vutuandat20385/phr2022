<?php

namespace App\Controllers;

require 'vendor/autoload.php';

use App\Services\HomeService;
use App\Services\SettingService;
use App\Services\TransactionService;

class Transactions extends BaseController {

	public function __construct(){
		$this->service = new HomeService();
        $this->setting = new SettingService();
        $this->transaction = new TransactionService();
	}

	public function index() {
		$data['panelTitle'] = 'THEO DÕI KÊNH GIAO DỊCH NẠP TIỀN';
        $data = $this->getAfterLoginLayout($data, 'Trang quản trị', 'AfterLogin/pages/transactions/index');
		return view('AfterLogin/main', $data);
	}

    public function addTransaction(){
        $sms_content    = $this->request->getVar('sms_content');
        $str1 = 'TK 9501166668888 ';
        $sms_content1 = trim(str_replace($str1,'',$sms_content));

        // Xử lý nội dung tin nhắn
        $pos1 = strpos($sms_content1, 'GD:');
        $pos2 = strpos($sms_content1, 'VND');
        $sms_amount_str = substr($sms_content1, $pos1, $pos2 - $pos1);
        $sms_amount = trim(str_replace('GD: +','',$sms_amount_str));
        $amount = trim(str_replace(',','',$sms_amount));
        $sms_content2 = trim(str_replace($sms_amount_str,'',$sms_content1));
        $pos3 = strpos($sms_content2, 'VND');
        $pos4 = strpos($sms_content2, 'SD');
        $sms_timeTransfer = substr($sms_content2, $pos3, $pos4 - $pos3);
        $time = trim(str_replace('VND ','',$sms_timeTransfer));
        $sms_content3 = trim(str_replace($sms_timeTransfer,'',$sms_content2));

        $subject = $sms_content3;
        preg_match_all('/(03|05|07|08|09|01[2|6|8|9])+([0-9]{8})\b/', $subject, $matches);
        $numbers = $matches[0];

        $phone = $numbers[0];
        
        $time_arr = explode(' ',$time);

        $time1_arr = explode('/',$time_arr[0]);
        $time1 = '20'.$time1_arr[2].'-'.$time1_arr[1].'-'.$time1_arr[0];
        $time2 = $time_arr[1];

        $time_transfer = $time1.' '.$time2;

        // Check phone_number
        $user_id = $this->transaction->checkPhoneNumber(trim($phone));
        
        if($user_id && $amount != ''){
            // Get wallet uuid
            $walletRecord = $this->transaction->getWalletInfo($user_id);

            $wallet             = $walletRecord['uuid'];
            $transactionCode    = 'SMS_TRANSFER_'.strtotime('now');
            $form               = 'TRANSFER';
            $transactionType    = 'ADMIN_ADD';
            $type               = 'basetransaction';
            $note               = 'Chuyển khoản ngân hàng';

            $transactionData = array(
                'wallet' 			=> $wallet,
                'amount' 			=> $amount,
                'transactionCode' 	=> $transactionCode,
                'form' 				=> $form,
                'transactionType' 	=> $transactionType,
                'type' 				=> $type,
                'note' 				=> $note 
            );

            $add = $this->apiTransaction($transactionData);
            // print_r(json_encode($add).'-');
            if(isset($add['uuid']) && $add['uuid'] != ''){
                $status = 'Nạp tiền thành công';
                $dataHistory = array(
                    'phone_number' 	        => $phone,
                    'transaction_type' 	    => $transactionType,
                    'amount' 			    => $amount,
                    'transaction_code' 	    => $transactionCode,
                    'note' 				    => $note,
                    'time_transfer'         => $time_transfer,
                    'sms_content'           => $sms_content,
                    'transaction_status' 	=> $status,
                    'status' 		        => 1
                );

                $this->transaction->saveHistoryAutoTransfer($dataHistory);

                $result['status'] = 1;
                $result['msg'] = $status;

            }else{
                // Kiểm tra transaction_code
                if($transactionCode != ''){
                    $checkCode = $this->transaction->checkTransactionCode($transactionCode, $amount);
                    if($checkCode){
                        $status = 'Nạp tiền thành công';
                        $s = 1;
                    }else{
                        $status = 'Nạp tiền thất bại';
                        $s = 0;
                    }
                }
               
                $dataHistory = array(
                    'phone_number' 	        => $phone,
                    'transaction_type' 	    => $transactionType,
                    'amount' 			    => $amount,
                    'transaction_code' 	    => $transactionCode,
                    'note' 				    => $note,
                    'time_transfer'         => $time_transfer,
                    'sms_content'           => $sms_content,
                    'transaction_status' 	=> $status,
                    'status' 		        => $s
                );

                $this->transaction->saveHistoryAutoTransfer($dataHistory);

                $result['status'] = 0;
                $result['msg'] = $status;
            }
        }else{
            $status = 'Thông tin không hợp lệ - Nạp tiền thất bại';
            $wallet             = '';
            $transactionCode    = '';
            $form               = 'TRANSFER';
            $transactionType    = 'ADMIN_ADD';
            $type               = 'basetransaction';
            $note               = 'Cộng tiền từ SMS chuyển khoản';

            $dataHistory = array(
                    'phone_number' 	        => $phone,
                    'transaction_type' 	    => $transactionType,
                    'amount' 			    => $amount,
                    'transaction_code' 	    => $transactionCode,
                    'note' 				    => $note,
                    'time_transfer'         => $time_transfer,
                    'sms_content'           => $sms_content,
                    'transaction_status' 	=> $status,
                    'status'                => 0
            );

            $this->transaction->saveHistoryAutoTransfer($dataHistory);

            $result['status'] = 0;
            $result['msg'] = $status;
        }

        return json_encode(array(
            'status'    => 1,
            'msg'       => $status
        ));
    }

    public function autoTransferHistory(){
        
			$data['user'] 	= session()->get('user');
			$data['pageTitle'] = 'Danh sách chuyển khoản nạp tiền';
			
			$page=(int)(($this->request->getVar('page')!==null) ? $this->request->getVar('page') : 1)-1;

			$data['info']=($this->request->getVar('info')!==null) ? $this->request->getVar('info'):'';

			$perPage =  20;
		
			$allHistory = $this->transaction->getAllAutoTransferHistory($data['info']);
			
			$countAll = count($allHistory);

			if($allHistory){
				if($countAll > 1){

					service('pager')->makeLinks($page+1, $perPage, $countAll);
					$start = $page * $perPage;

					foreach($allHistory as $k => $value){
						$allHistory[$k]['index'] = $countAll - $start - $k;
					}
				}else{
					$allHistory[0]['index'] = 1;
				}
				
				

				$data['history'] = $allHistory;

				$data['currentPage'] = $page+1;
				$data['totalPages'] = ceil($countAll/$perPage);
			}else{
				$data['history'] = false;
			}

			$data 			= $this->getAfterLoginLayout($data, 'Danh sách chuyển khoản nạp tiền', 'AfterLogin/pages/transactions/autoTransferHistory');
			return view('AfterLogin/main', $data);
		
    }

    public function vnpayTransactions(){

        $data['user'] 	= session()->get('user');
        $data['pageTitle'] = 'Danh sách giao dịch VNPAY';
        $data['panelTitle'] = 'Danh sách giao dịch VNPAY';
        
        $page=(int)(($this->request->getVar('page')!==null) ? $this->request->getVar('page') : 1)-1;

        $data['info']	= ($this->request->getVar('info')!==null) ? $this->request->getVar('info'):'';
        $data['start']	= ($this->request->getVar('start')!==null) ? $this->request->getVar('start'):'';
        $data['end']	= ($this->request->getVar('end')!==null) ? $this->request->getVar('end'):'';

        $dataInfo = array(
            'info'  => $data['info'],
            'start' => $data['start'],
            'end'   => $data['end']
        );

        $perPage =  20;
    
        $allTransactions = $this->transaction->getAllTransactions('vnpay', $dataInfo);
        
        $countAll = count($allTransactions);

        if($allTransactions){
            if($countAll > 1){
            
                service('pager')->makeLinks($page+1, $perPage, $countAll);
                $start = $page * $perPage;
                
                $transactions = $this->transaction->getTransactions('vnpay', $dataInfo , $start, $perPage);

                foreach($transactions as $k => $value){
                    $transactions[$k]['index'] = $countAll - $start - $k;
                }
            }else{
                $transactions = $allTransactions;
                $transactions[0]['index'] = 1;
            }

            $data['transactions'] = $transactions;
            $data['currentPage'] = $page+1;
            $data['totalPages'] = ceil($countAll/$perPage);
        }else{
            $data['transactions'] = false;
        }

        $data = $this->getAfterLoginLayout($data, 'Danh sách giao dịch VNPAY', 'AfterLogin/pages/transactions/vnpay');
        return view('AfterLogin/main', $data);
		
    }

    public function momoTransactions(){
        $data['user'] 	= session()->get('user');
        $data['pageTitle'] = 'Danh sách giao dịch MOMO';
        $data['panelTitle'] = 'Danh sách giao dịch MOMO';
        
        $page=(int)(($this->request->getVar('page')!==null) ? $this->request->getVar('page') : 1)-1;

        $data['info']	= ($this->request->getVar('info')!==null) ? $this->request->getVar('info'):'';
        $data['start']	= ($this->request->getVar('start')!==null) ? $this->request->getVar('start'):'';
        $data['end']	= ($this->request->getVar('end')!==null) ? $this->request->getVar('end'):'';

        $dataInfo = array(
            'info'  => $data['info'],
            'start' => $data['start'],
            'end'   => $data['end']
        );

        $perPage =  20;
    
        $allTransactions = $this->transaction->getAllTransactions('momo', $dataInfo);
        
        $countAll = count($allTransactions);

        if($allTransactions){
            if($countAll > 1){
            
                service('pager')->makeLinks($page+1, $perPage, $countAll);
                $start = $page * $perPage;

                $transactions = $this->transaction->getTransactions('momo', $dataInfo , $start, $perPage);
                
                foreach($transactions as $k => $value){
                    $transactions[$k]['index'] = $countAll - $start - $k;
                }
            }else{
                $transactions = $allTransactions;
                $transactions[0]['index'] = 1;
            }

            $data['transactions'] = $transactions;
            $data['currentPage'] = $page+1;
            $data['totalPages'] = ceil($countAll/$perPage);
        }else{
            $data['transactions'] = false;
        }

        $data = $this->getAfterLoginLayout($data, 'Danh sách giao dịch MOMO', 'AfterLogin/pages/transactions/momo');
        return view('AfterLogin/main', $data);
	
    }


}
