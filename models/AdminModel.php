<?php

defined('BASEPATH') or exit('No direct script access allowed');

class AdminModel extends CI_Model
{

    public function getAdmin()
    {
        // ambil semua data tb_admin
        return $this->db->get('tb_admin')->result();
    }
    public function getTotalAdmin()
    {
        // ambil value count data tb_admin
        return $this->db->get('tb_admin')->num_rows();
    }
    public function getAdminById($id)
    {
        // ambil satu data tb_admin
        return $this->db->get_where('tb_admin', ['id' => $id])->row();
    }
    public function tambahAdmin()
    {
        // tambah adminbaru
        $data = ['nama' => $this->input->post('nama', true), 'username' => $this->input->post('username', true), 'password' => password_hash($this->input->post('password1'), PASSWORD_DEFAULT)];
        $this->db->insert('tb_admin', $data);
    }
    public function ubahAdmin($id)
    {
        // ubah admin
        $data = ['nama' => $this->input->post('nama', true), 'username' => $this->input->post('username', true), 'password' => password_hash($this->input->post('password1'), PASSWORD_DEFAULT)];
        $this->db->set($data);
        $this->db->where('id', $id);
        $this->db->update('tb_admin');
    }
    public function hapusAdmin($id)
    {
        // hapus admin
        $this->db->where('id', $id);
        $this->db->delete('tb_admin');
    }
}

/* End of file adminModel.php */
