<?php
namespace App\Services;

class AppointmentService extends BaseService{

    public function __construct(){
        $this->db = \Config\Database::connect();
    }

    public function getAppointmentList($info, $date_from, $date_to, $rCode){
        $sql = "SELECT aa.appointment_code, patient.given_name as patient_name, rh.referral_code as code, provider.given_name as provider_name, ats.start_date, ats.end_date, aa.`status` FROM appointmentscheduling_appointment as aa 
                    LEFT JOIN appointmentscheduling_time_slot ats ON ats.time_slot_id = aa.time_slot_id
                    LEFT JOIN users creator ON creator.user_id = ats.creator
                    LEFT JOIN person_name provider ON provider.person_id = creator.person_id
                    LEFT JOIN person_name patient ON patient.person_id = aa.patient_id
                    LEFT JOIN person_attribute pa ON pa.person_id = aa.patient_id
                    LEFT JOIN referral_history rh ON rh.phone_number = pa.`value`
                    WHERE aa.`status` <> 'WAITING_PAYMENT' AND aa.`status` <> 'CANCELLED' ";

        if($info !== ''){
            $sql .= " AND (aa.appointment_code like '%$info%' OR patient.given_name like '%$info%')";
        }

        if($date_from != ''){
            $from = \DateTime::createFromFormat('d/m/Y', $date_from);
            $dateFrom = $from->format('Y-m-d 00:00:01');
            $sql .= " AND ats.start_date >= '$dateFrom'";
        }

        if($date_to != ''){
            $to = \DateTime::createFromFormat('d/m/Y', $date_to);
            $dateTo = $to->format('Y-m-d 23:59:59');
            $sql .= " AND ats.end_date <= '$dateTo'";
        }

        if($rCode !== ''){
            $sql .= " AND (rh.referral_code = '$rCode')";
        }

        $sql .= " GROUP BY aa.appointment_code ORDER BY aa.appointment_code DESC";
        // log_message('error', $sql);
        $appointment = $this->db->query($sql);
        $result = $appointment->getResultArray();
        return $result;
    }

    public function getAppointmentFollowList($info, $date_from, $date_to){
        $today = date('Y-m-d 00:00:01');
        $sql = "SELECT aaf.appointment_follow_id, aaf.appointment_id, aa.appointment_code,pi.identifier as ma_benh_nhan,patient.given_name as ten_benh_nhan, pa.`value` as sdt,provider.given_name as ten_bac_si, aaf.follow_date as ngay_tai_kham, aaf.follow_result FROM appointmentscheduling_appointment_follow  aaf
                    LEFT JOIN appointmentscheduling_appointment aa ON aa.appointment_id = aaf.appointment_id
                    LEFT JOIN provider pro ON pro.provider_id = aaf.follower
                    LEFT JOIN appointmentscheduling_time_slot as ats ON ats.time_slot_id = aa.time_slot_id
                    LEFT JOIN users as u ON u.user_id = ats.creator
                    LEFT JOIN person_name as provider ON u.person_id = provider.person_id
                    LEFT JOIN person_name as patient ON patient.person_id = aa.patient_id
                    LEFT JOIN patient_identifier as pi ON pi.patient_id = aa.patient_id
                    LEFT JOIN person_attribute as pa ON pa.person_id = pi.patient_id
                    WHERE aaf.follow_date >='$today'";
        // provider.person_id > 1 AND

        if($info !== ''){
            $sql .= " AND (pi.identifier like '%$info%' OR patient.given_name like '%$info%' OR pa.`value` like '%$info%' OR provider.given_name like '%$info%')";
        }

        if($date_from != ''){
            $from = \DateTime::createFromFormat('d/m/Y', $date_from);
            $dateFrom = $from->format('Y-m-d 00:00:01');
            $sql .= " AND aaf.follow_date >= '$dateFrom'";
        }

        if($date_to != ''){
            $to = \DateTime::createFromFormat('d/m/Y', $date_to);
            $dateTo = $to->format('Y-m-d 23:59:59');
            $sql .= " AND aaf.follow_date <= '$dateTo'";
        }

        $sql .= " ORDER BY aaf.follow_date ASC";

        // log_message('error', $sql);
        $appointment = $this->db->query($sql);
        $result = $appointment->getResultArray();
        return $result;
    }

