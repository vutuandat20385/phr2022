<?php
namespace App\Services;

use App\Models\UserModel;
use App\Models\User2Model;
use App\Models\Users2RolesModel;
use App\Models\HistoryImportModel;

class AccountService extends BaseService{
    public function __construct(){
        $this->user = new UserModel();
        $this->user2 = new User2Model();
        $this->user2role = new Users2RolesModel();
        $this->history = new HistoryImportModel();

        $this->db = \Config\Database::connect();
    }

    public function countUsers($type){
        
        switch ($type) {
            case 'moi-dang-ky':
                $todayBegin = date('Y-m-d 00:00:00');
                $todayEnd = date('Y-m-d 23:59:59');
                $query = "select pa.value, pn.given_name, p.birthdate, p.gender, u.date_created from users as u
                            left join person as p ON p.person_id = u.person_id
                            left join person_name as pn ON p.person_id = pn.person_id
                            left join person_attribute as pa ON p.person_id = pa.person_id
                            where u.date_created >= '" . $todayBegin . "' and u.date_created <= '" . $todayEnd . "' and u.retired=0";
                $todayRegister = $this->db->query($query);
                $result = $todayRegister->getResultArray();
                break;
            case 'hoat-dong':
                $lastActive = $this->active->orderBy('date_created', 'DESC')->first();
                $lastDate_begin = date('Y-m-d 00:00:00', strtotime($lastActive['date_created']));
                $lastDate_end = date('Y-m-d 23:59:59', strtotime($lastActive['date_created']));
                $query = "select pa.value, pn.given_name, ual.date_created, ual.phone_number from user_active_log as ual
                            left join users as u ON u.username = ual.user_name
                            left join person as p ON p.person_id = u.person_id
                            left join person_name as pn ON p.person_id = pn.person_id
                            left join person_attribute as pa ON p.person_id = pa.person_id
                            where ual.date_created >= '" . $lastDate_begin . "' and ual.date_created <= '" . $lastDate_end . "' and u.retired=0
                            group by ual.phone_number";
                $activeList = $this->db->query($query);
                $result['list'] = $activeList->getResultArray();
                $result['date'] = date('d-m-Y', strtotime($lastActive['date_created']));
                break;
            case 'sinh-nhat':
                $date = date('Y-m-d');
                $day = date('d', strtotime($date));
                $month = date('m', strtotime($date));

                $query_person = "SELECT p.person_id, pa.value, p.birthdate, pn.given_name FROM person p
                                    LEFT JOIN person_attribute pa ON pa.person_id = p.person_id 
                                    LEFT JOIN person_name pn ON pn.person_id = p.person_id
                                    WHERE MONTH(p.birthdate)='" . $month . "' AND DAY(p.birthdate)='" . $day . "'";
                $person = $this->db->query($query_person);
                $result = $person->getResultArray();
                break;
            default:
                // 
                // $result = $this->user->where(['status' => 'ACTIVE'])->countAll();
                $query = "SELECT count(person.person_id) as count
                FROM person
                INNER JOIN users ON users.person_id = person.person_id
                LEFT JOIN person_attribute ON person_attribute.person_id = person.person_id
                WHERE users.`status` = 'ACTIVE' AND person_attribute.`value` is not null and users.retired=0
                ORDER BY users.person_id DESC";
                $list = $this->db->query($query)->getResultArray();
                $result = $list[0]['count'];
                break;
        }

        return $result;
    }

