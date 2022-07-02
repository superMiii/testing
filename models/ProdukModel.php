<?php

defined('BASEPATH') or exit('No direct script access allowed');

class ProdukModel extends CI_Model
{

    public function getProduk($limit, $start, $keyword = '', $kategori)
    {
        // jika keyword ada, cari data yang sama dengan nama_produk, kategori, harga, dan deskripsi
        if ($keyword) {
            $this->db->like('nama_produk', $keyword);
            $this->db->or_like('kategori', $keyword);
            $this->db->or_like('harga', $keyword);
            $this->db->or_like('deskripsi', $keyword);
        }
        // jika key kategori ada, carikan sesuai kategori
        if ($kategori) {
            $this->db->where('kategori', $kategori);
        }
        $this->db->select('*');
        $this->db->from('tb_produk');
        $this->db->join('tb_kategori', 'tb_produk.kategori_id = tb_kategori.id');
        $this->db->order_by('id_produk', 'DESC');
        // kembalikan dengan limit dan data mulai untuk pagination
        return $this->db->get('', $limit, $start)->result();
    }
    public function getProdukdiAdmin()
    {
        $this->db->select('*');
        $this->db->from('tb_produk');
        $this->db->join('tb_kategori', 'tb_produk.kategori_id = tb_kategori.id');
        $this->db->order_by('id_produk', 'DESC');
        return $this->db->get()->result();
    }
    public function getTotalProduk()
    {
        // hitung semua produk
        return $this->db->get('tb_produk')->num_rows();
    }
    public function getProdukById($id)
    {
        $this->db->select('*');
        $this->db->from('tb_produk');
        $this->db->join('tb_kategori', 'tb_produk.kategori_id = tb_kategori.id');
        $this->db->where('id_produk', $id);
        return $this->db->get()->row();
    }
    public function getTotalProdukByKategori()
    {
        // hitung produk sesuai per kategori
        $data = "SELECT COUNT(id_produk) AS total_semua, SUM(kategori_id = 1) AS total_baju, SUM(kategori_id = 4) AS total_celana, SUM(kategori_id = 2) AS total_jaket, SUM(kategori_id = 3) AS total_tas FROM tb_produk";

        return $this->db->query($data)->row();
    }
    public function getNewProduk()
    {
        // mengambil produk terbaru yang ada di db dengan limit data sebanyak 6
        $data = "SELECT * FROM tb_produk ORDER BY id_produk DESC LIMIT 6";

        return $this->db->query($data)->result();
    }
    public function tambahData()
    {
        $data = [
            'nama_produk' => $this->input->post('nama_produk', true),
            'kategori_id' => $this->input->post('kategori', true),
            'deskripsi' => $this->input->post('deskripsi', true),
            'foto' => $this->upload->data('file_name'),
            'harga' => $this->input->post('harga')
        ];

        $this->db->insert('tb_produk', $data);
    }
    public function ubahData($id)
    {
        $data = [
            'nama_produk' => $this->input->post('nama_produk', true),
            'kategori_id' => $this->input->post('kategori', true),
            'deskripsi' => $this->input->post('deskripsi', true),
            'foto' => $this->upload->data('file_name'),
            'harga' => $this->input->post('harga')
        ];
        $this->db->set($data);
        $this->db->where('id_produk', $id);
        $this->db->update('tb_produk');
    }
    public function hapusproduk($id)
    {
        $this->db->where('id_produk', $id);
        $this->db->delete('tb_produk');
    }
}

/* End of file ProdukModel.php */
