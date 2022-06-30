<?php
namespace App\Services;


class TransactionService extends BaseService{

    public function __construct(){
        
        $this->db = \Config\Database::connect();
    }

    public function checkTransactionCode($code, $amount){
        $sql = "SELECT * FROM transaction WHERE amount=$amount AND transaction_code='".$code."'";
        $result = $this->db->query($sql)->getResultArray();
        
        if(!empty($result)){
            return true;
        }else
            return false;
    }

    public function getAllTransactions($name, $dataInfo){

        switch ($name) {
            case 'vnpay':
                $query = "SELECT person_attribute.`value`, transaction_vnpay.vnp_bank_code, transaction_vnpay.vnp_card_type, transaction_vnpay.vnp_amount, transaction_vnpay.vnp_pay_date, `transaction`.status FROM transaction_vnpay 
                    LEFT JOIN `transaction` ON `transaction`.transaction_id = transaction_vnpay.transaction_id
                    LEFT JOIN wallet ON wallet.wallet_id = `transaction`.wallet_id
                    LEFT JOIN users ON users.user_id = wallet.user_id
                    LEFT JOIN person ON person.person_id = users.person_id
                    LEFT JOIN person_attribute ON person_attribute.person_id = person.person_id
                    WHERE transaction_vnpay.voided = 0 AND `transaction`.status = 'PAID'";

                    if($dataInfo['info'] != ''){
                        $query .= " AND (person_attribute.`value` LIKE '%".$dataInfo['info']."%' OR transaction_vnpay.vnp_bank_code LIKE '%".$dataInfo['info']."%')";
                    }

                    if($dataInfo['start'] != ''){
                        $newDateStart = \DateTime::createFromFormat('d/m/Y H:i', $dataInfo['start']);
                        $start = $newDateStart->format('Y-m-d H:i');
                        $query .= " AND transaction_vnpay.vnp_pay_date >= '".$start."'";
                    }

                    if($dataInfo['start'] != ''){
                        $newDateEnd = \DateTime::createFromFormat('d/m/Y H:i', $dataInfo['end']);
                        $end = $newDateEnd->format('Y-m-d H:i');
                        $query .= " AND transaction_vnpay.vnp_pay_date <= '".$end."'";
                    }

                    $query .= " ORDER BY transaction_vnpay.vnp_pay_date DESC";

                break;
            case 'momo':
                $query = "SELECT person_attribute.`value`, transaction_momo.amount, `transaction`.date_created FROM transaction_momo 
                    LEFT JOIN `transaction` ON `transaction`.transaction_id = transaction_momo.transaction_id
                    LEFT JOIN wallet ON wallet.wallet_id = `transaction`.wallet_id
                    LEFT JOIN users ON users.user_id = wallet.user_id
                    LEFT JOIN person ON person.person_id = users.person_id
                    LEFT JOIN person_attribute ON person_attribute.person_id = person.person_id
                    WHERE transaction_momo.voided = 0  AND `transaction`.status = 'PAID'";

                    if($dataInfo['info'] != ''){
                        $query .= " AND (person_attribute.`value` LIKE '%".$dataInfo['info']."%')";
                    }

                    if($dataInfo['start'] != ''){
                        $newDateStart = \DateTime::createFromFormat('d/m/Y H:i', $dataInfo['start']);
                        $start = $newDateStart->format('Y-m-d H:i');
                        $query .= " AND `transaction`.date_created >= '".$start."'";
                    }

                    if($dataInfo['start'] != ''){
                        $newDateEnd = \DateTime::createFromFormat('d/m/Y H:i', $dataInfo['end']);
                        $end = $newDateEnd->format('Y-m-d H:i');
                        $query .= " AND `transaction`.date_created <= '".$end."'";
                    }

                    $query .= " ORDER BY `transaction`.date_created DESC";
                break;
            default:
                # code...
                break;
        }

        log_message('error',$query);

        return $this->db->query($query)->getResultArray();

    }

