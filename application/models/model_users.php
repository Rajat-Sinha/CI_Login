<?php

class Model_users extends CI_Model{
	public function can_log_in(){
		
		$this->db->where('email',$this->input->post('email'));
		$this->db->where('password',md5($this->input->post('password')));
		
		$query = $this->db->get('users');
		
		if($query->num_rows() == 1){
			return true;
		}else{
			return false;
		}
		
	}
	public function  add_temp_users($email_key){
	
		$data = array(
					'email' => $this->input->post('email'),
					'password' => md5($this->input->post('password')),
					'email_key'=>$email_key
					
					);
		$query = $this->db->insert('tmp_users',$data);
		if($query){
			return true;
		}else{
			return false;
		}
	}
	
	public function is_key_valid($email_key){
		$this->db->where('email_key',$email_key);
		$query = $this->db->get('tmp_users');
		if($query->num_rows() == 1){
			return true;
		}else{
			return false;
		}
	}
	public function add_user($email_key){
		$this->db->where('email_key', $email_key);
		
		$tmp_users = $this->db->get('tmp_users');
		if($tmp_users){
			$row = $tmp_users->row();
			
			$data = array(
					'email'=>$row->email,
					'password'=>$row->password
					);
			
			$did_add_user = $this->db->insert('users',$data);
			if($did_add_user){
				$this->db->where('email_key',$email_key);
				$this->db->delete('tmp_users');
				return $data['email'];
								
			}else{
				return false;
			}
			
		}
	}
	
}

