<?php

defined('BASEPATH') or exit('No direct script access allowed');

class KategoriModel extends CI_Model
{

    public function getKategori()
    {
        return $this->db->get('tb_kategori')->result();
    }
    public function getTotalKategori()
    {
        return $this->db->get('tb_kategori')->num_rows();
    }
    public function getKategoriById($id)
    {
        return $this->db->get_where('tb_kategori', ['id' => $id])->row();
    }
    public function tambahkategori()
    {
        $data = ['kategori' => $this->input->post('kategori')];
        $this->db->insert('tb_kategori', $data);
    }
    public function ubahkategori($id)
    {
        $data = ['kategori' => $this->input->post('kategori')];
        $this->db->set($data);
        $this->db->where('id', $id);
        $this->db->update('tb_kategori');
    }
    public function hapuskategori($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('tb_kategori');
    }
}

/* End of file KategoriModel.php */
