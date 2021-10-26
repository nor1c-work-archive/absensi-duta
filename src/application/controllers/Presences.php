<?php

class Presences extends CI_Controller {
  public function __construct() {
    parent::__construct();
    $this->load->model(array('User_model', 'master/Jam_kerja_m', 'Presences_m', 'Workday_plan_model'));
    $this->load->library('template');

    if (!$this->session->userdata('logged_in')) {
      redirect('/');
    }
  }

  public function index() {
    $selisih_hari = 0;
    $data['selisih_hari'] = $selisih_hari;

    $today = date('Y-m-d');
    $minus = mktime(0, 0, 0, date('m'), date('d') - $selisih_hari, date('Y'));
    $pastmonth = date('Y-m-d', $minus);

    if ($this->input->post('presences_date_start') || $this->input->post('presences_date_end')) {
      $data['date_start'] = $this->input->post('presences_date_start');
      $data['date_end'] = $this->input->post('presences_date_end');
      if ($this->input->post('id_karyawan') != null || $this->input->post('id_karyawan') != "" || empty($this->input->post('id_karyawan'))) {
        $data['id_karyawan'] = $this->input->post('id_karyawan');
        $this->session->set_userdata('id_karyawan', $this->input->post('id_karyawan'));
      } else {
        $data['id_karyawan'] = null;
      }
    } else {
      $data['date_start'] = $this->tanggal->tanggal_indo($pastmonth);
      $data['date_end'] = $this->tanggal->tanggal_indo($today);
    }

    $data['kehadiran'] = $this->Presences_m->get_attendance($data);

    $this->template->display('presences/presences', $data);
  }

  public function indeax() {
    $selisih_hari = 7;

    $data = array();
    $user_id = $this->session->userdata('user_id');
    $data['user'] = $this->User_model->get_by_id($user_id)->row_array();
    $data['jam_kerja'] = $this->Jam_kerja_m->get_by_id($data['user']['id_jam_kerja'])->row_array();

    $today = date('Y-m-d');
    $minus = mktime(0, 0, 0, date('m'), date('d') - $selisih_hari, date('Y'));
    $pastmonth = date('Y-m-d', $minus);

    $data['date_start'] = $this->tanggal->tanggal_indo($pastmonth);
    $data['date_end'] = $this->tanggal->tanggal_indo($today);
    if ($_POST) {
      $today = $this->tanggal->tanggal_simpan_db($this->input->post('presences_date_end'));
      $pastmonth = $this->tanggal->tanggal_simpan_db($this->input->post('presences_date_start'));

      $selisih_hari = $this->tanggal->get_selisih($today, $pastmonth);

      $data['date_start'] = $this->input->post('presences_date_start');
      $data['date_end'] = $this->input->post('presences_date_end');
    }

    $data['kehadiran'] = array();
    for ($i = $selisih_hari; $i >= 0; $i--) {
      $temp = mktime(0, 0, 0, $this->tanggal->get_only_month($today), $this->tanggal->get_only_date($today) - $i, $this->tanggal->get_only_year($today));
      $tanggal = date('Y-m-d', $temp);

      $data['kehadiran'][$i]['tanggal'] = $this->tanggal->tanggal_indo_monthtext($tanggal);
      $data['kehadiran'][$i]['hari'] = $this->tanggal->get_hari($tanggal);
      if ($this->Presences_m->get_by_date($tanggal, $user_id)->num_rows() > 0) {
        $present = $this->Presences_m->get_by_date($tanggal, $user_id)->row_array();
        $data['kehadiran'][$i]['datang'] = $this->tanggal->get_jam($present['jam_masuk']);
        $data['kehadiran'][$i]['pulang'] = $this->tanggal->get_jam($present['jam_keluar']);
        $data['kehadiran'][$i]['nama'] = $present['nama'];
        $data['kehadiran'][$i]['alasan'] = $present['nama_alasan'] . '(' . $present['keterangan'] . ')';
        if ($present['id_alasan'] == '5') {
          $data['kehadiran'][$i]['alasan'] = '-';
        }
      } else {
        $data['kehadiran'][$i]['datang'] = '-';
        $data['kehadiran'][$i]['pulang'] = '-';
        $data['kehadiran'][$i]['alasan'] = '-';
        $data['kehadiran'][$i]['nama'] = '-';
      }
      $workday_count = $this->Workday_plan_model->get_by_date(intval($this->tanggal->get_only_date($tanggal)), $this->tanggal->get_only_month($tanggal), $this->tanggal->get_only_year($tanggal))->num_rows();
      if ($workday_count > 0) {
        $workday = $this->Workday_plan_model->get_by_date(intval($this->tanggal->get_only_date($tanggal)), $this->tanggal->get_only_month($tanggal), $this->tanggal->get_only_year($tanggal))->row_array();
        if ($workday['status'] == '0') {
          $data['kehadiran'][$i]['datang'] = $workday['keterangan'];
          $data['kehadiran'][$i]['pulang'] = $workday['keterangan'];
          $data['kehadiran'][$i]['alasan'] = $workday['keterangan'];
        }
      }
    }
    $this->template->display('presences/presences', $data);
  }

