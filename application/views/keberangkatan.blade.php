@extends('components.layout_admin')

{{-- 
  Section tambahan:
  head : untuk penambahan kode didalam head
  script : untuk penambahan kode didalam script bagian paling bawah halaman
  sidebar : untuk mengatur menu pada sidebar
   --}}

@section('title', 'Jadwal Keberangkatan')
@section('sidebar_title', 'Jadwal Keberangkatan')
@section('user_image', 'images/img.jpg')
@section('username', 'Mandan')
@section('content_title', 'Jadwal Keberangkatan')

@section('content')
  @if($_SESSION['level'] == "Admin")
    <div class="row">
      <div class="col-sm-2 col-xs-12">
        <button type="button" onclick="showModalTambah()" class="btn btn-primary">Tambah Peserta</button>
      </div>
      <div class="col-sm-2 col-xs-12">
        <div class="dropdown">
          <button class="btn btn-success dropdown-toggle" type="button" data-toggle="dropdown">Cetak Laporan
          <span class="caret"></span></button>
          <ul class="dropdown-menu">
            @foreach($data_master_program as $d)
              <li><a href="{{ site_url('laporan/peserta-keberangkatan?id_program='.$d['id']) }}" target="_blank">{{ $d['nama_program'] }}</a></li>
            @endforeach
          </ul>
        </div>
      </div>
    </div>
  @endif
  <table class="table table-bordered table-stripped">
    <tr>
      <th>No</th>
      <th>ID Keberangkatan</th>
      <th>Tanggal Berangkat</th>
      <th>Maskapai</th>
      <th>Nama Program</th>
      <th>Status</th>
      <th>Keterangan</th>
      <th>Kuota</th>
      <th>Aksi</th>
    </tr>
    @foreach($data_list as $nomor => $data)
      <tr>
        <td>{{ ($nomor+1) }}</td>
        <td>{{ $data['id'] }}</td>
        <td>{{ TanggalIndo($data['tgl_berangkat']) }}</td>
        <td>{{ $data['nama_maskapai'] }}</td>
        <td>{{ $data['nama_program'] }} {{ $data['nm_jenis'] }}</td>
        <td>{{ $data['status'] }}</td>
        <td>{{ $data['keterangan'] }}</td>
        <td>{{ $data['kuota'] }} Orang</td>
        <td>
          @if($_SESSION['level'] == "Admin")
            <button type="button" onclick="showModalEdit({{ $nomor }})" class="btn btn-success">Edit</button>
            <button type="button" onclick="showConfirmationDelete('<?=site_url("keberangkatan/hapus?id=".$data['id'])?>')" class="btn btn-danger">Hapus</button>
          @endif
          <a href="{{ site_url('peserta-keberangkatan?id_keberangkatan='.$data['id']) }}" class="btn btn-success">Daftar Peserta</a>
        </td>
      </tr>
    @endforeach
  </table>
  
  <script>
    var data = <?=json_encode($data_list)?>;
    
    function resetModal()
    {
      elId("form_modal").action = "";
      elId("judul_modal").innerHTML = "Tambah Data Baru";
      elName("tgl_berangkat")[0].value = "";
      elName("nama_maskapai")[0].value = "";
      elName("status")[0].value = "";
      elName("keterangan")[0].value = "";
      elName("kuota")[0].value = "";
    }
    
    function closeModal()
    {
      resetModal();
      hideModal("#modal");
    }
    
    function showModalTambah()
    {
      resetModal();
      elId("form_modal").action = "{{ site_url('keberangkatan/tambah') }}";
      showModal("#modal");
    }
    
    function showModalEdit(id)
    {
      elId("judul_modal").innerHTML = "Edit Data";
      resetModal();
      var detail = data[id]; 
      elId("form_modal").action = "{{ site_url('keberangkatan/edit') }}";
      elName("id")[0].value = detail.id;
      elName("tgl_berangkat")[0].value = detail.tgl_berangkat;
      elName("nama_maskapai")[0].value = detail.nama_maskapai;
      elName("status")[0].value = detail.status;
      elName("keterangan")[0].value = detail.keterangan;
      elName("kuota")[0].value = detail.kuota;
      showModal("#modal");
    }
  </script>
  
  <div class="modal fade hide-modal" id="modal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
          </button>
          <h4 class="modal-title" id="judul_modal">Judul Modal</h4>
        </div>
        <div class="modal-body">
          <form id="form_modal" method="POST" action="{{ site_url('keberangkatan/tambah') }}">
            <input type="hidden" name="id">
            @include('components.form.input', ['_data' => ['type' => 'date', 'name' => 'tgl_berangkat', 'class' => 'form-control', 'max' => 10, 'label' => 'Tanggal berangkat']])
            @include('components.form.input', ['_data' => ['type' => 'text', 'name' => 'nama_maskapai', 'class' => 'form-control', 'max' => 50, 'label' => 'Nama Maskapai']])
            <div class="form-group">
              <label>Program</label>
              <select name="id_jenis" class="form-control">
                <?php foreach($data_program as $no => $d): ?>
                  <option value="<?=$d['id']?>"><?=$d['nama_program']?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label>Status</label>
              <select name="status" class="form-control">
                <option value="Belum Berangkat">Belum Berangkat</option>
                <option value="Dalam Perjalanan Pergi">Dalam Perjalanan Pergi</option>
                <option value="Sudah Sampai Tujuan">Sudah Sampai Tujuan</option>
                <option value="Dalam Perjalanan Pulang">Dalam Perjalanan Pulang</option>
                <option value="Sudah Kembali">Sudah Kembali</option>
                <option value="Dibatalkan">Dibatalkan</option>
              </select>
            </div>
            @include('components.form.textarea', ['_data' => ['name' => 'keterangan', 'class' => 'form-control', 'label' => 'Keterangan']])
      			@include('components.form.input', ['_data' => ['type' => 'number', 'name' => 'kuota', 'class' => 'form-control', 'label' => 'Kuota']])
      </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-default" onclick="closeModal()">Tutup</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
    </div>
    </div>
    </form>
  </div>
@endsection