    public function getPatientList($perPage, $start){
        $query = "SELECT users.username, person_name.given_name, person.gender, person.birthdate, users.date_created,
                    users.email, person_address.address1, person_address.city_village, person_attribute.value
                FROM person
                INNER JOIN person_name ON person_name.person_id = person.person_id
                INNER JOIN users ON users.person_id = person.person_id
                LEFT JOIN  person_address ON person_address.person_id = person.person_id
                LEFT JOIN person_attribute ON person_attribute.person_id = person.person_id
                WHERE users.`status` = 'ACTIVE' AND person_attribute.`value` is not null and users.retired=0
                AND person.person_id not in (SELECT p.person_id FROM provider p WHERE p.person_id > 1 GROUP BY p.person_id)
                ORDER BY users.person_id DESC LIMIT $start, $perPage";

        $list = $this->db->query($query);
        $result = $list->getResultArray();

        foreach ($result as $k => $u) {
            if ($u['gender'] == 'Nam' || $u['gender'] == 'M' || $u['gender'] == 'MALE') {
                $selected_nam = 'checked';
                $selected_nu = '';
            } else if ($u['gender'] == 'Nữ' || $u['gender'] == 'F' || $u['gender'] == 'FEMALE') {
                $selected_nu = 'checked';
                $selected_nam = '';
            } else {
                $selected_nam = '';
                $selected_nu = '';
            }

            $query_referralCode = "SELECT referral_code FROM referral_campaign";
            $listCodeArr = $this->db->query($query_referralCode)->getResultArray();
            // Get Referred Code
            $sql_referralCode = "SELECT * FROM referral_history WHERE phone_number='" . $u['value'] . "'";
            // log_message('error', $sql_referralCode);
            $rCodeArr = $this->db->query($sql_referralCode)->getResultArray();

            $result[$k]['code'] = $rCodeArr[0]['referral_code'];
            $result[$k]['modal'] = '<div class="modal-body">
                                                <div class="row m-0">
                                                    <div class="col-md-4"> <h6>Họ tên:</h6> </div>
                                                    <div class="col-md-8"> <input class="form-control miniTextBox" type="text" value="' . $u['given_name'] . '" name="uGivenName' . $u['value'] . '" id="uGivenName' . $u['value'] . '"> </div>

                                                    <div class="col-md-4"> <h6>Số điện thoại:</h6> </div>
                                                    <div class="col-md-8"> ' . $u['value'] . ' 
                                                        <input class="form-control miniTextBox" type="text" value="' . $u['value'] . '" name="uPhoneNumber' . $u['value'] . '" id="uPhoneNumber' . $u['value'] . '" hidden>
                                                    </div>

                                                    <div class="col-md-4"> <h6>Giới tính:</h6> </div>
                                                    <div class="col-md-4"> 
                                                        <div class="form-check">
                                                            <label class="form-check-label">
                                                            <input class="form-check-input" type="radio" name="uGender' . $u['value'] . '" value="Nam" ' . $selected_nam . '>
                                                            Nam
                                                            <i class="input-helper"></i></label>
                                                        </div>
                                                        
                                                    </div>
                                                    <div class="col-md-4"> 
                                                        <div class="form-check">
                                                            <label class="form-check-label">
                                                            <input class="form-check-input" type="radio" name="uGender' . $u['value'] . '" value="Nữ" ' . $selected_nu . '>
                                                            Nữ
                                                            <i class="input-helper"></i></label>
                                                        </div>
                                                        
                                                    </div>

                                                    <div class="col-md-4"> <h6>Email:</h6> </div>
                                                    <div class="col-md-8"> <input class="form-control miniTextBox" type="text" value="' . $u['email'] . '" name="uEmail' . $u['value'] . '" id="uEmail' . $u['value'] . '"> </div>

                                                    <div class="col-md-4"> <h6>Tỉnh/Thành phố:</h6> </div>
                                                    <div class="col-md-8 "> <input class="form-control miniTextBox" type="text" value="' . $u['city_village'] . '" name="uCity' . $u['value'] . '" id="uCity' . $u['value'] . '"> </div>
                                                    
                                                    <div class="col-md-4"> <h6>Ngày tạo:</h6> </div>
                                                    <div class="col-md-8"> ' . date('d-m-Y H:i:s', strtotime($u['date_created'])) . ' </div>

                                                    <div class="col-md-4"> <h6>Mã giới thiệu:</h6> </div>
                                                    <div class="col-md-8">
                                                        <select id="magioithieu' . $u['value'] . '" class="form-control" style="height: 36px;">
                                                            <option class="p-2" value="">Chọn Mã giới thiệu</option>';
            foreach ($listCodeArr as $code) {
                if ($code['referral_code'] == $rCodeArr[0]['referral_code']) {
                    $selected = 'selected';
                } else {
                    $selected = '';
                }
                $result[$k]['modal'] .=                 '<option value="' . $code['referral_code'] . '" ' . $selected . '>' . $code['referral_code'] . '</option>';
            }


            $result[$k]['modal'] .=             '</select>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                                                <button type="button" class="btn btn-primary" id="btnSave_' . $u['value'] . '" onClick="saveUserInfo(\'' . $u['value'] . '\')">Lưu</button>
                                            </div>
                                            <script>
                                                $(document).ready(function(){
                                                    $("#magioithieu' . $u['value'] . '").select2();
                                                });
                                            </script>';
        }

        return $result;
    }

