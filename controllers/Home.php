<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Home extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('ProdukModel');
	}
	public function index()
	{
		$data['judul'] = 'Home | Toko Baju Sri';
		$data['produk'] = $this->ProdukModel->getNewProduk();
		$this->load->view('ui/template/header', $data);
		$this->load->view('ui/index', $data);
		$this->load->view('ui/template/footer', $data);
	}
}