    public function getAppointmentFollowListOver($info, $date_from, $date_to){
        $today = date('Y-m-d 00:00:01');
        $sql = "SELECT aaf.appointment_follow_id, aaf.appointment_id, aa.appointment_code,pi.identifier as ma_benh_nhan,patient.given_name as ten_benh_nhan, pa.`value` as sdt,provider.given_name as ten_bac_si, aaf.follow_date as ngay_tai_kham, aaf.follow_result FROM appointmentscheduling_appointment_follow  aaf
                    LEFT JOIN appointmentscheduling_appointment aa ON aa.appointment_id = aaf.appointment_id
                    LEFT JOIN provider pro ON pro.provider_id = aaf.follower
                    LEFT JOIN appointmentscheduling_time_slot as ats ON ats.time_slot_id = aa.time_slot_id
                    LEFT JOIN users as u ON u.user_id = ats.creator
                    LEFT JOIN person_name as provider ON u.person_id = provider.person_id
                    LEFT JOIN person_name as patient ON patient.person_id = aa.patient_id
                    LEFT JOIN patient_identifier as pi ON pi.patient_id = aa.patient_id
                    LEFT JOIN person_attribute as pa ON pa.person_id = pi.patient_id
                    WHERE aaf.follow_date <'$today'";

        // provider.person_id > 1 AND

        if($info !== ''){
            $sql .= " AND (pi.identifier like '%$info%' OR patient.given_name like '%$info%' OR pa.`value` like '%$info%' OR provider.given_name like '%$info%')";
        }

        if($date_from != ''){
            $from = \DateTime::createFromFormat('d/m/Y', $date_from);
            $dateFrom = $from->format('Y-m-d 00:00:01');
            $sql .= " AND aaf.follow_date >= '$dateFrom'";
        }

        if($date_to != ''){
            $to = \DateTime::createFromFormat('d/m/Y', $date_to);
            $dateTo = $to->format('Y-m-d 23:59:59');
            $sql .= " AND aaf.follow_date <= '$dateTo'";
        }

        $sql .= " ORDER BY aaf.follow_date DESC";

        // log_message('error', $sql);
        $appointment = $this->db->query($sql);
        $result = $appointment->getResultArray();
        return $result;
    }

    public function getAppointmentDetail($appointment_code){
        $query = "SELECT aa.appointment_id, pn.given_name, pat.value, po.gender, po.birthdate, users.email, pad.address1,aa.patient_id,aa.visit_id,provider.given_name as provider_name FROM appointmentscheduling_appointment aa
                LEFT JOIN patient pa ON pa.patient_id = aa.patient_id
                LEFT JOIN person po ON po.person_id = pa.patient_id
                LEFT JOIN person_name pn ON pn.person_id = po.person_id
                LEFT JOIN person_address pad ON pad.person_id = po.person_id
                LEFT JOIN person_attribute pat ON pat.person_id = po.person_id
                LEFT JOIN users ON users.person_id = po.person_id
                LEFT JOIN appointmentscheduling_time_slot ats ON aa.time_slot_id = ats.time_slot_id
                LEFT JOIN users creator ON ats.creator = creator.user_id
                LEFT JOIN person_name provider ON creator.person_id = provider.person_id
                WHERE aa.appointment_code='$appointment_code'";
        $patientInfo = $this->db->query($query)->getResultArray();

        if($patientInfo[0]['gender'] === 'M' || $patientInfo[0]['gender'] === 'MALE' || $patientInfo[0]['gender'] === 'Nam'){
            $patientInfo[0]['gender1'] = 'Nam';
        }else if($patientInfo[0]['gender'] === 'F' || $patientInfo[0]['gender'] === 'FEMALE' || $patientInfo[0]['gender'] === 'Nữ'){
            $patientInfo[0]['gender1'] = 'Nữ';
        }else{
            $patientInfo[0]['gender1'] = '';
        }

        $patientInfo[0]['birthdate1'] = date('d-m-Y', strtotime($patientInfo[0]['birthdate']));

        $patient_id = $patientInfo[0]['patient_id'];
        $visit_id = $patientInfo[0]['visit_id'];

        // Get all encounter
        $query_getEncounter = "SELECT * FROM encounter WHERE patient_id=".$patient_id." AND visit_id=".$visit_id;
        
        $encounter = $this->db->query($query_getEncounter)->getResultArray();
        log_message('error', 'Test SQL: '.$query_getEncounter);
        
        //        $res = db_connect()->getLastQuery();
        //    echo '<pre>';
        //    print_r($encounter);
        //    echo '</pre>';
        //    die();

        $encounter_id = $encounter[0]['encounter_id'];
        // Get All Order
        $order = $this->getOrders($encounter_id);

        $query1 = "SELECT * FROM appointmentscheduling_appointment WHERE appointment_code='$appointment_code'";
        $appointmentInfo1 = $this->db->query($query1);
        $appointmentInfo = $appointmentInfo1->getResultArray();
            
            return array('patientInfo' => $patientInfo[0], 'appointmentInfo' => $appointmentInfo[0], 'order' => $order);
    }

