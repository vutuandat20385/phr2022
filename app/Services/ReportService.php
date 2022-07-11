<?php
namespace App\Services;

// use App\Models\User2Model;

class ReportService extends BaseService{
    public function __construct(){
        $this->db = \Config\Database::connect();
        
    }

    public function getAllReportsInMonth(){
        $start = date('Y-m-d', strtotime('first day of last month'));
        $end = date('Y-m-d', strtotime('last day of last month'));

        // $start = '2021-06-01';
        // $end = '2022-06-30';
        $query = "SELECT aa.date_changed AS ngay_kham, pi.identifier AS ma_benh_nhan, pn.given_name AS ten_benh_nhan, pa.value, aa.appointment_code AS ma_cuoc_kham, pn2.given_name AS ten_bac_si FROM appointmentscheduling_appointment aa
                    LEFT JOIN patient_identifier pi ON pi.patient_id = aa.patient_id
                    LEFT JOIN person_name pn ON pn.person_id = aa.patient_id
                    LEFT JOIN person_attribute pa ON pa.person_id = aa.patient_id
                    LEFT JOIN appointmentscheduling_time_slot ats ON ats.time_slot_id = aa.time_slot_id
                    LEFT JOIN users u ON u.user_id = ats.creator
                    LEFT JOIN person_name pn2 ON pn2.person_id = u.person_id
                    WHERE aa.`status` = 'COMPLETED' AND (aa.date_changed >='".$start."' AND aa.date_changed <= '".$end."')";

        $reports = $this->db->query($query);

        return $reports->getResultArray();
    }

    public function getReportsInMonth($dataReport){
        $start = date('Y-m-d', strtotime('first day of last month'));
        $end = date('Y-m-d', strtotime('last day of last month'));

        // $start = '2021-06-01';
        // $end = '2022-06-30';
        $query = "SELECT aa.date_changed AS ngay_kham, pi.identifier AS ma_benh_nhan, pn.given_name AS ten_benh_nhan, pa.value, aa.appointment_code AS ma_cuoc_kham, pn2.given_name AS ten_bac_si FROM appointmentscheduling_appointment aa
                    LEFT JOIN patient_identifier pi ON pi.patient_id = aa.patient_id
                    LEFT JOIN person_name pn ON pn.person_id = aa.patient_id
                    LEFT JOIN person_attribute pa ON pa.person_id = aa.patient_id
                    LEFT JOIN appointmentscheduling_time_slot ats ON ats.time_slot_id = aa.time_slot_id
                    LEFT JOIN users u ON u.user_id = ats.creator
                    LEFT JOIN person_name pn2 ON pn2.person_id = u.person_id
                    WHERE aa.`status` = 'COMPLETED' AND (aa.date_changed >='".$start."' AND aa.date_changed <= '".$end."')
                    LIMIT ".$dataReport['start'].", ".$dataReport['limit'];

        $reports = $this->db->query($query);

        return $reports->getResultArray();
    }

    public function getAllPaymentInMonth(){
        $start = date('Y-m-d', strtotime('first day of last month'));
        $end = date('Y-m-d', strtotime('last day of last month'));

        // $start = '2021-06-01';
        // $end = '2022-06-30';
        $query = "SELECT pi.identifier ma_benh_nhan, pn.given_name ten_benh_nhan, pa.`value` so_dien_thoai, tran.form hinh_thuc_thanh_toan, tran.amount gia_kham, pc.promotion_code ma_khuyen_mai, tran.real_payment da_thanh_toan, tran.date_created ngay_thanh_toan, u.person_id FROM `transaction` tran
        LEFT JOIN wallet w ON w.wallet_id = tran.wallet_id
        LEFT JOIN users u ON u.user_id = w.user_id
        LEFT JOIN person_name pn ON pn.person_id = u.person_id
        LEFT JOIN person_attribute pa ON pa.person_id = u.person_id
        LEFT JOIN patient_identifier pi ON pi.patient_id = u.person_id
        LEFT JOIN promotion_code pc ON pc.promotion_code_id = tran.promotion_code_id
        WHERE tran.`status` = 'PAID' AND tran.transaction_type = 'PATIENT_BOOK_APPOINTMENT' AND u.retired = 0 AND (tran.date_created >='".$start."' AND tran.date_created <= '".$end."') AND u.person_id IN (SELECT patient_id FROM patient WHERE patient.voided = 0) ";

        $reports = $this->db->query($query);

        return $reports->getResultArray();
    }

    public function getPaymentInMonth($dataReport){
        $start = date('Y-m-d', strtotime('first day of last month'));
        $end = date('Y-m-d', strtotime('last day of last month'));

        // $start = '2021-06-01';
        // $end = '2022-06-30';
        $query = "SELECT pi.identifier ma_benh_nhan, pn.given_name ten_benh_nhan, pa.`value` so_dien_thoai, tran.form hinh_thuc_thanh_toan, tran.amount gia_kham, pc.promotion_code ma_khuyen_mai, tran.real_payment da_thanh_toan, tran.date_created ngay_thanh_toan, u.person_id FROM `transaction` tran
        LEFT JOIN wallet w ON w.wallet_id = tran.wallet_id
        LEFT JOIN users u ON u.user_id = w.user_id
        LEFT JOIN person_name pn ON pn.person_id = u.person_id
        LEFT JOIN person_attribute pa ON pa.person_id = u.person_id
        LEFT JOIN patient_identifier pi ON pi.patient_id = u.person_id
        LEFT JOIN promotion_code pc ON pc.promotion_code_id = tran.promotion_code_id
        WHERE tran.`status` = 'PAID' AND tran.transaction_type = 'PATIENT_BOOK_APPOINTMENT' AND u.retired = 0 AND (tran.date_created >='".$start."' AND tran.date_created <= '".$end."') AND u.person_id IN (SELECT patient_id FROM patient WHERE patient.voided = 0) LIMIT ".$dataReport['start'].", ".$dataReport['limit'];

        $reports = $this->db->query($query);

        return $reports->getResultArray();
    }

    public function getAppointmentHistory($person_id){
        $query = "SELECT aa.date_changed ngay_kham, pi.identifier ma_benh_nhan, pn.given_name ten_benh_nhan, aa.appointment_code ma_cuoc_kham, pn2.given_name bac_si FROM appointmentscheduling_appointment aa
                LEFT JOIN patient_identifier pi ON pi.patient_id = aa.patient_id
                LEFT JOIN person_name pn ON pn.person_id = aa.patient_id
                LEFT JOIN appointmentscheduling_time_slot ats ON ats.time_slot_id = aa.time_slot_id
                LEFT JOIN users u ON u.user_id = ats.creator
                LEFT JOIN person_name pn2 ON pn2.person_id = u.person_id
                WHERE aa.`status` = 'COMPLETED' AND aa.patient_id = ".$person_id;

        $reports = $this->db->query($query);

        return $reports->getResultArray();
    }
}