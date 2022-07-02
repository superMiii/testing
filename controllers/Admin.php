<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        // meload model yang dibutuhkan untuk bisa berinteraksi dengan database
        $this->load->model('ProdukModel');
        $this->load->model('KategoriModel');
        $this->load->model('AdminModel');
        // pengecekan jika tidak ada session user maka dialihkan ke halaman login
        if (!$this->session->userdata('id')) {
            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Untuk mengakses admin, anda perlu login.</div>');
            redirect('login');
        }
        $data['admin'] = $this->db->get_where('tb_admin', ['username' => $this->session->userdata('username')])->row_array();
    }
    public function index()
    {
        $data['judul'] = 'Dashboard Admin | Toko Baju Sri';
        // get value count data products, kategori, admin, pada database dari model yang sudah di load tadi pada bagian construct
        $data['total_produk'] = $this->ProdukModel->getTotalProduk();
        $data['total_kategori'] = $this->KategoriModel->getTotalKategori();
        $data['total_admin'] = $this->AdminModel->getTotalAdmin();
        $this->load->view('admin/template/header', $data);
        $this->load->view('admin/template/sidebar', $data);
        $this->load->view('admin/index', $data);
        $this->load->view('admin/template/footer', $data);
    }
    public function produk()
    {
        $data['judul'] = 'Produk | Toko Baju Sri';
        $data['produk'] = $this->ProdukModel->getProdukdiAdmin();
        $data['total'] = $this->ProdukModel->getTotalProdukByKategori();
        $this->load->view('admin/template/header', $data);
        $this->load->view('admin/template/sidebar', $data);
        $this->load->view('admin/produk/index', $data);
        $this->load->view('admin/template/footer', $data);
    }
    public function tambahproduk()
    {
        // inisialisasi aturan validasi
        $this->form_validation->set_rules('nama_produk', 'nama produk', 'trim|required');
        $this->form_validation->set_rules('kategori', 'kategori', 'trim|required');
        $this->form_validation->set_rules('deskripsi', 'deskripsi', 'trim|required');
        $this->form_validation->set_rules('harga', 'harga', 'trim|required|numeric');

        // jika validasi gagal tampilkan kembali form jika berhasil masukkan data
        if ($this->form_validation->run() === FALSE) {
            $data['judul'] = 'Tambah Produk | Toko Baju Sri';
            $data['kategori'] = $this->KategoriModel->getKategori();
            $this->load->view('admin/template/header', $data);
            $this->load->view('admin/template/sidebar', $data);
            $this->load->view('admin/produk/tambah', $data);
            $this->load->view('admin/template/footer', $data);
        } else {
            $config['upload_path']          = 'assets/img/produk';
            $config['allowed_types']        = 'jpeg|jpg|png';
            $this->load->library('upload', $config);
            if (!empty($_FILES['foto']['name'])) {
                if ($this->upload->do_upload('foto')) {
                    $this->ProdukModel->tambahData();
                    $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Produk berhasil ditambah</div');
                    redirect('admin/produk');
                }
            } else {
                echo 'tidak ada photo, harap masukkan foto untuk produk. <br> <a href="' . base_url('admin/tambahproduk') . '" class="btn btn-secondary">Kembali</a>';
            }
        }
    }
    public function ubahproduk($id)
    {
        $this->form_validation->set_rules('nama_produk', 'nama produk', 'trim|required');
        $this->form_validation->set_rules('kategori', 'kategori', 'trim|required');
        $this->form_validation->set_rules('deskripsi', 'deskripsi', 'trim|required');
        $this->form_validation->set_rules('harga', 'harga', 'trim|required|numeric');

        $data['produk'] = $this->ProdukModel->getProdukById($id);

        if ($this->form_validation->run() === FALSE) {
            $data['judul'] = 'Ubah Produk | Toko Baju Sri';
            $data['kategori'] = $this->KategoriModel->getKategori();
            $this->load->view('admin/template/header', $data);
            $this->load->view('admin/template/sidebar', $data);
            $this->load->view('admin/produk/ubah', $data);
            $this->load->view('admin/template/footer', $data);
        } else {
            //jika ada gambar yang diubah hapus yg lama ganti dengan yang baru, jika tidak ada yg di ubah masukkan data image yg sama
            $uploadimage = $_FILES['foto']['name'];

            if ($uploadimage) {
                $config['upload_path']          = 'assets/img/produk/';
                $config['allowed_types']        = 'jpeg|jpg|png';

                $this->load->library('upload', $config);

                if ($this->upload->do_upload('foto')) {
                    $old_image = $data['produk']->foto;
                    if ($old_image != 'default.png') {
                        unlink(FCPATH . 'assets/img/produk/' . $old_image);
                    }
                    $this->ProdukModel->ubahData($id);
                    $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Produk berhasil diubah</div');
                    redirect('admin/produk');
                }
            } else {
                $prev_img = $data['produk']->foto;
                $data = [
                    'nama_produk' => $this->input->post('nama_produk', true),
                    'kategori_id' => $this->input->post('kategori', true),
                    'deskripsi' => $this->input->post('deskripsi', true),
                    'foto' => $prev_img,
                    'harga' => $this->input->post('harga')
                ];
                $this->db->set($data);
                $this->db->where('id_produk', $id);
                $this->db->update('tb_produk');
                $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Produk berhasil diubah</div');
                redirect('admin/produk');
            }
        }
    }
    public function hapusproduk($id)
    {
        $data['produk'] = $this->ProdukModel->getProdukById($id);

        $old_image = $data['produk']->foto;
        if ($old_image != 'default.png') {
            unlink(FCPATH . 'assets/img/produk/' . $old_image);
        }
        $this->ProdukModel->hapusProduk($id);
        $this->session->set_flashdata('produk', 'diubah');
        redirect('admin/produk');
    }

    // kategori produk
    public function kategoriproduk()
    {
        $data['judul'] = 'Kategori Produk | Toko Baju Sri';
        $data['kategori'] = $this->KategoriModel->getKategori();
        $this->load->view('admin/template/header', $data);
        $this->load->view('admin/template/sidebar', $data);
        $this->load->view('admin/kategori/index', $data);
        $this->load->view('admin/template/footer', $data);
    }
    public function tambahkategori()
    {
        $this->form_validation->set_rules('kategori', 'kategori', 'trim|required|is_unique[tb_kategori.kategori]');

        if ($this->form_validation->run() === FALSE) {
            $data['judul'] = 'Tambah Kategori | Toko Baju Sri';
            $this->load->view('admin/template/header', $data);
            $this->load->view('admin/template/sidebar', $data);
            $this->load->view('admin/kategori/tambah', $data);
            $this->load->view('admin/template/footer', $data);
        } else {
            $this->KategoriModel->tambahkategori();
            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Kategori berhasil ditambah</div');
            redirect('admin/kategoriproduk');
        }
    }
    public function ubahkategori($id)
    {
        $this->form_validation->set_rules('kategori', 'kategori', 'trim|required|is_unique[tb_kategori.kategori]');
        $data['kategori'] = $this->KategoriModel->getKategoriById($id);

        if ($this->form_validation->run() === FALSE) {
            $data['judul'] = 'Tambah Kategori | Toko Baju Sri';
            $this->load->view('admin/template/header', $data);
            $this->load->view('admin/template/sidebar', $data);
            $this->load->view('admin/kategori/ubah', $data);
            $this->load->view('admin/template/footer', $data);
        } else {
            $this->KategoriModel->ubahkategori($id);
            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Kategori berhasil diubah</div');
            redirect('admin/kategoriproduk');
        }
    }
    public function hapuskategori($id)
    {
        $this->KategoriModel->hapuskategori($id);
        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Kategori berhasil dihapus</div');
        redirect('admin/kategoriproduk');
    }

    // admin
    public function kelolaadmin()
    {
        $data['judul'] = 'Kelola Admin | Toko Baju Sri';
        $data['admin'] = $this->AdminModel->getAdmin();
        $this->load->view('admin/template/header', $data);
        $this->load->view('admin/template/sidebar', $data);
        $this->load->view('admin/kelolaadmin/index', $data);
        $this->load->view('admin/template/footer', $data);
    }
    public function tambahadmin()
    {
        $this->form_validation->set_rules('username', 'username', 'trim|required|is_unique[tb_admin.username]');
        $this->form_validation->set_rules('nama', 'nama', 'trim|required');
        $this->form_validation->set_rules('password1', 'password', 'trim|required|matches[password2]|min_length[6]');
        $this->form_validation->set_rules('password2', 'repeat password', 'trim|required|matches[password1]|min_length[6]');

        if ($this->form_validation->run() === FALSE) {
            $data['judul'] = 'Tambah Admin | Toko Baju Sri';
            $this->load->view('admin/template/header', $data);
            $this->load->view('admin/template/sidebar', $data);
            $this->load->view('admin/kelolaadmin/tambah', $data);
            $this->load->view('admin/template/footer', $data);
        } else {
            $this->AdminModel->tambahadmin();
            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Admin berhasil ditambah</div');
            redirect('admin/kelolaadmin');
        }
    }
    public function ubahadmin($id)
    {
        $data['admin'] = $this->AdminModel->getAdminById($id);
        if ($this->session->userdata('id') === $data['admin']->id) {
            $this->form_validation->set_rules('username', 'username', 'trim|required');
            $this->form_validation->set_rules('nama', 'nama', 'trim|required');
            $this->form_validation->set_rules('password1', 'password', 'trim|required|matches[password2]|min_length[6]');
            $this->form_validation->set_rules('password2', 'repeat password', 'trim|required|matches[password1]|min_length[6]');

            if ($this->form_validation->run() === FALSE) {
                $data['judul'] = 'ubah Admin | Toko Baju Sri';
                $this->load->view('admin/template/header', $data);
                $this->load->view('admin/template/sidebar', $data);
                $this->load->view('admin/kelolaadmin/ubah', $data);
                $this->load->view('admin/template/footer', $data);
            } else {
                $this->AdminModel->ubahadmin($id);
                $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Admin berhasil diubah</div');
                redirect('admin/kelolaadmin');
            }
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">dilarang merubah data admin yang lain</div');
            redirect('admin/kelolaadmin');
        }
    }
    public function hapusadmin($id)
    {
        $data['admin'] = $this->AdminModel->getAdminById($id);
        if ($this->session->userdata('id') === $data['admin']->id) {
            $this->KategoriModel->hapusadmin($id);
            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Akun anda terhapus</div');
            $this->session->unset_userdata('username');
            $this->session->unset_userdata('nama');
            $this->session->unset_userdata('id');
            redirect('login');
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">dilarang merubah data admin yang lain</div');
            redirect('admin/kelolaadmin');
        }
    }
}

/* End of file Admin.php */
