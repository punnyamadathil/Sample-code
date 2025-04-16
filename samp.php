<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Sign Up Form </title>


    <!-- Font Icon -->
    <link rel="stylesheet" href="<?php echo site_url('public/sign-form/'); ?>fonts/material-icon/css/material-design-iconic-font.min.css">


    <!-- Main css -->
    <link rel="stylesheet" href="<?php echo site_url('public/sign-form/'); ?>css/style.css">
</head>
<body>


    <div class="main">


        <!-- Sign up form -->
        <section class="signup">
            <div class="container">
                <div class="signup-content">
                    <div class="signup-form">
                        <h2 class="form-title">Sign up</h2>
                        <form method="POST" class="register-form" id="register-form" action="<?php echo base_url()?>register" >
                            <div class="form-group">
                                <label for="full_name"><i class="zmdi zmdi-account material-icons-name"></i></label>
                                <input type="text" name="full_name" id="full_name" placeholder="Your Name"/>
                                <?php echo form_error('full_name','<div class="errpor">','</div>');?>
                            </div>
                            <div class="form-group">
                                <label for="email"><i class="zmdi zmdi-email"></i></label>
                                <input type="email" name="email" id="email" placeholder="Your Email"/>
                                <?php echo form_error('email','<div class="errpor">','</div>');?>
                            </div>
                            <div class="form-group">
                                <label for="password"><i class="zmdi zmdi-lock"></i></label>
                                <input type="password" name="password" id="password" placeholder="Password"/>
                                <?php echo form_error('password','<div class="errpor">','</div>');?>
                            </div>
                            <!-- <div class="form-group">
                                <label for="re-pass"><i class="zmdi zmdi-lock-outline"></i></label>
                                <input type="password" name="re_pass" id="re_pass" placeholder="Repeat your password"/>
                            </div> -->
                            <!-- <div class="form-group">
                                <input type="checkbox" name="agree-term" id="agree-term" class="agree-term" />
                                <label for="agree-term" class="label-agree-term"><span><span></span></span>I agree all statements in  <a href="#" class="term-service">Terms of service</a></label>
                            </div> -->
                            <div class="form-group form-button">
                                <input type="submit" name="signup" id="signup" class="form-submit" value="Register"/>
                            </div>
                        </form>
                    </div>
                    <div class="signup-image">
                        <figure><img src="<?php echo site_url('public/'); ?>images/signup-image.jpg" alt="sing up image"></figure>
                        <a href="<?php echo site_url(); ?>sign-in" class="signup-image-link">I am already member</a>
                    </div>
                </div>
            </div>
        </section>


    </div>


    <!-- JS -->
    <!-- <script src="<?php // echo site_url('public/sign-form/'); ?>vendor/jquery/jquery.min.js"></script>
    <script src="<?php // echo site_url('public/sign-form/'); ?>js/main.js"></script> -->
