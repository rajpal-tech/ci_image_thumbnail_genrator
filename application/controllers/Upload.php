<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Upload extends CI_Controller{

  function __construct(){
    parent::__construct();
    $this->load->library('upload');
    //load model Upload_model.php
    $this->load->model('Upload_model','upload_model');
  }

  function index(){
    $this->load->view('upload_view');
  }

  function do_upload(){
      $config['upload_path'] = './assets/images/'; //path folder
	    $config['allowed_types'] = 'gif|jpg|png|jpeg|bmp';
	    $config['encrypt_name'] = TRUE;

	    $this->upload->initialize($config);

      if(!empty($_FILES['filefoto']['name'])){
		        if ($this->upload->do_upload('filefoto')){
		            $img = $this->upload->data();
	              //Compress Image
                $this->_create_thumbs($img['file_name']);
                $this->_create_thumbs_watemark($img['file_name']);

                $title = $this->input->post('title',TRUE);
                $image_large = $img['file_name'];
                $image_medium = $img['file_name'];
                $image_small = $img['file_name'];

                $this->upload_model->insert_images($title,$image_large,$image_medium,$image_small);
                $this->session->set_flashdata('msg','<div class="alert alert-info">Image Upload Successful.</div>');
                redirect('upload/show_images');
				    }else{
		            echo $this->upload->display_errors();
		    	  }

		    }else{
				    echo "image is empty or type of image not allowed";
			}
  }

  function _create_thumbs($file_name){
        // Image resizing config
        $config = array(
            // Large Image
            array(
                'image_library' => 'GD2',
                'source_image'  => './assets/images/'.$file_name,
                'maintain_ratio'=> TRUE,
                'width'         => 700,
                'height'        => 467,
                'new_image'     => './assets/images/large/'.$file_name
                ),
            // Medium Image
            array(
                'image_library' => 'GD2',
                'source_image'  => './assets/images/'.$file_name,
                'maintain_ratio'=> TRUE,
                'width'         => 600,
                'height'        => 400,
                'new_image'     => './assets/images/medium/'.$file_name
                ),
            // Small Image
            array(
                'image_library' => 'GD2',
                'source_image'  => './assets/images/'.$file_name,
                'maintain_ratio'=> TRUE,
                'width'         => 100,
                'height'        => 67,
                'new_image'     => './assets/images/small/'.$file_name
            ));

        $this->load->library('image_lib', $config[0]);
        foreach ($config as $item){
            $this->image_lib->initialize($item);
            $this->image_lib->rotate();
            if(!$this->image_lib->resize()){
                return false;
            }
            $this->image_lib->clear();
        }
    }
  
    //     $config['source_image'] = './assets/images/large/'.$file_name;
    //     $config['wm_text'] = 'Copyright 2006 - John Doe RAJPAL';
    //     $config['wm_type'] = 'text';
    //     $config['wm_font_path'] = './system/fonts/texb.ttf';
    //     $config['wm_font_size'] = '16';
    //     $config['wm_font_color'] = 'ffffff';
    //     $config['wm_vrt_alignment'] = 'bottom';
    //     $config['wm_hor_alignment'] = 'center';
    //     $config['wm_padding'] = '20';
    //     $this->image_lib->initialize($config);
    //     $this->image_lib->watermark();        
    
        function _create_thumbs_watemark($file_name){    
            // Image watermark config
            $config = array(
                // Large Image
                array(                    
                    'source_image'  => './assets/images/large/'.$file_name,
                    'wm_text'       => 'TECHUGO',
                    'wm_type'       => 'text',
                    'wm_font_size'  => 30,
                 'wm_vrt_alignment' => 'middle',
                 'wm_hor_alignment' => 'center',
                    'wm_padding'    => '10',
                    'wm_font_color' => 'ffffff',
                    ),
                // Medium Image
                array(                    
                    'source_image'  => './assets/images/medium/'.$file_name,
                    'wm_text'       => 'TECHUGO',
                    'wm_type'       => 'text',
                    'wm_font_size'  => 10,
                 'wm_vrt_alignment' => 'middle',
                 'wm_hor_alignment' => 'center',
                    'wm_padding'    => '10',
                    'wm_font_color' => 'ffffff',
                    ),
                // Small Image
                array(                    
                    'source_image'  => './assets/images/small/'.$file_name,
                    'wm_text'       => 'TECHUGO',
                    'wm_type'       => 'text',
                    'wm_font_size'  =>  3,
                 'wm_vrt_alignment' => 'left',
                 'wm_hor_alignment' => 'center',
                    'wm_padding'    => '20',
                    'wm_font_color' => 'ffffff',
                    ),
                );

            $this->load->library('image_lib', $config[0]);

            foreach ($config as $item){
                $this->image_lib->initialize($item);                
                if(!$this->image_lib->watermark()){
                    return false;
                }
                $this->image_lib->clear();
            }
        }

        public function add_video() {                   // move_upload_file code

            if(!empty($_FILES['video']['name'])){
        
                $tmp_name_array = $_FILES['video']['tmp_name'];
                $n_array = $_FILES['video']['name'];
                $exp = explode('.', $n_array);
                $newnme = date('his').rand().'.'.end($exp);
                $raw_name = explode('.', $newnme);
        
                $full_path = base_url('uploads').'/'.$newnme;
                $new_path = base_url('uploads').'/';
                if(move_uploaded_file($tmp_name_array, "uploads/".$newnme))
                {                
                    $full_path = base_url('uploads').'/'.$newnme;
                    $new_path = base_url('uploads').'/';
        
                    print_r(exec("ffmpeg -i ".$full_path." ".$new_path.$raw_name[0].".jpg"));
        
                    echo "uploaded Successfully";
                }
            }else{
        
                echo "not selected any file";
            }
        }
        

    //function to show images to view
    function show_images(){
      $data['images']=$this->upload_model->show_images();
      $this->load->view('images_view', $data);
    }

}
