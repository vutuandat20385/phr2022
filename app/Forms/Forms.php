<?php

namespace App\Forms;

class Forms{

	public function loginForm($formData){
		return view('BeforeLogin/forms/login',$formData);
	}

	public function forgotPasswordForm($formData){
		return view('BeforeLogin/forms/forgotPassword',$formData);
	}

	public function changePasswordForm($formData){
		return view('BeforeLogin/forms/changePassword',$formData);
	}


	public function form_importModal($formData){
		return view('AfterLogin/forms/importModal',$formData);
	}

	public function form_importGroupModal($formData){
		return view('AfterLogin/forms/importGroupModal',$formData);
	}

	public function form_importTestCovidModal($formData){
		return view('AfterLogin/forms/importTestCovidModal',$formData);
	}

	
}