    public function city_village_list(){
        $query = "SELECT person_address.city_village
                FROM person
                INNER JOIN person_name ON person_name.person_id = person.person_id
                INNER JOIN users ON users.person_id = person.person_id
                LEFT JOIN  person_address ON person_address.person_id = person.person_id
                LEFT JOIN person_attribute ON person_attribute.person_id = person.person_id
                WHERE users.`status` = 'ACTIVE' AND person_attribute.`value` is not null AND person_address.city_village is not null
                GROUP BY person_address.city_village
                ORDER BY person_address.city_village ASC";
        $list = $this->db->query($query);
        $result = $list->getResultArray();

        return $result;
    }

    public function updateAccount($data){
        $acc = $this->db->query("SELECT person_id FROM users WHERE username='" . $data['phoneNumber'] . "'")->getResultArray();
        if ($acc) {
            return $this->db->query("UPDATE person SET birthdate='" . $data['birthdate'] . "' WHERE person_id=" . $acc[0]['person_id']);
        } else {
            return false;
        }
    }

    public function addHistoryImport($data){
        return $this->history->save($data);
    }

    public function getUserList_searchResult($perPage, $start, $info, $city, $rCode){
        $query = "SELECT users.username, person_name.given_name, person.gender, person.birthdate, users.date_created,
                users.email, person_address.address1, person_address.city_village, person_attribute.value, referral_history.referral_code as `code`
            FROM person
            INNER JOIN person_name ON person_name.person_id = person.person_id
            INNER JOIN users ON users.person_id = person.person_id
            LEFT JOIN  person_address ON person_address.person_id = person.person_id
            LEFT JOIN person_attribute ON person_attribute.person_id = person.person_id
            LEFT JOIN referral_history ON referral_history.phone_number = person_attribute.value
            WHERE users.`status` = 'ACTIVE' AND person_attribute.`value` is not null and users.retired=0";
        if ($city != '') {
            $query .= " AND person_address.city_village ='" . $city . "'";
        }

        if ($info != '') {
            $query .= " AND (person_name.given_name like '%" . $info . "%' OR person_attribute.value = '" . $info . "')";
        }

        if ($rCode != '') {
            $query .= " AND (referral_history.referral_code = '$rCode')";
        }
        $query .= " ORDER BY users.person_id DESC LIMIT $start, $perPage";

        $list = $this->db->query($query);
        $result = $list->getResultArray();

        foreach ($result as $k => $u) {
            if ($u['gender'] == 'Nam' || $u['gender'] == 'M' || $u['gender'] == 'MALE') {
                $selected_nam = 'checked';
                $selected_nu = '';
            } else if ($u['gender'] == 'Nữ' || $u['gender'] == 'F' || $u['gender'] == 'FEMALE') {
                $selected_nu = 'checked';
                $selected_nam = '';
            } else {
                $selected_nam = '';
                $selected_nu = '';
            }



            $query_referralCode = "SELECT referral_code FROM referral_campaign";
            $listCodeArr = $this->db->query($query_referralCode)->getResultArray();
            // Get Referred Code
            $sql_referralCode = "SELECT * FROM referral_history WHERE phone_number='" . $u['value'] . "'";
            $rCodeArr = $this->db->query($sql_referralCode)->getResultArray();

            $result[$k]['code'] = $rCodeArr[0]['referral_code'];
            $result[$k]['modal'] = '<div class="modal-body">
                                            <div class="row m-0">
                                                <div class="col-md-6 form-control"> <h6>Họ tên:</h6> </div>
                                                <div class="col-md-6 "> <input class="form-control" type="text" value="' . $u['given_name'] . '" name="uGivenName' . $u['value'] . '" id="uGivenName' . $u['value'] . '"> </div>

                                                <div class="col-md-6 form-control"> <h6>Số điện thoại:</h6> </div>
                                                <div class="col-md-6 form-control pl-3"> ' . $u['value'] . ' 
                                                    <input class="form-control" type="text" value="' . $u['value'] . '" name="uPhoneNumber' . $u['value'] . '" id="uPhoneNumber' . $u['value'] . '" hidden>
                                                </div>

                                                <div class="col-md-6 form-control"> <h6>Giới tính:</h6> </div>
                                                <div class="col-md-6 form-control pl-3"> 
                                                    <div class="form-check form-check-inline">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="radio" name="uGender' . $u['value'] . '" value="Nam" ' . $selected_nam . '> Nam
                                                            <span class="circle">
                                                            <span class="check"></span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="radio" name="uGender' . $u['value'] . '" value="Nữ" ' . $selected_nu . '> Nữ
                                                            <span class="circle">
                                                            <span class="check"></span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="col-md-6 form-control"> <h6>Email:</h6> </div>
                                                <div class="col-md-6"> <input class="form-control" type="text" value="' . $u['email'] . '" name="uEmail' . $u['value'] . '" id="uEmail' . $u['value'] . '"> </div>

                                                <div class="col-md-6 form-control"> <h6>Tỉnh/Thành phố:</h6> </div>
                                                <div class="col-md-6 "> <input class="form-control" type="text" value="' . $u['city_village'] . '" name="uCity' . $u['value'] . '" id="uCity' . $u['value'] . '"> </div>
                                                
                                                <div class="col-md-6 form-control"> <h6>Ngày tạo:</h6> </div>
                                                <div class="col-md-6 form-control pl-3"> ' . date('d-m-Y H:i:s', strtotime($u['date_created'])) . ' </div>

                                                <div class="col-md-6 form-control"> <h6>Mã giới thiệu:</h6> </div>
                                                <div class="col-md-6 pr-0">
                                                    <select id="magioithieu' . $u['value'] . '" class="form-control" style="height: 36px;">
                                                        <option class="p-2" value="">Chọn Mã giới thiệu</option>';
            foreach ($listCodeArr as $code) {
                if ($code['referral_code'] == $rCodeArr[0]['referral_code']) {
                    $selected = 'selected';
                } else {
                    $selected = '';
                }
                $result[$k]['modal'] .=                 '<option value="' . $code['referral_code'] . '" ' . $selected . '>' . $code['referral_code'] . '</option>';
            }


            $result[$k]['modal'] .=             '</select>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                                            <button type="button" class="btn btn-primary" id="btnSave_' . $u['value'] . '" onClick="saveUserInfo(\'' . $u['value'] . '\')">Lưu</button>
                                        </div>
                                        <script>
                                            $(document).ready(function(){
                                                $("#magioithieu' . $u['value'] . '").select2();
                                            });
                                        </script>';
        }

        return $result;
    }