    public function getObs($encounter_id){
        $query = "SELECT * FROM obs WHERE encounter_id=$encounter_id";
        return $this->db->query($query)->getResultArray();
    }

    public function getEncounterDiagnosis($encounter_id){
        $query = "SELECT * FROM encounter_diagnosis WHERE encounter_id=$encounter_id ORDER BY date_created";
        $diagnosis = $this->db->query($query)->getResultArray();
        $chandoan_sobo = array();
        $chandoan_xacdinh = array();
        if ($diagnosis) {
            foreach ($diagnosis as $k => $dia) {
                if ($dia['diagnosis_coded'] != null) {
                    $concept_id = $dia['diagnosis_coded'];
                    $chandoan = $this->db->query("SELECT * FROM concept_name WHERE concept_id=$concept_id")->getResultArray();
                    array_push($chandoan_sobo, $chandoan[0]['name']);
                } else {
                    array_push($chandoan_xacdinh, $dia['diagnosis_non_coded']);
                }
            }
            return array(
                'chandoan_sobo'     => $chandoan_sobo,
                'chandoan_xacdinh'  => $chandoan_xacdinh
            );
        } else
            return '';
    }

    public function getOrders($encounter_id){
        $query = "SELECT orders.concept_id, orders.order_number, orders.order_type_id, concept_name.name FROM orders 
                    LEFT JOIN concept_name ON concept_name.concept_id = orders.concept_id
                    WHERE orders.encounter_id=$encounter_id
                    GROUP BY orders.order_number";
        $orders = $this->db->query($query)->getResultArray();

        $thuoc_arr = array();
        $cls_arr = array();
        foreach($orders as $k => $ord){

            $id = str_replace('ORD-','',$ord['order_number']);
            // $type = $ord['order_type_id'];
            // $orders[$k]['ten_thuoc'] = '';
            // $orders[$k]['cach_su_dung'] = '';

            //$type = 5: Thuốc
            if($ord['order_type_id'] == 5){
                // Tên thuốc
                $thuoc['ten_thuoc'] = $orders[$k]['name'];

                // Hướng dẫn sử dụng
                $query1 = "SELECT * FROM drug_order_new WHERE order_id=$id";
                $orders_new = $this->db->query($query1)->getResultArray();
                if($orders_new){

                    if($orders_new[0]['eating_status']=='AFTER'){
                        $eat = ' Sau ăn';
                    }else if($orders_new[0]['eating_status']=='BEFORE'){
                        $eat = ' Trước ăn';
                    }else{
                        $eat = '';
                    }

                    $time_use = str_replace('MORNING', 'Sáng',$orders_new[0]['time_uses']);
                    $time_use = str_replace('AFTERNOON', 'Chiều',$time_use);
                    $time_use = str_replace('NOON', 'Trưa',$time_use);
                    $time_use = str_replace('EVENING', 'Tối',$time_use);

                    $thuoc['cach_su_dung'] = $orders_new[0]['dosing_instructions'].' ('.$time_use;
                    if($eat == ''){
                        $thuoc['cach_su_dung'] .= ' )';
                    }else{
                        $thuoc['cach_su_dung'] .= ') ( '.$eat.' )';
                    }
                    array_push($thuoc_arr, $thuoc);
                }
            }



            //$type = 4: CLS
            if($ord['order_type_id'] == 4){
                $query2 = "SELECT * FROM cls_order 
                            LEFT JOIN order_set ON order_set.order_set_id = cls_order.order_set_id
                            WHERE order_id=$id";
                $orders_cls = $this->db->query($query2)->getResultArray();
                if($orders_cls){
                    $cls_group = $orders_cls[0]['name'];
                    $order_set_id = $orders_cls[0]['order_set_id'];
                    $query3 = "SELECT order_set_member.order_set_id, order_set_member.concept_id, order_set_member.concept_non_code, concept_name.name FROM order_set_member 
                                LEFT JOIN concept_name ON concept_name.concept_id = order_set_member.concept_id
                                WHERE order_set_id = $order_set_id GROUP BY order_set_member.concept_id";
                    $orders_cls_member = $this->db->query($query3)->getResultArray();
                    if($orders_cls_member){
                        $cls_member = $orders_cls_member;
                    }
                }

                array_push($cls_arr, array(
                    'group' => $cls_group,
                    'member'=> $cls_member
                ));
            }

        }

        return array(
            'thuoc' => $thuoc_arr,
            'cls'   => $cls_arr
        );
    }