  public function report_pdf() {
    $startdate = date("Y-m-d", strtotime($this->input->get('startdate')));
    $enddate = date("Y-m-d", strtotime($this->input->get('enddate')));
    $id_karyawan = $this->input->get('id_karyawan');

    $selisih_hari = 0;
    $data['selisih_hari'] = $selisih_hari;

    $today = date('Y-m-d');
    $minus = mktime(0, 0, 0, date('m'), date('d') - $selisih_hari, date('Y'));
    $pastmonth = date('Y-m-d', $minus);

    if ($startdate || $enddate) {
      $data['date_start'] = $this->input->get('startdate');
      $data['date_end'] = $this->input->get('enddate');
      if ($id_karyawan != null || $id_karyawan != "") {
        $data['id_karyawan'] = $id_karyawan;
      }
    } else {
      $data['date_start'] = $this->tanggal->tanggal_indo($pastmonth);
      $data['date_end'] = $this->tanggal->tanggal_indo($today);
    }

    $data['kehadiran'] = $this->Presences_m->get_attendance($data);
    $this->load->view('presences/report_pdf', $data);
  }

  public function input_report_pdf() {
    $startdate = date("Y-m-d", strtotime($this->input->get('startdate')));
    $enddate = date("Y-m-d", strtotime($this->input->get('enddate')));
    $id_karyawan = $this->input->get('id_karyawan');
    $id_judul_buku = $this->input->get('id_judul_buku');

    $selisih_hari = 0;
    $data['selisih_hari'] = $selisih_hari;

    $today = date('Y-m-d');
    $minus = mktime(0, 0, 0, date('m'), date('d') - $selisih_hari, date('Y'));
    $pastmonth = date('Y-m-d', $minus);

    if ($startdate || $enddate) {
      $data['date_start'] = $this->input->get('startdate');
      $data['date_end'] = $this->input->get('enddate');
      if ($id_karyawan != null || $id_karyawan != "") {
        $data['id_karyawan'] = $id_karyawan;
      }
      if ($id_judul_buku != null || $id_judul_buku != "") {
        $data['id_judul_buku'] = $id_judul_buku;
      }
    } else {
      $data['date_start'] = $this->tanggal->tanggal_indo($pastmonth);
      $data['date_end'] = $this->tanggal->tanggal_indo($today);
    }

    $data['report_pekerjaan'] = $this->Presences_m->get_report_pekerjaan($data);
    $this->load->view('presences/input_report_pdf', $data);
  }

