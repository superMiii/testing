<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Produk extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('ProdukModel');
        $this->load->model('KategoriModel');
    }
    public function index()
    {
        $data['key_kategori'] = $this->input->get_post('kategori');
        $data['keyword'] = $this->input->get_post('keyword');
        $data['judul'] = 'Produk | Toko Baju Sri';

        //load library pagination
        $this->load->library('pagination');
        //configure pagination
        $config['base_url'] = base_url('produk/index/');

        $config['total_rows'] = $this->db->get('tb_produk')->num_rows();
        $config['per_page'] = 8;

        //css pagination
        $config['full_tag_open'] = '<ul class="pagination pagination-lg">';
        $config['full_tag_close'] = '</ul>';

        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';

        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';

        $config['next_tag_open'] = '<li><i class="fa fa-long-arrow-right"></i>';
        $config['next_tag_close'] = '</li>';

        $config['prev_tag_open'] = '<li><i class="fa fa-long-arrow-left"></i>';
        $config['prev_tag_close'] = '</li>';

        $config['cur_tag_open'] = '<li class="active"><a href="#" class="page-link">';
        $config['cur_tag_close'] = '</a></li>';

        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';

        $config['attributes'] = array('class' => 'page-link');

        //initialize pagination
        $this->pagination->initialize($config);

        // example: /produk/index/[segment3]
        $data['start'] = $this->uri->segment(3);
        $data['produk'] = $this->ProdukModel->getProduk($config['per_page'], $data['start'], $data['keyword'], $data['key_kategori']);
        $data['total'] = $this->ProdukModel->getTotalProdukByKategori();
        $data['kategori'] = $this->KategoriModel->getKategori();

        $this->load->view('ui/template/header', $data);
        $this->load->view('ui/produk', $data);
        $this->load->view('ui/template/footer', $data);
    }
    public function detail($id)
    {
        $data['judul'] = 'Detail Produk | Toko Baju Sri';
        $data['produk'] = $this->ProdukModel->getProdukById($id);
        $data['total'] = $this->ProdukModel->getTotalProdukByKategori();
        $data['kategori'] = $this->KategoriModel->getKategori();
        $this->load->view('ui/template/header', $data);
        $this->load->view('ui/detailproduk', $data);
        $this->load->view('ui/template/footer', $data);
    }
}

/* End of file Produk.php */
