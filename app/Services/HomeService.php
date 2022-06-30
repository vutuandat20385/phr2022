<?php
namespace App\Services;

use App\Models\UserModel;

class HomeService extends BaseService{
    public function __construct(){
        $this->user = new UserModel();
    }

    public function getNewRegister($num_day){
        $arr_day = array();
        $x = 0;
        for ($i = $num_day; $i >= 0; $i--) {
            $day        = date('d-m', strtotime('-' . $i . ' days'));
            $dayBegin   = date('Y-m-d 00:00:01', strtotime('-' . $i . ' days'));
            $dayEnd     = date('Y-m-d 23:59:59', strtotime('-' . $i . ' days'));
            $arr_day[$x]['day'] = array(
                'day'       => $day,
                'dayBegin'  => $dayBegin,
                'dayEnd'    => $dayEnd
            );
            $x++;
        }
        $y = 0;
        foreach ($arr_day as $k => $day) {
            $b = $day['day']['dayBegin'];
            $e = $day['day']['dayEnd'];
            $arr_day[$y]['countUsers'] = $this->user->where(['date_created >=' => $b, 'date_created <=' => $e])->countAllResults();
            $y++;
        }
        // dd($arr_day);
        return $arr_day;
    }
}