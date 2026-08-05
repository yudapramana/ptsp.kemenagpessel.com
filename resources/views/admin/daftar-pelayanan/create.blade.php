@extends('layouts.admin.master')
@section('title', $title)


@section('_styles')
    <link rel="stylesheet" href="{{ asset('css/dataTables.bootstrap5.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/buttons.bootstrap5.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/select2-bootstrap-5-theme.rtl.min.css') }}" />
    {{-- File Pond --}}
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/filepond/filepond.css') }}">

    <style>
        .responsive-iframe {
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
            width: 100%;
            height: 100%;
        }

        .modal-print {
            min-height: 700px !important;
        }
    </style>
@endsection


@section('content')

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>{{ $title }}</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">{{ $br1 }}</a></li>
                    <li class="breadcrumb-item active">{{ $br2 }}</li>
                </ol>
            </nav>
        </div>
        <section class="section">
            <div class="row">
                {{-- <div class="row nav" id="nav-tab" role="tablist"> --}}

                {{-- Input Pelayanan Section --}}
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title m-0 p-0"> <i class="bi bi-plus-square"></i> Input Data
                                Pelayanan</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mt-2">
                                <div class="col-sm-12">
                                    <div class="input-group mt-2">
                                        <a id="inputButton" class="btn btn-primary mr-2" data-bs-toggle="collapse" href="#inputSection" role="button" aria-expanded="false" aria-controls="inputSection" data-bs-target=".input-collapse">
                                            Tambah Pelayanan
                                        </a>
                                        @if ($type == 'MINIMAL')
                                            <a id="simpleButton" class="btn btn-secondary" data-bs-toggle="collapse" href="#inputSimpleSection" role="button" aria-expanded="false" aria-controls="inputSimpleSection" data-bs-target=".input-collapse">
                                                Tambah Simple
                                            </a>
                                        @endif
                                    </div>
                                </div>


                            </div>
                        </div>

                    </div>
                </div>

                {{-- Lacak Pelayanan Section --}}
                <div class="col-lg-6">
                    <div class="card" id="find-data">
                        <div class="card-header">
                            <h5 class="card-title m-0 p-0"> <i class="bi bi-search"></i> Cari Data Pelayanan</h5>
                        </div>
                        <div class="card-body">

                            <div class="row mt-2">
                                <div class="col-sm-8">
                                    <div class="input-group mt-2">
                                        {{-- <input type="text" class="form-control" placeholder="Ketik No. Registrasi"
                                            aria-label="Ketik No. Registrasi" aria-describedby="basic-addon2"> --}}


                                        <select name="id_daftar_pelayanan" id="search-pelayanan" class="form-control select2-search">
                                        </select>
                                        <button class="input-group-text" id="searchButton" data-bs-toggle="collapse" href="#searchSection" role="button" aria-expanded="false" aria-controls="searchSection" data-bs-target=".search-collapse">
                                            <i class="bi bi-search"></i>&nbsp;&nbsp;Cari
                                        </button>

                                        {{-- <button class="input-group-text" id="basic-addon2" id="nav-profile-tab"
                                            data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab"
                                            aria-controls="nav-profile" aria-selected="false">
                                            <i class="bi bi-search"></i>&nbsp;&nbsp;Cari
                                        </button> --}}
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>


            {{-- <div class="tab-content" id="nav-tabContent"> --}}


            <div class="form-box">

                {{-- <div class="row tab-pane fade" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab"> --}}
                <div class="row collapse input-collapse" id="inputSection">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title m-0 p-0"> <i class="bi bi-card-checklist"></i> Form Data Pelayanan
                                </h5>
                            </div>
                            <div class="card-body">

                                <form class="row g-3 mt-2 needs-validation" novalidate id="fForm" method="post" action="{{ route('daftar-pelayanan.store') }}" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <div class="col-md-12 form-group">
                                        <label for="id_layanan" class="form-label fw-bold">Nama Layanan</label>
                                        <select name="id_layanan" id="id_layanan" class="form-control select2 custom-select">
                                            <option selected value="">Pilih Layanan</option>
                                            @foreach ($daftar_layanan as $layanan)
                                                <option value="{{ $layanan->id_layanan }}">{{ $layanan->unit->name . ' - ' . $layanan->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-12 template-syarat" style="display: none">
                                        <ul class="list-group">
                                            <li class="list-group-item list-group-item-secondary">Syarat Layanan</li>
                                            <li class="list-group-item"><span id="message-syarat"></span>
                                            </li>
                                        </ul>
                                    </div>

                                    <div class="col-md-12 form-group">
                                        <label for="perihal" class="form-label fw-bold">Perihal</label>
                                        {{-- <input class="form-control" name="perihal" id="perihal" type="text" value="" placeholder="Perihal"> --}}

                                        <div class="input-group mt-2">
                                            {{-- <input type="text" class="form-control" placeholder="Ketik No. Registrasi"
                                                aria-label="Ketik No. Registrasi" aria-describedby="basic-addon2"> --}}
                                            <input class="form-control" name="perihal" id="perihal" type="text" value="" placeholder="Perihal">
                                            <button class="input-group-text" id="copy-from-layanan" role="button" type="button" aria-expanded="false" aria-controls="searchSection">
                                                <i class="bi bi- clipboard-check"></i>&nbsp;&nbsp;Salin dari Layanan
                                            </button>

                                            {{-- <button class="input-group-text" id="basic-addon2" id="nav-profile-tab"
                                                data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab"
                                                aria-controls="nav-profile" aria-selected="false">
                                                <i class="bi bi-search"></i>&nbsp;&nbsp;Cari
                                            </button> --}}
                                        </div>
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label for="pemohon_no_surat" class="form-label fw-bold">No. Surat
                                            Permohonan</label>
                                        <input class="form-control" name="pemohon_no_surat" id="pemohon_no_surat" type="text" placeholder="Nomor Surat" value="">
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label for="pemohon_tanggal_surat" class="form-label fw-bold">Tanggal Surat
                                            Permohonan</label>
                                        <input type="date" class="form-control" name="pemohon_tanggal_surat" id="pemohon_tanggal_surat" placeholder="Tanggal Surat" value="{{ date('Y-m-d') }}">
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label for="pemohon_nama" class="form-label fw-bold">Nama Pemohon</label>
                                        <input class="form-control" name="pemohon_nama" id="pemohon_nama" type="text" placeholder="Nama Pemohon" value="">
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label for="pemohon_alamat" class="form-label fw-bold">Alamat Pemohon</label>
                                        <input class="form-control" name="pemohon_alamat" id="pemohon_alamat" type="text" placeholder="Alamat Pemohon" value="">
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label for="pemohon_no_hp" class="form-label fw-bold">No. HP Pemohon</label>
                                        <input class="form-control" name="pemohon_no_hp" id="pemohon_no_hp" type="text" placeholder="Nomor HP Pemohon" value="">
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label for="pengirim_nama" class="form-label fw-bold">Nama Pengirim</label>
                                        <input class="form-control" name="pengirim_nama" id="pengirim_nama" type="text" placeholder="Nama Pengirim" value="">
                                    </div>

                                    {{-- <div class="col-md-12 form-group">
                                        <label for="kelengkapan_syarat" class="form-label fw-bold">Kelengkapan
                                            Syarat</label>
                                        <select name="kelengkapan_syarat" id="kelengkapan_syarat" class="form-control select2 custom-select">
                                            <option selected value="">-- Pilih Kelengkapan Syarat --</option>
                                            <option value="Sudah Lengkap">Sudah Lengkap</option>
                                            <option value="Belum Lengkap">Belum Lengkap</option>
                                        </select>
                                    </div> --}}

                                    <input type="hidden" name="kelengkapan_syarat" id="kelengkapan_syarat" value="Sudah Lengkap">
                                    <input type="hidden" name="status_pelayanan" id="status_pelayanan" value="Baru">

                                    <div class="col-12">
                                        <label for="catatan" class="form-label fw-bold">Catatan</label>
                                        <textarea class="form-control" style="height: 100px" name="catatan" id="catatan"></textarea>
                                    </div>

                                    <!--  For multiple file uploads  -->
                                    <div class="col-12">
                                        <label for="pengirim_nama" class="form-label fw-bold">Dokumen Pendukung</label>
                                        <input type="file" name="data_file[]" multiple required />
                                        <p class="help-block">{{ $errors->first('data_file.*') }}</p>
                                    </div>


                                    <div class="card-footer">
                                        <button id="submitPelayananBtn" type="button" class="btn btn-primary float-end">Simpan Data
                                            Pelayanan</button>
                                        <button type="reset" class="btn btn-secondary float-start">Reset</button>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>


                {{-- SIMPLE --}}
                <div class="row collapse input-collapse" id="inputSimpleSection">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title m-0 p-0"> <i class="bi bi-card-checklist"></i> Form Data Simple
                                </h5>
                            </div>
                            <div class="card-body">

                                <form class="row g-3 mt-2 needs-validation" novalidate id="sForm" method="post" action="{{ route('daftar-pelayanan.store.simple') }}" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <div class="col-md-12 form-group">
                                        <label for="id_layanan2" class="form-label fw-bold">Nama Layanan</label>
                                        <select name="id_layanan2" id="id_layanan2" class="form-control select2 custom-select">
                                            <option selected value="">Pilih Layanan</option>
                                            @foreach ($daftar_layanan as $layanan)
                                                <option value="{{ $layanan->id_layanan }}">{{ $layanan->unit->name . ' - ' . $layanan->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-12 template-syarat" style="display: none">
                                        <ul class="list-group">
                                            <li class="list-group-item list-group-item-secondary">Syarat Layanan</li>
                                            <li class="list-group-item"><span id="message-syarat"></span>
                                            </li>
                                        </ul>
                                    </div>



                                    <div class="col-md-6 form-group">
                                        <label for="pemohon_nama2" class="form-label fw-bold">Nama Pemohon</label>
                                        <input class="form-control" name="pemohon_nama2" id="pemohon_nama2" type="text" placeholder="Nama Pemohon" value="">
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label for="pemohon_no_hp2" class="form-label fw-bold">No. HP Pemohon</label>
                                        <input class="form-control" name="pemohon_no_hp2" id="pemohon_no_hp2" type="text" placeholder="Nomor HP Pemohon" value="">
                                    </div>

                                    <div class="col-md-12 form-group">
                                        <label for="perihal2" class="form-label fw-bold">Perihal</label>
                                        {{-- <input class="form-control" name="perihal2" id="perihal2" type="text" value="" placeholder="Perihal2"> --}}

                                        <div class="input-group mt-2">
                                            {{-- <input type="text" class="form-control" placeholder="Ketik No. Registrasi"
                                                aria-label="Ketik No. Registrasi" aria-describedby="basic-addon2"> --}}
                                            <input class="form-control" name="perihal2" id="perihal2" type="text" value="" placeholder="Perihal">
                                            <button class="input-group-text" id="copy-from-layanan2" role="button" type="button" aria-expanded="false" aria-controls="searchSection">
                                                <i class="bi bi- clipboard-check"></i>&nbsp;&nbsp;Salin dari Layanan
                                            </button>

                                            {{-- <button class="input-group-text" id="basic-addon2" id="nav-profile-tab"
                                                data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab"
                                                aria-controls="nav-profile" aria-selected="false">
                                                <i class="bi bi-search"></i>&nbsp;&nbsp;Cari
                                            </button> --}}
                                        </div>
                                    </div>



                                    <input type="hidden" name="kelengkapan_syarat" id="kelengkapan_syarat" value="Sudah Lengkap">
                                    <input type="hidden" name="status_pelayanan" id="status_pelayanan" value="Baru">


                                    <div class="card-footer">
                                        <button id="submitPelayananBtn2" type="button" class="btn btn-primary float-end">Simpan Data
                                            Pelayanan</button>
                                        <button type="reset" class="btn btn-secondary float-start">Reset</button>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
                {{-- END SIMPLE --}}

                {{-- <div class="row tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab"> --}}
                <div class="row collapse search-collapse2" id="searchSection2">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title m-0 p-0"> <i class="bi bi-card-checklist"></i> Hasil Pencarian
                                    Pelayanan
                                    <button id="cetak-bukti-button" type="button" class="btn btn-warning btn-sm mx-1 float-end" data-bs-toggle="modal" data-bs-target="#ExtralargeModal" data-cetak_bukti_link="">
                                        Cetak Bukti
                                    </button>

                                    <a target="_blank" type="button" class="btn btn-primary btn-sm mx-1 float-end detail-button">Detail</a>

                                    {{-- <button id="upload_arsip_masuk" class="btn btn-secondary btn-sm mx-1 float-end" type="button" data-bs-toggle="modal" data-bs-target="#arsipModal" data-title="Edit Data Item Layanan">Upload Arsip</button> --}}
                                </h5>

                            </div>
                            <div class="card-body">

                                <form class="row g-3 mt-2" id="fForm" method="post" action="{{ route('daftar-pelayanan.store') }}" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <div class="col-md-6">
                                        <label for="search_no_registrasi" class="form-label fw-bold">No Registrasi</label>
                                        <input class="form-control" name="search_no_registrasi" id="search_no_registrasi" type="text" value="" placeholder="No Registrasi" disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="search_id_layanan" class="form-label fw-bold">Nama Layanan</label>
                                        <select name="search_id_layanan" id="search_id_layanan" class="form-control select2" disabled="true">
                                            <option selected="">Pilih Layanan</option>
                                            @foreach ($daftar_layanan as $layanan)
                                                <option value="{{ $layanan->id_layanan }}">{{ $layanan->unit->name . ' - ' . $layanan->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-12">
                                        <label for="search_pemohon_no_surat" class="form-label fw-bold">Perihal</label>
                                        <input class="form-control" name="search_perihal" id="search_perihal" type="text" value="" placeholder="Perihal" disabled>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="search_pemohon_no_surat" class="form-label fw-bold">No. Surat
                                            Permohonan</label>
                                        <input class="form-control" name="search_pemohon_no_surat" id="search_pemohon_no_surat" type="text" placeholder="Nomor Surat" value="" disabled>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="search_pemohon_tanggal_surat" class="form-label fw-bold">Tanggal Surat
                                            Permohonan</label>
                                        <input type="date" class="form-control" name="search_pemohon_tanggal_surat" id="search_pemohon_tanggal_surat" placeholder="Tanggal Surat" disabled>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="search_pemohon_nama" class="form-label fw-bold">Nama Pemohon</label>
                                        <input class="form-control" name="search_pemohon_nama" id="search_pemohon_nama" type="text" placeholder="Nama Pemohon" value="" disabled>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="search_pemohon_alamat" class="form-label fw-bold">Alamat
                                            Pemohon</label>
                                        <input class="form-control" name="search_pemohon_alamat" id="search_pemohon_alamat" type="text" placeholder="Alamat Pemohon" value="" disabled>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="search_pemohon_no_hp" class="form-label fw-bold">No. HP
                                            Pemohon</label>
                                        <input class="form-control" name="search_pemohon_no_hp" id="search_pemohon_no_hp" type="text" placeholder="Nomor HP Pemohon" value="" disabled>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="search_pengirim_nama" class="form-label fw-bold">Nama Pengirim</label>
                                        <input class="form-control" name="search_pengirim_nama" id="search_pengirim_nama" type="text" placeholder="Nama Pengirim" value="" disabled>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="search_kelengkapan_syarat" class="form-label fw-bold">Kelengkapan
                                            Syarat</label>
                                        <select name="search_kelengkapan_syarat" id="search_kelengkapan_syarat" class="form-control select2" disabled="true">
                                            <option selected="">-- Pilih Kelengkapan Syarat --</option>
                                            <option value="Sudah Lengkap">Sudah Lengkap</option>
                                            <option value="Belum Lengkap">Belum Lengkap</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="search_status_pelayanan" class="form-label fw-bold">Status
                                            Pelayanan</label>
                                        <select name="search_status_pelayanan" id="search_status_pelayanan" class="form-control select2" disabled="true">
                                            <option selected="">-- Pilih Status Layanan --</option>
                                            <option value="Baru">Baru</option>
                                            <option value="Proses">Proses</option>
                                            <option value="Selesai">Selesai</option>
                                            <option value="Ambil">Ambil</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label for="search_catatan" class="form-label fw-bold">Catatan</label>
                                        <textarea class="form-control" style="height: 100px" name="search_catatan" id="search_catatan" disabled="true"></textarea>
                                    </div>
                                    <!--  For multiple file uploads  -->
                                    <div class="col-12">
                                        <label for="dokumen_masuk" class="form-label fw-bold">Dokumen Pendukung</label>

                                        <div class="dokumen_masuk_box">

                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <label for="dokumen_keluar" class="form-label fw-bold">Hasil Pelayanan</label>

                                        <div class="dokumen_keluar_box">

                                        </div>
                                    </div>

                                    <div class="card-footer">
                                        <a target="_blank" type="button" class="btn btn-primary float-end detail-button">Detail</a>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

            {{-- </div> --}}
        </section>

        @include('admin.daftar-pelayanan._cmodal')

    </main>


@endsection


@section('_scripts')


    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/sweetalert2@11.js') }}"></script>


    <script type="text/javascript" language="javascript" src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
    <script type="text/javascript" language="javascript" src="{{ asset('js/dataTables.bootstrap5.min.js') }}"></script>
    <script type="text/javascript" language="javascript" src="{{ asset('js/dataTables.buttons.min.js') }}"></script>
    <script type="text/javascript" language="javascript" src="{{ asset('js/buttons.bootstrap5.min.js') }}"></script>
    <script type="text/javascript" language="javascript" src="{{ asset('js/jszip.min.js') }}"></script>
    <script type="text/javascript" language="javascript" src="{{ asset('js/pdfmake.min.js') }}"></script>
    <script type="text/javascript" language="javascript" src="{{ asset('js/vfs_fonts.js') }}"></script>
    <script type="text/javascript" language="javascript" src="{{ asset('js/buttons.html5.min.js') }}"></script>
    <script type="text/javascript" language="javascript" src="{{ asset('js/buttons.print.min.js') }}"></script>
    <script type="text/javascript" language="javascript" src="{{ asset('js/buttons.colVis.min.js') }}"></script>
    <script src="https://upload-widget.cloudinary.com/global/all.js" type="text/javascript"></script>

    {{-- File Pond --}}
    <script src="{{ asset('assets/js/filepond/filepond.js') }}"></script>
    <script src="{{ asset('assets/js/filepond/validate-filepond.js') }}"></script>


    <script>
        // Register the plugin
        FilePond.registerPlugin(FilePondPluginFileValidateType);

        // Get a reference to the file input element
        const inputElement = document.querySelector('input[name="data_file[]"]');
        // Create a FilePond instance
        const pond = FilePond.create(inputElement, {
            acceptedFileTypes: ['application/pdf'],
        });

        // const pond = FilePond.create(inputElement, {
        //     chunkUploads: true
        // });

        FilePond.setOptions({
            server: {
                process: {
                    url: '/upload-file/upload',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                },
                revert: {
                    url: '/upload-file/destroy/1',
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        '_method': 'DELETE'
                    }
                }
            },
            onaddfilestart: (file) => {
                isLoadingCheck();
            },
            onprocessfile: (files) => {
                isLoadingCheck();
            }
        });

        function isLoadingCheck() {
            var isLoading = pond.getFiles().filter(x => x.status !== 5).length !== 0;
            if (isLoading) {
                $('#submitPelayananBtn').attr("disabled", "disabled");
            } else {
                $('#submitPelayananBtn').removeAttr("disabled");
            }
        }




        // Global Variabel
        var id_pelayanan = null;
        var id_arsip = null;
        var arsip_masuk_url = null;
        var arsip_keluar_url = null;
        // Cloudinary Widget

        var widgetMasuk = cloudinary.createUploadWidget({
            cloudName: 'kemenagpessel',
            uploadPreset: 'arsip_masuk',
            theme: 'minimal',
            multiple: true,
            max_file_size: 10048576,
            background: "white"
        }, (error, result) => {
            if (!error && result && result.event === "success") {
                console.log('Info Arsip Masuk: ', result.info);
                var arsip_masuk_url = result.info.secure_url;
                console.log('arsip_masuk_url');
                console.log(arsip_masuk_url);
                $('#arsip_masuk_url').val(arsip_masuk_url);
                console.log('val');
                console.log($('#arsip_masuk_url').val());
                console.log('hide masuk');

                $('.upload_widget_opener_masuk').hide();
                $('.masuk-img').show();
                $('#arsip-masuk-src').attr("src", arsip_masuk_url);

            }
        });

        document.getElementById("upload_widget_opener_masuk").addEventListener("click", function() {
            widgetMasuk.open();
        }, false);



        var widgetKeluar = cloudinary.createUploadWidget({
            cloudName: 'kemenagpessel',
            uploadPreset: 'arsip_keluar',
            theme: 'minimal',
            multiple: true,
            max_file_size: 10048576,
            background: "white"
        }, (error, result) => {
            if (!error && result && result.event === "success") {
                console.log('Info Arsip Keluar: ', result.info);
                var arsip_keluar_url = result.info.secure_url;
                console.log('arsip_keluar_url');
                console.log(arsip_keluar_url);
                $('#arsip_keluar_url').val(arsip_keluar_url);
                console.log('val');
                console.log($('#arsip_keluar_url').val());
                console.log('hide keluar');

                $('.upload_widget_opener_keluar').hide();
                $('.keluar-img').show();
                $('#arsip-keluar-src').attr("src", arsip_keluar_url);
            }
        });

        var dataPelayanan = null;

        $(document).on("click", "#upload_arsip_masuk", function(e) {
            var title = $(this).data('title');
            $("#judul-modal").html('Upload Arsip Masuk');
            $('.arsip-masuk-box').show();
            $('.arsip-keluar-box').hide();
            console.log('dataPelayanan');
            console.log(dataPelayanan);
            var data = dataPelayanan;
            $('#arsip_id_pelayanan').val(data.id_pelayanan);
            $('#arsip_no_registrasi').val(data.no_registrasi);
            $('#arsip_id_layanan').val(data.id_layanan).trigger('change');
            $('#arsip_perihal').val(data.perihal);

        });


        $(document).on('click', '#searchButton', function(e) {
            e.preventDefault();


            // alert($('#search-pelayanan').val());

            if ($('#search-pelayanan').val()) {

                searchData($('#search-pelayanan').val());

            } else {
                Swal.fire(
                    'Perhatian!',
                    'Harap Ketik No. Registrasi untuk melacak Data',
                    'warning'
                );
            }

        });


        $("#copy-from-layanan").on("click", function() {
            var textLayanan = $('#id_layanan').find(":selected").text();
            $('#perihal').val(textLayanan.split("-").pop());
        });

        $("#copy-from-layanan2").on("click", function() {
            var textLayanan = $('#id_layanan2').find(":selected").text();
            var textPemohonNama = $('#pemohon_nama2').val();

            $('#perihal2').val(textLayanan.split("-").pop() + ' a.n. ' + textPemohonNama);
        });


        function searchData(id_pelayanan) {
            $('body').block({
                message: `Loading...`
            });

            setTimeout(function() {

                $.ajax({
                    type: 'GET',
                    url: '/daftar-pelayanan/fetch/' + id_pelayanan,
                    dataType: 'json', // let's set the expected response format
                    success: function(data) {
                        console.log('data');
                        console.log(data);
                        if (data.success) {
                            var item = data.data;
                            dataPelayanan = item;
                            $('#search_id_layanan').val(item.id_layanan).trigger('change');
                            $('#search_no_registrasi').val(item.no_registrasi);
                            $('#search_perihal').val(item.perihal);
                            $('#search_pemohon_no_surat').val(item.pemohon_no_surat);
                            $('#search_pemohon_tanggal_surat').val(item
                                .pemohon_tanggal_surat);
                            $('#search_pemohon_nama').val(item.pemohon_nama);
                            $('#search_pemohon_alamat').val(item.pemohon_alamat);
                            $('#search_pemohon_no_hp').val(item.pemohon_no_hp);
                            $('#search_pengirim_nama').val(item.pengirim_nama);
                            $('#search_kelengkapan_syarat').val(item.kelengkapan_syarat)
                                .trigger('change');
                            $('#search_status_pelayanan').val(item.status_pelayanan)
                                .trigger(
                                    'change');
                            $('#search_catatan').val(item.catatan);

                            $('.detail-button').attr('href', data.url_detail);

                            $('#cetak-bukti-button').attr('data-cetak_bukti_link', data.url_pdf)
                            $('#cetak-bukti-button').data('cetak_bukti_link', data.url_pdf);

                            $(document).on("click", "#cetak-bukti-button", function() {
                                // $("#cetak-bukti-button").on("click", function() {
                                var cetakBuktiLink = $(this).data('cetak_bukti_link');
                                console.log('cetakBuktiLink');
                                console.log(cetakBuktiLink);
                                $('#cetak-bukti-link').attr('src', cetakBuktiLink);
                                var iFrame = $('#cetak-bukti-link');
                                iFrame.load(cetakBuktiLink);
                                document.getElementById('cetak-bukti-link').contentDocument.location.reload(true);
                                $('#cetak-bukti-link').attr("src", $('#cetak-bukti-link').attr("src"));
                                document.getElementById('cetak-bukti-link').src = document.getElementById('cetak-bukti-link').src

                                console.log('cetakBuktiLink Finished');
                            });

                            if (item.arsip) {
                                if (item.arsip.arsip_masuk_url) {
                                    $('#upload_arsip_masuk').prop('disabled', true);
                                    $('#upload_arsip_masuk').props('disabled', true);
                                }
                            }

                            // $('[name=dokumen_masuk_url]').val(item.arsip.dokumen_masuk_url);

                            var boxmasuk = $('.dokumen_masuk_box');
                            boxmasuk.empty();
                            var htmlmasuk = '';
                            if (item.arsip.dokumen_masuk_url) {
                                $.each(item.arsip.dokumen_masuk_url, function(key, item) {
                                    htmlmasuk += `<div class="btn btn-outline-dark mx-2 my-1 text-start">
                                                    <u>
                                                        <a id="string_url" target="_blank" href="${item.file_url}" style="font-size:smaller;">
                                                            ${item.filename}
                                                        </a>
                                                    </u>
                                                </div>`
                                });

                                boxmasuk.append(htmlmasuk);
                            } else {
                                boxmasuk.append('-');

                            }

                            var boxkeluar = $('.dokumen_keluar_box');
                            boxkeluar.empty();
                            var htmlkeluar = '';
                            if (item.arsip.dokumen_keluar_url) {
                                $.each(item.arsip.dokumen_keluar_url, function(key, item) {
                                    htmlkeluar += `<div class="btn btn-outline-dark mx-2 my-1 text-start">
                                                    <u>
                                                        <a id="string_url" target="_blank" href="${item.file_url}" style="font-size:smaller;">
                                                            ${item.filename}
                                                        </a>
                                                    </u>
                                                </div>`
                                });

                                boxkeluar.append(htmlkeluar);
                            } else {
                                boxkeluar.append('-');
                            }



                        } else {
                            Swal.fire(
                                'Error!', data.message, 'error'
                            );
                        }
                        $('#fForm')[0].reset();
                        $('#id_layanan').val('');

                    },
                    error: function(err) {
                        if (err.status ==
                            422) { // when status code is 422, it's a validation issue
                            console.log(err.responseJSON);
                            // you can loop through the errors object and show it to the user
                            console.warn(err.responseJSON.errors);
                            // display errors on each form field
                            $('.ajax-invalid').remove();
                            $.each(err.responseJSON.errors, function(i, error) {
                                var el = $(document).find('[name="' + i + '"]');
                                el.after($('<span class="ajax-invalid" style="color: red;">' +
                                    error[0] + '</span>'));
                            });
                        } else if (err.status == 403) {
                            Swal.fire(
                                'Unauthorized!',
                                'You are unauthorized to do the action',
                                'warning'
                            );

                        }
                    }
                });



                $('body').unblock();
                $('#inputSection').removeClass('show');
                $('#searchSection2').fadeIn("slow");
            }, 1500);
        }

        $(document).on('click', '#inputButton', function(e) {
            e.preventDefault();

            $('#inputSection').fadeIn("slow");
            $('#searchSection2').hide();
            $('#inputSimpleSection').hide();

        });

        $(document).on('click', '#simpleButton', function(e) {
            e.preventDefault();

            $('#inputSimpleSection').fadeIn("slow");
            $('#searchSection2').hide();
            $('#inputSection').hide();

        })


        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.select2').select2({
            theme: 'bootstrap-5',
        });

        $('.select2-search').select2({
            theme: 'bootstrap-5',
            ajax: {
                url: "/daftar-pelayanan/search",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function(data, params) {
                    console.log(data);
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: item.no_registrasi,
                                id: item.id_pelayanan,
                            }
                        })
                    };
                },
                cache: true
            },
            placeholder: 'Ketik No. Registrasi',
            minimumInputLength: 5,
        });





        $(document).ready(function() {

            // $(document).on("click", "#cetak-bukti-button", function() {
            //     var cetakBuktiLink = $(this).data('cetak_bukti_link');
            //     console.log('cetakBuktiLink');
            //     console.log(cetakBuktiLink);
            //     $('#cetak-bukti-link').attr('src', cetakBuktiLink);
            //     var iFrame = $('#cetak-bukti-link');
            //     iFrame.load(cetakBuktiLink);
            // });

            $(document).on("click", "#addBtn", function() {
                $('.edit-state').hide();
                $('#id_layanan').val('');
                $('#fForm')[0].reset();
                var title = $(this).data('title');
                $("#judul-modal").html(title);
                $('#nama').prop('disabled', false);
            });

            $(document).on("click", "#editBtn", function() {
                $('.edit-state').show();
                var title = $(this).data('title');
                $("#judul-modal").html(title);
                var data = table.row($(this).parents('tr')).data();
                console.log(data);
                console.log(title);
                $("#id_layanan").val(data.id_layanan);
                $('#name').val(data.name);

                $('#id_unit_pengolah').val(data.id_unit_pengolah).trigger('change');
                $('#id_jenis_layanan').val(data.id_jenis_layanan).trigger('change');
                $('#id_output_layanan').val(data.id_output_layanan).trigger('change');
                $('#lama_layanan').val(data.lama_layanan);
                $('#biaya_layanan').val(data.biaya_layanan);

            });

            $(document).on('select2:select', '#id_layanan', function(e) {
                var id_layanan = $(this).val();
                fetchSyarat(id_layanan);
            });


            function fetchSyarat(id_layanan) {

                $('.template-syarat').block({
                    message: '',
                });

                setTimeout(function() {
                    $.ajax({
                        url: '/syarat-layanan/list/fetch/' + id_layanan,
                        method: 'get'
                    }).done(function(res) {
                        console.log('res');
                        console.log(res);


                        var htmlData = '';

                        if (res.data.length > 0) {
                            htmlData += `<ol>`;
                            $.each(res.data, function(key, item) {
                                htmlData += `<li>${item.name}</li>`
                            });
                            htmlData += `</ol>`;
                        } else {
                            htmlData += '.:: Belum ada Data Syarat ::.';
                        }




                        $('#message-syarat').html(htmlData);
                        $('.template-syarat').fadeIn("slow");
                        $('.template-syarat').unblock();
                    });

                }, 300)
            }


            $(".custom-select").on("select2:close", function(e) {
                $(this).valid();
                console.log($(this).val());
                console.log($(this).valid());
            });


            jQuery.validator.addMethod("phoneID", function(value, element) {
                return value.match(/\(?([0-9]{3})\)?([ .-]?)([0-9]{3})\2([0-9]{4})/);
            }, "Masukkan Nomor HP yang valid!");


            $('#fForm').validate({
                lang: 'id', // or whatever language option you have.
                ignore: [],
                rules: {
                    id_layanan: {
                        required: true,
                    },
                    perihal: {
                        required: true,
                    },
                    pemohon_no_surat: {
                        required: true,
                    },
                    pemohon_tanggal_surat: {
                        required: true,
                    },
                    pemohon_nama: {
                        required: true,
                    },
                    pemohon_alamat: {
                        required: true,
                    },
                    pemohon_no_hp: {
                        required: true,
                        phoneID: true
                    },
                    pengirim_nama: {
                        required: true,
                    },
                    kelengkapan_syarat: {
                        required: true,
                    },
                },
                messages: {
                    email: {
                        required: "Please enter a valid email address",
                        minlength: "Please enter a valid email address",
                        email: "Please enter a valid email address",
                        remote: "This email is already registered"
                    },
                    username: {
                        required: "Please enter a valid username",
                        remote: "This username is already registered"
                    },
                    jenis_usaha: "Jenis Usaha Harap diisi",
                    id_kabkota: "Kota / Kabupaten Harap diisi",
                    nama: "Nama Harap diisi"
                },
                errorElement: 'div',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                }
            });

            $('#sForm').validate({
                lang: 'id', // or whatever language option you have.
                ignore: [],
                rules: {
                    id_layanan2: {
                        required: true,
                    },
                    perihal2: {
                        required: true,
                    },
                    pemohon_nama2: {
                        required: true,
                    },
                    pemohon_no_hp2: {
                        required: true,
                        phoneID: true
                    },
                },
                messages: {
                    email: {
                        required: "Please enter a valid email address",
                        minlength: "Please enter a valid email address",
                        email: "Please enter a valid email address",
                        remote: "This email is already registered"
                    },
                    username: {
                        required: "Please enter a valid username",
                        remote: "This username is already registered"
                    },
                    jenis_usaha: "Jenis Usaha Harap diisi",
                    id_kabkota: "Kota / Kabupaten Harap diisi",
                    nama: "Nama Harap diisi"
                },
                errorElement: 'div',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                }
            });

            $("#submitPelayananBtn").on("click", function(event) {
                event.preventDefault();
                console.log($("#fForm").valid());

                if ($("#fForm").valid()) {

                    $('#fForm').block({
                        message: `Loading...`
                    });

                    setTimeout(function() {

                        var formdata = $("#fForm")
                            .serialize(); // here $(this) refere to the form its submitting
                        console.log(formdata);
                        url = $('#fForm').attr('action');


                        $.ajax({
                            type: 'POST',
                            url: url,
                            data: formdata, // here $(this) refers to the ajax object not form
                            dataType: 'json', // let's set the expected response format
                            success: function(data) {

                                console.log(data);
                                if (data.success) {
                                    Swal.fire(
                                        'Mantap!',
                                        'Data Pelayanan berhasil disimpan!',
                                        'success'
                                    );

                                    // if (!(typeof socket === "undefined")) {
                                    socket.emit('sendSummaryToServer', data.summary);
                                    var notifData = [
                                        data.recipient, data.disposisi
                                    ];
                                    socket.emit('sendNotifToServer', notifData);
                                    // }

                                    searchData(data.data.id_pelayanan);

                                } else {
                                    Swal.fire(
                                        'Perhatian!', data.message,
                                        'warning'
                                    );
                                }

                            },
                            error: function(err) {
                                if (err.status ==
                                    422
                                ) { // when status code is 422, it's a validation issue
                                    console.log(err.responseJSON);
                                    // you can loop through the errors object and show it to the user
                                    console.warn(err.responseJSON.errors);
                                    // display errors on each form field
                                    $('.ajax-invalid').remove();
                                    $.each(err.responseJSON.errors,
                                        function(i, error) {
                                            var el = $(document).find(
                                                '[name="' +
                                                i + '"]');
                                            el.after($('<span class="ajax-invalid" style="color: red;">' +
                                                error[0] +
                                                '</span>'));
                                        });
                                } else if (err.status == 403) {
                                    Swal.fire(
                                        'Unauthorized!',
                                        'You are unauthorized to do the action',
                                        'warning'
                                    );
                                }
                            }
                        });

                        $('#fForm').unblock();
                        $('#fForm')[0].reset();
                        $('#id_layanan').val('').trigger('change');
                        $('#inputSection').hide();
                        $('#inputSection').removeClass('show');

                    }, 1500);
                }
            });

            $("#submitPelayananBtn2").on("click", function(event) {
                event.preventDefault();
                console.log($("#sForm").valid());

                if ($("#sForm").valid()) {

                    $('#sForm').block({
                        message: `Loading...`
                    });

                    setTimeout(function() {

                        var formdata = $("#sForm")
                            .serialize(); // here $(this) refere to the form its submitting
                        console.log(formdata);
                        url = $('#sForm').attr('action');


                        $.ajax({
                            type: 'POST',
                            url: url,
                            data: formdata, // here $(this) refers to the ajax object not form
                            dataType: 'json', // let's set the expected response format
                            success: function(data) {

                                console.log('data');
                                console.log('=');
                                console.log(data);
                                console.log('=');
                                console.log('data');


                                if (data.success) {
                                    Swal.fire(
                                        'Mantap!',
                                        'Data Pelayanan berhasil disimpan!',
                                        'success'
                                    );
                                    searchData(data.data.id_pelayanan);

                                    // if (!(typeof socket === "undefined")) {
                                    socket.emit('sendSummaryToServer', data.summary);
                                    var notifData = [
                                        data.recipient, data.disposisi
                                    ];
                                    socket.emit('sendNotifToServer', notifData);
                                    // }


                                } else {
                                    Swal.fire(
                                        'Perhatian!', data.message,
                                        'warning'
                                    );
                                }

                            },
                            error: function(err) {
                                if (err.status ==
                                    422
                                ) { // when status code is 422, it's a validation issue
                                    console.log(err.responseJSON);
                                    // you can loop through the errors object and show it to the user
                                    console.warn(err.responseJSON.errors);
                                    // display errors on each form field
                                    $('.ajax-invalid').remove();
                                    $.each(err.responseJSON.errors,
                                        function(i, error) {
                                            var el = $(document).find(
                                                '[name="' +
                                                i + '"]');
                                            el.after($('<span class="ajax-invalid" style="color: red;">' +
                                                error[0] +
                                                '</span>'));
                                        });
                                } else if (err.status == 403) {
                                    Swal.fire(
                                        'Unauthorized!',
                                        'You are unauthorized to do the action',
                                        'warning'
                                    );
                                }
                            }
                        });

                        $('#sForm').unblock();
                        $('#sForm')[0].reset();
                        $('#id_layanan2').val('').trigger('change');
                        $('#inputSimpleSection').hide();
                        $('#inputSimpleSection').removeClass('show');

                    }, 1500);
                }
            });

            $("#submitBtn").on("click", function(event) {
                event.preventDefault();

                $('.modalBox').block({
                    message: null
                });

                $('#submitBtn').prop("disabled", true);

                var formdata = $("#arsipForm")
                    .serialize(); // here $(this) refere to the form its submitting
                console.log(formdata);

                url = $('#arsipForm').attr('action');
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: formdata, // here $(this) refers to the ajax object not form
                    dataType: 'json', // let's set the expected response format
                    success: function(data) {
                        setTimeout(function() {
                            $('#submitBtn').prop("disabled", false);
                            $('.modalBox').unblock();
                            console.log(data);
                            if (data.success) {
                                $('#fModal').modal('hide');
                                searchData(dataPelayanan.id_pelayanan);
                                Swal.fire(
                                    'Great!', 'Data sukses di update!', 'success'
                                );
                                // if (!(typeof socket === "undefined")) {
                                socket.emit('sendSummaryToServer', data.summary);
                                // }
                            } else {
                                Swal.fire(
                                    'Error!', data.message, 'error'
                                );
                            }
                            $('#fForm')[0].reset();
                            $('#id_jenis_layanan').val('');
                        }, 200);

                    },
                    error: function(err) {
                        if (err.status ==
                            422) { // when status code is 422, it's a validation issue
                            console.log(err.responseJSON);
                            // you can loop through the errors object and show it to the user
                            console.warn(err.responseJSON.errors);
                            // display errors on each form field
                            $('.ajax-invalid').remove();
                            $.each(err.responseJSON.errors, function(i, error) {
                                var el = $(document).find('[name="' + i + '"]');
                                el.after($('<span class="ajax-invalid" style="color: red;">' +
                                    error[0] + '</span>'));
                            });
                        } else if (err.status == 403) {
                            Swal.fire(
                                'Unauthorized!', 'You are unauthorized to do the action',
                                'warning'
                            );

                        }
                    }
                });
            });

        });
    </script>
@endsection