  public function input() {
    $this->form_validation->set_rules('nik', 'NIK', 'required');
    $this->form_validation->set_rules('pwd', 'Password', 'required');
    $this->form_validation->set_rules('type_absen', 'Type', 'required');

    if ($this->form_validation->run() == false) {
      // check job role
      

      $this->template->display('presences/input_view');
    } else {
      if ($this->User_model->check_absen($this->input->post('nik'), $this->input->post('pwd'))) {

        $data_user = $this->User_model->get_by_nik($this->input->post('nik'))->row_array();

        if ($this->input->post('type_absen') == '1') {
          $cek = $this->Presences_m->get_by_date(date('Y-m-d'), $data_user['id_karyawan']);
          $num_row = $cek->num_rows();
          if ($num_row > 0) {
            if ($cek->row()->jam_masuk == null || $cek->row()->jam_masuk == "") {
              $data_kehadiran = array(
                'id_karyawan' => $data_user['id_karyawan'],
                'tanggal' => date('Y-m-d'),
                'jam_masuk' => date('Y-m-d H:i:s'),
                'hadir' => '1',
                'id_alasan' => '5',
                'created_date' => date('Y-m-d'),
                'created_user' => $this->session->userdata('user_id'),
                'active' => '1',
              );
              $data_kehadiran['computer_name'] = gethostbyaddr($_SERVER['REMOTE_ADDR']);
              $this->Presences_m->update($cek->row()->id_kehadiran, $data_kehadiran);

              $this->session->set_flashdata('message_alert', '<div class="alert alert-success">Data Terupdate</div>');
              redirect('presences/input');
            } else {
              $this->session->set_flashdata('message_alert', '<div class="alert alert-danger">Anda Sudah Absen Untuk Hari Ini .</div>' . $cek->row()->jam_masuk);
              redirect('presences/input');
            }
          } else {
            $data_kehadiran = array(
              'id_karyawan' => $data_user['id_karyawan'],
              'tanggal' => date('Y-m-d'),
              'jam_masuk' => date('Y-m-d H:i:s'),
              'hadir' => '1',
              'id_alasan' => '5',
              'created_date' => date('Y-m-d'),
              'created_user' => $this->session->userdata('user_id'),
              'active' => '1',
            );
            $data_kehadiran['computer_name'] = gethostbyaddr($_SERVER['REMOTE_ADDR']);
            $this->Presences_m->save($data_kehadiran);

            $this->session->set_flashdata('message_alert', '<div class="alert alert-success">Data Tersimpan</div>');
            redirect('presences/input');
          }
        } else {

          $cek = $this->Presences_m->get_by_date(date('Y-m-d'), $data_user['id_karyawan'])->num_rows();
          if ($cek > 0) {

            $tmp = $this->Presences_m->get_by_date(date('Y-m-d'), $data_user['id_karyawan'])->row_array();

            $data_kehadiran = array(
              'jam_keluar' => date('Y-m-d H:i:s'),
              'updated_date' => date('Y-m-d'),
              'updated_user' => $this->session->userdata('user_id'),
            );
            $data_kehadiran['computer_name'] = gethostbyaddr($_SERVER['REMOTE_ADDR']);
            $this->Presences_m->update($tmp['id_kehadiran'], $data_kehadiran);

            $this->session->set_flashdata('message_alert', '<div class="alert alert-success">Data Tersimpan</div>');
            redirect('presences/input');
          } else {

            $this->session->set_flashdata('message_alert', '<div class="alert alert-danger">Data absen is not available. Please input the in time first.</div>');
            redirect('presences/input');
          }
        }

      } else {
        $this->session->set_flashdata('message_alert', '<div class="alert alert-danger">Username atau password tidak terdaftar.</div>');
        redirect('presences/input');
      }
    }
  }

  public function buku_dikerjakan() {
    $id = $this->input->post('id');

    echo json_encode($this->db->get_where('buku_dikerjakan', array('id' => $id))->row());
  }

  public function report_pekerjaan() {
    //
    $data['id_karyawan'] = $this->session->userdata('user_id');
    $data['pekerjaan'] = $this->input->post('pekerjaan');
    if ($this->input->post('id_buku') == "") {

      $data['id_buku_dikerjakan'] = null;
    } else {

      $data['id_buku_dikerjakan'] = $this->input->post('id_buku');
    }
    $data['catatan'] = $this->input->post('catatan');
    $data['target'] = $this->input->post('target');
    $data['status'] = $this->input->post('status');
    $data['realisasi_target'] = $this->input->post('realisasi_target');
    $data['date'] = date("Y-m-d");

    if ($this->db->insert('report_pekerjaan', $data)) {
      $this->session->set_flashdata('success', 1);
      redirect('presences/input', 'refresh');
    }
  }

  public function update_report_pekerjaan() {
    $work_id = $this->input->get('work-id');
    $data['realisasi_target'] = $this->input->post('realisasi_target');
    $data['status'] = $this->input->post('status');

    $updated = $this->Presences_m->update_report_pekerjaan($work_id, $data);
    
    if ($updated) {
      $this->session->set_flashdata('success_update', 1);
      redirect('presences/input', 'refresh');
    }
  }

  public function laporan_filter() {
    $startdate = date("Y-m-d", strtotime($this->input->post('startdate')));
    $enddate = date("Y-m-d", strtotime($this->input->post('enddate')));

    $id_karyawan = $this->input->post('id_karyawan');
    $id_judul_buku = $this->input->post('id_judul_buku');

    $this->session->set_flashdata('startdate', $startdate);
    $this->session->set_flashdata('enddate', $enddate);
    $this->session->set_flashdata('id_karyawan', $id_karyawan);
    $this->session->set_flashdata('id_judul_buku', $id_judul_buku);

    redirect('presences/input', 'refresh');
  }
}
