<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (is_file(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Login');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
//$routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.


// Login
$routes->get('/dang-nhap', 'Login::index');
$routes->post('/xu-ly-dang-nhap', 'Login::loginProcess');

$routes->get('/dang-xuat', 'Login::logOut');

// After Login
$routes->get('/', 'Home::index', ['filter' => 'checklogin']);
$routes->group('trang-quan-tri', ['filter' => 'checklogin'], function($routes){
    $routes->get('/', 'Home::index');
    $routes->get('thong-ke', 'Home::analytic');

    // Quản lý tài khoản
    $routes->group('tai-khoan', function($routes){
        $routes->get('khach-hang', 'Accounts::patientList');
        $routes->get('bac-si', 'Accounts::providerList');
        $routes->get('quan-tri', 'Accounts::managerList');

        $routes->post('sua-thong-tin', 'Accounts::updateAccountInfo');
        $routes->get('tim-kiem-khach-hang', 'Accounts::searchAccount');
    });

    // Lịch sử
    $routes->group('lich-su', function($routes){
        $routes->get('import', 'History::import');
    });

    // HIS-EHC
    $routes->group('ehc', function($routes){
        $routes->get('khach-le', 'Ehc::ehcHistoryKhachLe');
    });

    // Marketing
    $routes->group('marketing', function($routes){
        $routes->get('thong-bao', 'Marketing::marketingNotification');
        
        $routes->get('sinh-nhat', 'Marketing::birthdaySetting');
        $routes->post('luu-thong-bao-sinh-nhat', 'Marketing::saveBirthdayNotifySetting');
        $routes->post('luu-sms-sinh-nhat', 'Marketing::saveBirthdaySMSSetting');
        
        $routes->get('tai-khoan-hoat-dong', 'Marketing::activeUsers');
        $routes->post('cap-nhat-ghi-chu', 'Marketing::updateNoteInfo');
    });

    // Bệnh án
    $routes->group('benh-an', function($routes){
        $routes->group('d4u-khach-le', function($routes){
            $routes->get('/', 'PHR::d4uSingle');
            $routes->get('chi-tiet-benh-an/(:num)', 'PHR::detailSingle/$1');
            $routes->post('import', 'ImportController::d4uListSingleImport');
        });

        $routes->group('d4u-khach-doan', function($routes){
            $routes->get('/', 'PHR::d4uGroup');
            $routes->get('chi-tiet-benh-an/(:num)', 'PHR::detailGroup/$1');
            $routes->post('import', 'ImportController::d4uListGroupImport');
        });

        $routes->get('d4u-test-covid', 'PHR::d4uCovid');
    });

    // Thực thi
    $routes->group('thuc-thi', function($routes){
        $routes->get('/', 'Enforcement::index');
        $routes->get('gui-sms', 'Enforcement::sendSMSView');
        $routes->get('gui-notify', 'Enforcement::sendNotifyView');
        $routes->get('truy-van', 'Enforcement::commandView');

        $routes->post('send-sms', 'Enforcement::sendSMSProcess');
        $routes->post('send-notify', 'Enforcement::sendNotifyProcess');
        $routes->post('send-sql', 'Enforcement::sendSQLProcess');
    });

    // Quản lý giao dịch
    $routes->group('giao-dich', function($routes){
        $routes->get('/', 'Transactions::index');
        $routes->get('vnpay', 'Transactions::vnpayTransactions');
        $routes->get('momo', 'Transactions::momoTransactions');

        $routes->get('chuyen-khoan', 'Transactions::autoTransferHistory');
        $routes->post('/', 'Transactions::index');
        $routes->post('add', 'Transactions::addTransaction');
    });

    // Import
    $routes->group('import', function($routes){
        $routes->group('danh-sach-benh-an', function($routes){
            $routes->post('khach-le', 'ImportController::d4uListSingleImport');
            $routes->post('khach-doan', 'ImportController::d4uListGroupImport');
            $routes->post('test-covid', 'ImportController::d4uListCovidImport');
        });
    });

    // Lịch sử
    $routes->group('lich-su', function($routes){
        $routes->group('import', function($routes){
            $routes->get('benh-an/(:any)', 'History::historyPHRimport/$1');
            $routes->get('tim-benh-an/(:any)/(:any)', 'History::history_import_result/$1/$2');
            $routes->get('chi-tiet/(:any)', 'History::detail_import/$1');
        });
    });

    // Cấu hình
    $routes->group('cau-hinh', function($routes){
        $routes->get('/', 'Settings::index');
        $routes->get('thong-bao', 'Settings::notification');
        $routes->group('dich-vu', function($routes){
            $routes->get('/', 'Settings::service');
            $routes->post('them-moi', 'Settings::addNewService');
            $routes->post('chinh-sua/(:num)', 'Settings::editService/$1');
            $routes->post('xoa/(:num)', 'Settings::deleteService/$1');
            $routes->post('khoi-phuc/(:num)', 'Settings::restoreService/$1');
        });
        
        $routes->get('chi-so-chuan', 'Settings::indexValue');
        $routes->get('quan-ly-bac-si', 'Settings::providerManage');
        $routes->get('template', 'Settings::template');

        $routes->post('cap-nhat', 'Settings::saveSettings');
        $routes->post('nhac-nho-thuoc', 'Settings::saveMedicineRemindSetting');
	    $routes->post('nhac-ket-thuc-kham', 'Settings::saveAppCompleteRemindSetting');
    });

    // Tư vấn qua app
    $routes->group('tu-van-qua-app', function($routes){
        $routes->get('danh-sach-tu-van', 'AppointmentController::getAppointmentList');
        $routes->get('chi-tiet-benh-nhan/(:any)', 'AppointmentController::appointmentDetail/$1');

        $routes->get('lich-tai-kham', 'AppointmentController::appointmentFollowList');
        $routes->get('lich-tai-kham-qua-hen', 'AppointmentController::appointmentFollowListOver');
        $routes->post('cap-nhat-lich-tai-kham', 'AppointmentController::updateAppointmentFollowStatus');
        $routes->get('ket-qua-theo-doi', 'AppointmentController::notificationAppointmentFollow');

        $routes->get('so-du-tai-khoan', 'AppointmentController::accountBalance');
        $routes->get('account-balance-sort', 'AppointmentController::accountBalanceSort');

        $routes->get('quan-ly-tu-van', 'AppointmentController::appointmentFollowManage');
        $routes->post('cap-nhat-quan-ly-tu-van', 'AppointmentController::editAppointmentFollowManage');

        $routes->get('bao-cao-kham-tu-van', 'AppointmentController::getAppointmentReport');

    });
    
});

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