    //lấy uuid của order(encounter_id, order_type_id)
    public function getOrderUUID($encounter, $type){
        $query = "SELECT uuid FROM orders WHERE encounter_id=$encounter AND order_type_id=$type";
        return $this->db->query($query)->getResultArray();
    }

    //theo doi
    public function getAppointmentFollow($appointment_id){
        $query = "SELECT follow_description,follow_result,follow_date  FROM appointmentscheduling_appointment_follow WHERE appointment_id=$appointment_id ORDER BY follow_date";
        return $this->db->query($query)->getResultArray();
    }

    public function getCHRONIC($patientId){
        $query = "SELECT * FROM conditions WHERE patient_id=$patientId";
        return $this->db->query($query)->getResultArray();
    }

    public function getAllergy($patientId){
        $query = "SELECT * FROM allergy WHERE patient_id=$patientId";
        return $this->db->query($query)->getResultArray();
    }

    public function getDayAgo($date){
        $date1 = date('Y-m-d');
        $date2 = date('Y-m-d ', strtotime($date));

        $diff = abs(strtotime($date2) - strtotime($date1));
        $years = floor($diff / (365 * 60 * 60 * 24));
        $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
        $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));

        $result = '';
        if ($years > 0) {
            $result .= $years . " năm";
        } else if ($months > 0) {
            $result .= $months . " tháng";
        } else if ($days > 0) {
            $result .= $days . " ngày";
        } else {
            $result = '';
        }

        return $result;
    }

    public function history_appointment($patient_id){
        $query = "SELECT appointmentscheduling_appointment.visit_id, appointmentscheduling_appointment.reason, appointmentscheduling_appointment.reason_detail, encounter.encounter_id, appointmentscheduling_appointment.date_changed
                FROM appointmentscheduling_appointment 
                LEFT JOIN encounter ON encounter.visit_id = appointmentscheduling_appointment.visit_id
                LEFT JOIN encounter_diagnosis ON encounter_diagnosis.encounter_id = encounter.encounter_id
                WHERE appointmentscheduling_appointment.patient_id=$patient_id GROUP BY appointmentscheduling_appointment.visit_id ORDER BY date_changed DESC";
        $all_appointment =  $this->db->query($query)->getResultArray();

        if ($all_appointment) {
            foreach ($all_appointment as $k => $app) {
                $all_appointment[$k]['date'] = $this->getDayAgo($app['date_changed']);
                $chandoan_sobo = array();
                $chandoan_xacdinh = array();
                $encounter_id = $app['encounter_id'];
                $query1 = "SELECT encounter_diagnosis.diagnosis_coded, encounter_diagnosis.diagnosis_non_coded, encounter_diagnosis.diagnosis_coded_name, encounter_diagnosis.certainty, concept_name.name 
                            FROM encounter_diagnosis 
                            LEFT JOIN concept_name ON concept_name.concept_id = encounter_diagnosis.diagnosis_coded
                            WHERE encounter_id=$encounter_id";
                $encounter_diagnosis = $this->db->query($query1)->getResultArray();

                if ($encounter_diagnosis) {
                    foreach ($encounter_diagnosis as $h => $ed) {
                        //chẩn đoán sơ bộ
                        if ($ed['certainty'] == 'PROVISIONAL') {
                            if ($ed['diagnosis_coded'] != null) {
                                array_push($chandoan_sobo, $ed['name']);
                            } else {
                                array_push($chandoan_sobo, $ed['diagnosis_non_coded']);
                            }
                        }
                        //chẩn đoán xác định
                        if ($ed['certainty'] == 'CONFIRMED') {
                            if ($ed['diagnosis_coded'] != null) {
                                array_push($chandoan_xacdinh, $ed['name']);
                            } else {
                                array_push($chandoan_xacdinh, $ed['diagnosis_non_coded']);
                            }
                        }
                    }
                }

                $all_appointment[$k]['chandoan_sobo'] = $chandoan_sobo;
                $all_appointment[$k]['chandoan_xacdinh'] = $chandoan_xacdinh;
            }
        }
        return $all_appointment;
    }

    public function updateAppointmentFollowStatus($id, $content){

        $sql = "UPDATE appointmentscheduling_appointment_follow SET follow_result='".$content."' WHERE appointment_follow_id=".$id;
        // log_message('error', $sql);
        return $this->db->query($sql);
    }

    public function getAppointmentNotifyList($time){
        $day = date('Y-m-d 08:00:00', strtotime('- '.$time.' days'));
        $sql = "SELECT pa.`value`, aaf.follow_date FROM appointmentscheduling_appointment_follow aaf 
        LEFT JOIN appointmentscheduling_appointment aa ON aa.appointment_id = aaf.appointment_id
        LEFT JOIN person_attribute pa ON pa.person_id = aa.patient_id
        WHERE aaf.follow_date = '$day' GROUP BY pa.`value`";

        $appointment = $this->db->query($sql);
        $result = $appointment->getResultArray();
        return $result;
    }

    public function getAccountBalance($info, $fieldSort){
        $query_getAllAccountBalance = "SELECT wa.user_id, wa.balance, u.person_id, pn.given_name, pa.`value`, pi.identifier FROM wallet wa
                LEFT JOIN users u ON u.user_id = wa.user_id
                LEFT JOIN person_name pn ON pn.person_id = u.person_id
                LEFT JOIN person_attribute pa ON pa.person_id = u.person_id
                LEFT JOIN patient_identifier pi ON pi.patient_id = u.person_id
                WHERE wa.voided = 0 AND u.retired = 0 AND u.person_id <> 1 AND u.person_id <> 9";
        if($info != ''){
            $query_getAllAccountBalance .= " AND (pn.given_name like '%".$info."%' OR pa.`value` like '%".$info."%' OR pi.identifier like '%".$info."%')";
        }


        $query_getAllAccountBalance .= " ORDER BY wa.".$fieldSort." DESC";

        return $this->db->query($query_getAllAccountBalance)->getResultArray();
    }

    public function getAppointmentManage($info, $date_from, $date_to){
        $this->db = \Config\Database::connect();
        $sql = "SELECT aa.appointment_code,aa.appointment_id, patient.given_name as patient_name, pa.`value` sdt, rh.referral_code as code, provider.given_name as provider_name, ats.start_date, ats.end_date, aa.`status`, afm.symptom, afm.diagnose, afm.feedback, afm.note, aaf.follow_date FROM appointmentscheduling_appointment as aa 
                    LEFT JOIN appointmentscheduling_time_slot ats ON ats.time_slot_id = aa.time_slot_id
                    LEFT JOIN users creator ON creator.user_id = ats.creator
                    LEFT JOIN person_name provider ON provider.person_id = creator.person_id
                    LEFT JOIN person_name patient ON patient.person_id = aa.patient_id
                    LEFT JOIN person_attribute pa ON pa.person_id = aa.patient_id
                    LEFT JOIN referral_history rh ON rh.phone_number = pa.`value`
                    LEFT JOIN appointment_follow_manage afm ON afm.appointment_id = aa.appointment_id
	                LEFT JOIN appointmentscheduling_appointment_follow aaf ON aaf.appointment_id = aa.appointment_id
                    WHERE (aa.`status` = 'COMPLETED' OR aa.`status` = 'WAITING_EXAMINATION' OR aa.`status` = 'INCONSULTATION')";

        if ($info !== '') {
            $sql .= " AND (aa.appointment_code like '%$info%' OR patient.given_name like '%$info%')";
        }

        if ($date_from != '') {
            $from = \DateTime::createFromFormat('d/m/Y', $date_from);
            $dateFrom = $from->format('Y-m-d 00:00:01');
            $sql .= " AND ats.start_date >= '$dateFrom'";
        }

        if ($date_to != '') {
            $to = \DateTime::createFromFormat('d/m/Y', $date_to);
            $dateTo = $to->format('Y-m-d 23:59:59');
            $sql .= " AND ats.end_date <= '$dateTo'";
        }

        $sql .= " GROUP BY aa.appointment_code ORDER BY aa.appointment_code DESC";
        // log_message('error', $sql);
        $appointment = $this->db->query($sql);
        $result = $appointment->getResultArray();
        return $result;
    }

    public function editAppointmentFollowManage($data){
        $query = "SELECT * FROM appointment_follow_manage WHERE appointment_id=".$data['appointment_id'];
        $check = $this->db->query($query)->getResultArray();
        if ($check){

            //UPDATE
            $update = "UPDATE appointment_follow_manage SET
                        symptom='".$data['symptom']."' ,
                        diagnose='".$data['diagnose']."' ,
                        feedback='".$data['feedback']."' ,
                        re_exam_date='".$data['re_exam']."' ,
                        note='".$data['note']."'
                        WHERE appointment_id=".$data['appointment_id'];

            return $this->db->query($update);
        } else {

            //CREATE
            $create = "INSERT INTO appointment_follow_manage(appointment_id,symptom,diagnose,feedback,re_exam_date,note) 
                        VALUES('".$data['appointment_id']."','".$data['symptom']."','".$data['diagnose']."','".$data['feedback']."','".$data['re_exam']."','".$data['note']."')";

            return $this->db->query($create);
        }
    }
}