    public function countUsers_searchResult($info, $city, $rCode){
        $query = "SELECT users.username, person_name.given_name, person.gender, person.birthdate, users.date_created,
                users.email, person_address.address1, person_address.city_village, person_attribute.value, referral_history.referral_code as `code`
            FROM person
            INNER JOIN person_name ON person_name.person_id = person.person_id
            INNER JOIN users ON users.person_id = person.person_id
            LEFT JOIN  person_address ON person_address.person_id = person.person_id
            LEFT JOIN person_attribute ON person_attribute.person_id = person.person_id
            LEFT JOIN referral_history ON referral_history.phone_number = person_attribute.value
            WHERE users.`status` = 'ACTIVE' AND person_attribute.`value` is not null  and users.retired=0";
        if ($city != '') {
            $query .= " AND person_address.city_village = '" . $city . "'";
        }

        if ($info != '') {
            $query .= " AND (person_name.given_name like '%" . $info . "%' OR person_attribute.value = '" . $info . "')";
        }

        if ($rCode != '') {
            $query .= " AND (referral_history.referral_code = '$rCode')";
        }

        $query .= " ORDER BY users.person_id DESC";
        // dd($query);
        $list = $this->db->query($query);
        $result = $list->getResultArray();

        return $result;
    }

    public function countProvider($type){
        switch ($type) {

            default:
                // 
                // $result = $this->user->where(['status' => 'ACTIVE'])->countAll();
                $query = "SELECT count(p.person_id) as `count` FROM provider p 
                            LEFT JOIN person_name pn ON pn.person_id = p.person_id
                            LEFT JOIN person_attribute pa ON pa.person_id = p.person_id
                            WHERE p.person_id > 1";
                $list = $this->db->query($query)->getResultArray();

                $result = $list[0]['count'];
                break;
        }

        return $result;
    }