    public function getTransactions($name, $dataInfo, $start_tr, $limit_tr){

        switch ($name) {
            case 'vnpay':
                $query = "SELECT person_attribute.`value`, transaction_vnpay.vnp_bank_code, transaction_vnpay.vnp_card_type, transaction_vnpay.vnp_amount, transaction_vnpay.vnp_pay_date, `transaction`.status FROM transaction_vnpay 
                    LEFT JOIN `transaction` ON `transaction`.transaction_id = transaction_vnpay.transaction_id
                    LEFT JOIN wallet ON wallet.wallet_id = `transaction`.wallet_id
                    LEFT JOIN users ON users.user_id = wallet.user_id
                    LEFT JOIN person ON person.person_id = users.person_id
                    LEFT JOIN person_attribute ON person_attribute.person_id = person.person_id
                    WHERE transaction_vnpay.voided = 0 AND `transaction`.status = 'PAID'";

                    if($dataInfo['info'] != ''){
                        $query .= " AND (person_attribute.`value` LIKE '%".$dataInfo['info']."%' OR transaction_vnpay.vnp_bank_code LIKE '%".$dataInfo['info']."%')";
                    }

                    if($dataInfo['start'] != ''){
                        $newDateStart = \DateTime::createFromFormat('d/m/Y H:i', $dataInfo['start']);
                        $start = $newDateStart->format('Y-m-d H:i');
                        $query .= " AND transaction_vnpay.vnp_pay_date >= '".$start."'";
                    }

                    if($dataInfo['start'] != ''){
                        $newDateEnd = \DateTime::createFromFormat('d/m/Y H:i', $dataInfo['end']);
                        $end = $newDateEnd->format('Y-m-d H:i');
                        $query .= " AND transaction_vnpay.vnp_pay_date <= '".$end."'";
                    }

                    $query .= " ORDER BY transaction_vnpay.vnp_pay_date DESC LIMIT $start_tr, $limit_tr";

                break;
            case 'momo':
                $query = "SELECT person_attribute.`value`, transaction_momo.amount, `transaction`.date_created FROM transaction_momo 
                    LEFT JOIN `transaction` ON `transaction`.transaction_id = transaction_momo.transaction_id
                    LEFT JOIN wallet ON wallet.wallet_id = `transaction`.wallet_id
                    LEFT JOIN users ON users.user_id = wallet.user_id
                    LEFT JOIN person ON person.person_id = users.person_id
                    LEFT JOIN person_attribute ON person_attribute.person_id = person.person_id
                    WHERE transaction_momo.voided = 0  AND `transaction`.status = 'PAID'";

                    if($dataInfo['info'] != ''){
                        $query .= " AND (person_attribute.`value` LIKE '%".$dataInfo['info']."%')";
                    }

                    if($dataInfo['start'] != ''){
                        $newDateStart = \DateTime::createFromFormat('d/m/Y H:i', $dataInfo['start']);
                        $start = $newDateStart->format('Y-m-d H:i');
                        $query .= " AND `transaction`.date_created >= '".$start."'";
                    }

                    if($dataInfo['start'] != ''){
                        $newDateEnd = \DateTime::createFromFormat('d/m/Y H:i', $dataInfo['end']);
                        $end = $newDateEnd->format('Y-m-d H:i');
                        $query .= " AND `transaction`.date_created <= '".$end."'";
                    }

                    $query .= " ORDER BY `transaction`.date_created DESC LIMIT $start_tr, $limit_tr";
                break;
            default:
                # code...
                break;
        }

        return $this->db->query($query)->getResultArray();

    }

    public function getAllAutoTransferHistory($info){

        $sql = "SELECT * FROM auto_transfer_history";
        if ($info != '') {
            $sql .= " WHERE phone_number like '%" . $info . "%'";
        }

        $sql .= " ORDER BY date_modify DESC";

        return $this->db->query($sql)->getResultArray();
    }

}