</body><!-- This templates was made by Colorlib (https://colorlib.com) -->
</html>




//=========================================================




<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Users extends CI_Controller {


    public function __construct() {
        parent::__construct();
        $this->load->model('users_model');
        $this->load->library('form_validation');
        $this->load->helper(array('form', 'url'));


    }
    public function index()
    {
        $this->load->view('sign-in');
    }
    public function sign_up()
    {
        $this->load->view('sign-up');
    }
    public function register()
    {
        $this->form_validation->set_rules(
            'full_name', 'Full Name',
            'required|min_length[3]|max_length[15]',
            array(
                'required' => 'You have not provided %s.'
            )
        );


        $this->form_validation->set_rules(
            'password', 'Password', 
            'required',
            array(
                'required' => 'You have not provided %s.'
            )
        );


        /* $this->form_validation->set_rules(
            'cpwd', 'Password Confirmation', 
            'required|matches[pwd]',
            array(
                'required' => 'You have not provided %s.',
                'matches' => 'The password confirmation does not match.'
            )
        ); */


        $this->form_validation->set_rules(
            'email', 'Email', 
            'trim|required|valid_email',
            array(
                'required' => 'You have not provided %s.',
                'valid_email' => 'Please provide a valid %s.'
            )
        );


        if ($this->form_validation->run() == FALSE) {
            $this->load->view('sign-up');
        } 
        else {
            $this->load->library('encrypt');
            $password = $this->input->post('password');
            $password = $this->encrypt->encode($password);
            $data = array(
                'full_name' => $this->input->post('full_name'),
                'email' => $this->input->post('email'),
                'password' => $password
            );


            $register = $this->users_model->add($data,'bm_users');


            if($register) {
                $this->session->set_flashdata('success', 'Registration successful');
                redirect('sign-in');
            } 
        }
    }
    public function login()
    {
        $this->form_validation->set_rules(
            'email', 'Email', 
            'trim|required|valid_email',
            array(
                'required' => 'You have not provided %s.',
                'valid_email' => 'Please provide a valid %s.'
            )
        );
        $this->form_validation->set_rules(
            'password', 'Password', 
            'required',
            array(
                'required' => 'You have not provided %s.'
            )
        );
        if ($this->form_validation->run() == FALSE) {
            $this->load->view('sign-in');
        } else {
            
            $password = $this->input->post('password');
            $username = $this->input->post('email');
               
                $user=$this->users_model->user_login($username,$password);
                if($user)
                {
                    $user_data = array(
                        'user_id'  => $user['id'],
                        'name'     => $user['full_name'],
                        'email'     => $user['email'],
                        'logged_in' => TRUE
                    );
                    $this->session->set_userdata($user_data);


                    redirect('dashboard');
                }
                else{


                    $this->load->view('sign-in');
                }


       


    }
    }
    public function dashboard()
    {
        if($this->session->userdata('user_id') || $this->session->userdata('logged_in')==TRUE){
            $this->load->view('dashboard');
        }else{
            redirect('sign-in');
        }
    }
    public function add_book()
    {  if($this->session->userdata('user_id') || $this->session->userdata('logged_in')==TRUE){
        $data['title']='Add-Book';
        $data['card_title']='Add Book';
        $where['user_id']=$this->session->userdata('user_id'); 
        $data['categories']=$this->users_model->getAllByUser(array(),'bm_category');
        
            $this->load->view('add-book',$data);
        }else{
            redirect('sign-in');
        }
    }
    public function insert_book()
    {
        $user_input_array=$default_array=array();
        $this->form_validation->set_rules(
            'title', 'Title',
            'required|min_length[3]|max_length[15]',
            array(
                'required' => 'You have not provided %s.'
            )
        );
        $this->form_validation->set_rules(
            'author', 'Author',
            'required|min_length[3]|max_length[15]',
            array(
                'required' => 'You have not provided %s.'
            )
        );
        $this->form_validation->set_rules(
            'description', 'Description',
            'trim|required|min_length[3]|max_length[2000]',
            array(
                'required' => 'You have not provided %s.'
            )
        );
        $this->form_validation->set_rules(
            'publish_date', 'Publication Date',
            'required',
            array(
                'required' => 'You have not provided %s.'
            )
        );
        // $this->form_validation->set_rules(
        //     'pdf_file', 'PdfPublication  Date',
        //     'required',
        //     array(
        //         'required' => 'You have not provided %s.'
        //     )
        // );
        if ($this->form_validation->run() == FALSE) {
            $data['title']='Add-Book';
            $data['card_title']='Add Book';
            $this->load->view('add-book',$data);
        } else {
            $params = ['title', 'author',  'description','publish_date','category_id','category_name'];
            foreach ( $params as $param ) {
                $user_input_array[ $param ] = $this->input->post( $param );
            }
            if (!empty($_FILES['pdf_file']['name'])) {
                $config['upload_path']          = './uploads/';
                $config['allowed_types']        = 'pdf';
                $config['min_size']             = 1024;


                $this->load->library('upload', $config);


                if ( ! $this->upload->do_upload('pdf_file'))
                {
                        $error = array('error' => $this->upload->display_errors());
                        
                        $this->load->view('add-book', $error);
                }
                else
                {
                    $data = $this->upload->data();
                    $user_input_array['pdf_file'] = 'uploads/' . $data['file_name'];
                    
                }
            }
            $default_array['user_id']=$this->session->userdata('user_id'); 
            
            //var_dump($input_array);exit;
            $update_id= $this->input->post( 'update_id' );
            if($update_id==0){
                $input_array = array_merge($user_input_array,$default_array);
                $book = $this->users_model->add($input_array,'bm_books');


                if($book) {
                    $this->session->set_flashdata('success', 'New Book added successfully');
                    redirect('add-book');
                } 
            }else{
                
                $manipulated_array['updated_at']=date('Y-m-d H:i:s');
                $input_array = array_merge($user_input_array,$default_array,$manipulated_array);
                $where['id']=$update_id;
                $book = $this->users_model->update_data($where,$input_array,'bm_books');


                if($book) {
                    $this->session->set_flashdata('success', 'The Book Updated successfully');
                    redirect('add-book');
                } 
            }
        }
    }
    public function all_books()
    {  if($this->session->userdata('user_id') || $this->session->userdata('logged_in')==TRUE){
        //$where['user_id']=$this->session->userdata('user_id'); 
        $data['books']=$this->users_model->getAllByUser(array(),'bm_books');
        
            $this->load->view('all-books',$data);
        }else{
            redirect('sign-in');
        }
    }
    public function my_books()
    {  if($this->session->userdata('user_id') || $this->session->userdata('logged_in')==TRUE){
        $where['user_id']=$this->session->userdata('user_id'); 
        $data['books']=$this->users_model->getAllByUser($where,'bm_books');
        $this->load->view('my-books',$data);
        }else{
            redirect('sign-in');
        }
    }
        public function edit_book($id)
    {
        $where['id']=$id; 
        $data['title']='Edit-Book';
        $data['card_title']='Edit Book';
        $data['book']=$this->users_model->getAllById($where,'bm_books');
        $data['categories']=$this->users_model->getAllByUser(array(),'bm_category');
        $this->load->view('add-book',$data);
    }
    public function delete_book($id){
            $manipulated_array['updated_at']=date('Y-m-d H:i:s');
            $manipulated_array['active']=0;
            $manipulated_array['status']=0;
                
                $where['id']=$id;
                $book = $this->users_model->update_data($where,$manipulated_array,'bm_books');
                if($book){
                    $response['message'] = 'Success!! ,Removed  succesfully';
            
                }else{
                    $response['message'] = 'Failed!! ,Something Went wrong';
                }
                echo json_encode($response);


    }
    public function add_category()
    {  if($this->session->userdata('user_id') || $this->session->userdata('logged_in')==TRUE){
        $data['title']='Add-Category';
        $data['card_title']='Add Category';
            $this->load->view('add-category',$data);
        }else{
            redirect('sign-in');
        }
    }
    public function insert_category()
    {
        $user_input_array=$default_array=array();
        $this->form_validation->set_rules(
            'category', 'Category Name',
            'required|min_length[3]|max_length[15]',
            array(
                'required' => 'You have not provided %s.'
            )
        );
        
        
        if ($this->form_validation->run() == FALSE) {
            $data['title']='Add-Category';
            $data['card_title']='Add Category';
            $this->load->view('add-category',$data);
        } else {
            $params = ['category'];
            foreach ( $params as $param ) {
                $user_input_array[ $param ] = $this->input->post( $param );
            }
            
            $default_array['user_id']=$this->session->userdata('user_id');
            $update_id= $this->input->post('update_id');
            if($update_id==0) {
                $input_array = array_merge($user_input_array,$default_array);
                //var_dump($input_array);exit;
                $category = $this->users_model->add($input_array,'bm_category');
                if($category) {
                    $this->session->set_flashdata('success', 'New Category added successfully');
                    redirect('add-category');
                } 
            }else{
                $manipulated_array['updated_at']=date('Y-m-d H:i:s');
                $input_array = array_merge($user_input_array,$default_array,$manipulated_array);
                $where['id']=$update_id;
                $category = $this->users_model->update_data($where,$input_array,'bm_category');


                if($category) {
                    $this->session->set_flashdata('success', 'The category updated successfully');
                    redirect('add-category');
                } 
            }
        }
    }
    public function view_category()
    {  if($this->session->userdata('user_id') || $this->session->userdata('logged_in')==TRUE){
        $data['category']=$this->users_model->getAllByUser(array(),'bm_category');
            $this->load->view('view-category',$data);
        }else{
            redirect('sign-in');
        }
    }
    public function edit_category($id)
    {
        $where['id']=$id; 
        $data['title']='Edit-Category';
        $data['card_title']='Edit Category';
        $data['cate']=$this->users_model->getAllById($where,'bm_category');
        
        $this->load->view('add-category',$data);
    }
    public function delete_category($id){
            $manipulated_array['updated_at']=date('Y-m-d H:i:s');
            $manipulated_array['active']=0;
            $manipulated_array['status']=0;
                
                $where['id']=$id;
                $book = $this->users_model->update_data($where,$manipulated_array,'bm_category');
                if($book){
                    $response['message'] = 'Success!! ,Removed  succesfully';
            
                }else{
                    $response['message'] = 'Failed!! ,Something Went wrong';
                }
                echo json_encode($response);


    }
    public function logout() {
        $this->session->sess_destroy();
        redirect('sign-in');
      }
}


  //===========================================================================


<?php
class Users_model extends CI_Model {
    public function add($data,$table) {
        //echo $table;exit;
        return $this->db->insert($table, $data);
    }
    public function user_login($username,$password){ 
        $query = $this->db->query("select id,password,full_name,email from bm_users where `email`='$username' and status=1");
       
       
        if($query->num_rows() == 1){
            
           $user=$query->row_array();
            
           $this->load->library('encrypt');
           $pass =  $this->encrypt->decode($user['password']);
           if($pass == $password){
            
                return $user; 
            }
           else{
            
                return FALSE;
            }
        }
        else{
            return FALSE;
        }
        
        
    }
    public function getAllByUser($where,$table){


        $this->db->where($where);
        $this->db->where('active',1);
        $this->db->where('status',1);
        $this->db->order_by('id', 'desc');
        $query=$this->db->get($table);
        return $query->result_array();
        
    }
    public function getAllById($where,$table){


        $this->db->where($where);
        $this->db->where('active',1);
        $this->db->where('status',1);
        $this->db->order_by('id', 'desc');
        $query=$this->db->get($table);
        return $query->row_array();
        
    }
    public function update_data($where,$data,$table){
        $this->db->update($table,$data,$where);
        return ($this->db->affected_rows() > 0) ? TRUE : FALSE;
  }
}


//=====================================================================
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title>Forms - Ready Bootstrap Dashboard</title>
	<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
	<link rel="stylesheet" href="<?php echo site_url('public/');?>assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i">
	<link rel="stylesheet" href="<?php echo site_url('public/');?>assets/css/ready.css">
	<link rel="stylesheet" href="<?php echo site_url('public/');?>assets/css/demo.css">
</head>
<body>
	<div class="wrapper">
		<?php  $this->load->view('main-header'); ?>
		<?php  $this->load->view('sidebar'); ?>
			<div class="main-panel">
				<div class="content">
					<div class="container-fluid">
						<h4 class="page-title">All Books</h4>
						<div class="row">
							<div class="