    public function getProviderList($perPage, $start){

        $query = "SELECT p.person_id, pn.given_name, pa.`value`, ps.gender, ps.birthdate FROM provider p 
                    LEFT JOIN person ps ON ps.person_id = p.person_id
                    LEFT JOIN person_name pn ON pn.person_id = p.person_id
                    LEFT JOIN person_attribute pa ON pa.person_id = p.person_id
                    WHERE p.person_id > 1
                    ORDER BY p.person_id DESC LIMIT $start, $perPage";
        $list = $this->db->query($query);
        $result = $list->getResultArray();

        foreach ($result as $k => $u) {
            if ($u['gender'] == 'Nam' || $u['gender'] == 'M' || $u['gender'] == 'MALE') {
                $selected_nam = 'checked';
                $selected_nu = '';
            } else if ($u['gender'] == 'Nữ' || $u['gender'] == 'F' || $u['gender'] == 'FEMALE') {
                $selected_nu = 'checked';
                $selected_nam = '';
            } else {
                $selected_nam = '';
                $selected_nu = '';
            }



            $result[$k]['modal'] = '<div class="modal-body">
                                                    <div class="row m-0">
                                                        <div class="col-md-6 form-control"> <h6>Họ tên:</h6> </div>
                                                        <div class="col-md-6 "> <input class="form-control" type="text" value="' . $u['given_name'] . '" name="uGivenName' . $u['value'] . '" id="uGivenName' . $u['value'] . '"> </div>

                                                        <div class="col-md-6 form-control"> <h6>Số điện thoại:</h6> </div>
                                                        <div class="col-md-6 form-control pl-3"> ' . $u['value'] . ' 
                                                            <input class="form-control" type="text" value="' . $u['value'] . '" name="uPhoneNumber' . $u['value'] . '" id="uPhoneNumber' . $u['value'] . '" hidden>
                                                        </div>

                                                        <div class="col-md-6 form-control"> <h6>Giới tính:</h6> </div>
                                                        <div class="col-md-6 form-control pl-3"> 
                                                            <div class="form-check form-check-inline">
                                                                <label class="form-check-label">
                                                                    <input class="form-check-input" type="radio" name="uGender' . $u['value'] . '" value="Nam" ' . $selected_nam . '> Nam
                                                                    <span class="circle">
                                                                    <span class="check"></span>
                                                                    </span>
                                                                </label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <label class="form-check-label">
                                                                    <input class="form-check-input" type="radio" name="uGender' . $u['value'] . '" value="Nữ" ' . $selected_nu . '> Nữ
                                                                    <span class="circle">
                                                                    <span class="check"></span>
                                                                    </span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 form-control"> <h6>Ngày sinh:</h6> </div>
                                                        <div class="col-md-6 pl-3">
                                                            <input type="text" class="form-control u_datepicker" value="' . $u['birthdate'] . '" name="uBirthdate' . $u['value'] . '" id="uBirthdate' . $u['value'] . '">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                                                    <button type="button" class="btn btn-primary" id="btnSave_' . $u['value'] . '" onClick="saveUserInfo(\'' . $u['value'] . '\')">Lưu</button>
                                                </div>';
        }

        return $result;
    }

    public function getManagerList(){
        return $this->user2->join('users2_roles', 'users2_roles.role_id = users2.role','LEFT')->where(['status' => 1])->findAll();
    }

    public function deleteAccount($id){
        return $this->user2->set(['status' => 0])->where(['id' => $id])->update();
    }

    public function updateAccount2($id, $data){
        return $this->user2->set($data)->where(['id' => $id])->update();
    }

    public function getRoleList(){
        return $this->user2role->findAll();
    }


}