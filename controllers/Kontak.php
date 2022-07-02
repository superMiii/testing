<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Kontak extends CI_Controller
{

    public function index()
    {
        $data['judul'] = 'Kontak | Toko Baju Sri';
        $this->load->view('ui/template/header', $data);
        $this->load->view('ui/kontak', $data);
        $this->load->view('ui/template/footer', $data);
    }
}

/* End of file Kontak.php */
