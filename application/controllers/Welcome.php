<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function index()
	{
		$this->login();
	}
	public function login(){
		$this->load->view('login');
	}
	
	public function signup(){
		$this->load->view('signup');
	}
	
	public function members(){
		if($this->session->userdata('is_logged_in')){
			$this->load->view('members');
		}else{
			redirect('Welcome/restricted');
		}
		
	}
	
	public function restricted(){
		$this->load->view('restricted');
	}
	
	public function login_validation(){
		
		$this->load->library('form_validation');
		$this->form_validation->set_rules('email','Email','required|trim|callback_validate_credentials');//name,Actual title,type of validation
		$this->form_validation->set_rules('password','Password','required|md5|trim');
		if($this->form_validation->run()){
			redirect('Welcome/members');
		}else{
			$this->load->view('login');
		}
		
	}
	
	public function signup_validation(){
		$this->load->library('form_validation');
		$this->form_validation->set_rules('email','Email','required|trim|valid_email|is_unique[users.email]');
		$this->form_validation->set_rules('password','Password','required|trim');
		$this->form_validation->set_rules('cpassword','Confirm Password','required|trim|matches[password]');
		
		$this->form_validation->set_message('is_unique',"The Email already Exists");
		
		if($this->form_validation->run()){
			
			echo $email_key = md5(uniqid());
			
			/*$this->load->library('email',array('mailtype'=>'html'));
			
			*/
			$this->load->model('model_users');
			/*$this->email->from('sinharajat.858@gmail.com','Rajat');
			$this->email->to($this->input->post('email'));
			$this->email->subject('We Confirm Your Account');
			
			$message = "<p>Thank You For Signing Up</p>";
			$message .="<p><a herf='".base_url()."Welcome/register_user/$key'>Click Here</a> TO Confirm Your Account</p>"; 
			
			*///$this->email->message($message);
			
			/*if($this->email->send()){
				echo 'Email has been Send';
			}else{
				echo 'Could not send mail';
			}*/
			
			if($this->model_users->add_temp_users($email_key)){
				echo 'Email has been Send';
			}else{
				echo 'Problem In Registering';
			}
			
			

			
			
		}else{
			$this->load->view('signup');
		}
		
	}
	
	
	
	public function validate_credentials(){
		$this->load->model('model_users');
		
		if($this->model_users->can_log_in()){
			
			$data = array(
						'email' => $this->input->post('email'),
						'is_logged_in' => 1
						);
			$this->session->set_userdata($data);
			return true;
		}else{
			$this->form_validation->set_message('validate_credentials','Incorrect Email/Password');
			return false;
		}
	}
	
	public function logout(){
		$this->session->sess_destroy();
		redirect('Welcome/login');
	}
	
	public function register_user($email_key){
	  $this->load->model('model_users');
	  if($this->model_users->is_key_valid($email_key)){
		  
		  if($new_email = $this->model_users->add_user($email_key)){
			  
			  
			  $data = array('email'=>$new_email,
			  'is_logged_in'=>1
			  );
			  $this->session->set_userdata($data);
			  redirect('Welcome/members');
			  
		  }else{
			  echo 'Falied To Add Users';
		  }
		  
	  }else{
		  echo 'Invalid Key';
	  }
	
	}
}
