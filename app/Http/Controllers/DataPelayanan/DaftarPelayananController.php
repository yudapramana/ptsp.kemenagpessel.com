<?php

namespace App\Http\Controllers\DataPelayanan;

use Notification;
use App\Notifications\NewPelayananNotification;

use App\Models\TemporaryFile;
use App\Http\Controllers\Controller;
use App\Models\DaftarDisposisi;
use Illuminate\Http\Request;
use App\Models\DaftarPelayanan;
use App\Models\DaftarLayanan;
use App\Models\JenisLayanan;
use App\Models\MasterAksiDisposisi;
use App\Models\OutputLayanan;
use App\Models\UnitPengolah;
use App\Models\User;
use App\Notifications\NewUserNotification;
use DataTables;
use Auth;
use DB;
use Vinkla\Hashids\Facades\Hashids;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Http;
use Config;

class DaftarPelayananController extends Controller
{
    public function index(Request $request, $status)
    {
        if ($request->ajax()) {
            $idUnit = $request->id_unit_pengolah_filter;
            $idLayanan = $request->id_layanan_filter;
            $query = new DaftarPelayanan();
            if ($status != 'Semua') {
                $query = $query->where('status_pelayanan', $status);
            }

            if ($idUnit != 0) {
                $query = $query->where('id_unit_pengolah', $idUnit);
            }

            if ($idLayanan != 0) {
                $query = $query->where('id_layanan', $idLayanan);
            }

            $query = $query->whereYear('created_at', date("Y"));

            $query = $query->with('layanan', 'unit', 'output', 'jenis')->orderBy('id_pelayanan', 'desc');

            if ($status == 'Semua' || $status == 'semua') {
                $pelayanans = $query->get();
            } else {
                $access_type = DB::table('access_type')->first();
                $type = $access_type->name;

                switch ($type) {
                    case 'ZERO':
                        $pelayanans = collect([]);
                        break;

                    case 'MINIMAL':
                        $pelayanans = $query->take(1)->get();
                        break;

                    case 'STANDARD':
                        $pelayanans = $query->take(500)->get();
                        break;

                    default:
                        $pelayanans = $query->take(500)->get();
                        break;
                }
            }



            $datatable = Datatables::of($pelayanans)
                ->addIndexColumn()
                ->addColumn('action', function ($layanan) {
                    $url = route('daftar-pelayanan.detail', Hashids::encode($layanan->id_pelayanan));
                    $urlPDF = route('pdf.create', Hashids::encode($layanan->id_pelayanan));
                    // $btn = '<a href="'.$urlPDF.'" target="_blank" id="pdfBtn" type="button" class="btn btn-sm btn-secondary btn-xs"><i class="bi bi-printer"></i></a>';
                    $btn = '<button id="cetak-bukti-button" type="button" class="btn btn-secondary btn-sm mx-1" data-bs-toggle="modal" data-bs-target="#ExtralargeModal" data-cetak_bukti_link="'.$urlPDF.'"><i class="bi bi-printer"></i></button>';
                    $btn .= '<a href="'.$url.'" target="_blank" id="viewBtn" type="button" class="btn btn-sm btn-primary btn-xs mx-1"><i class="bi bi-journal-check"></i></a>';
                    $user = Auth::user();
                    if ($user->hasRole('super_administrator')) {
                        $btn .= '<button id="destroyBtn" type="button" class="btn btn-sm btn-danger btn-xs mx-1" data-bs-id_pelayanan="'. $layanan->id_pelayanan  .'" data-id_pelayanan="'.  $layanan->id_pelayanan  .'"><i class="bi bi-trash-fill"></i></button>';
                    }
                    // || $user->hasRole('operator')
                    if ($user->hasRole('super_administrator') || $user->username == 'mardiyana') {
                        $btn .= '<button id="editBtn" type="button" class="btn btn-sm btn-warning btn-xs mx-1" data-bs-toggle="modal" data-bs-target="#fModal" data-title="Edit Data Item Layanan"><i class="bi bi-pencil-square"></i></button>';
                    }
                    return $btn;
                })
                // ->addColumn('pelayanan_perihal', function ($layanan) {
                //     $html = '';
                //     $html .= '<span>'.$layanan->perihal.  '</span><br>';
                //     $html .= '<span class="text-muted" style="font-size:smaller!important;">Oleh: '.$layanan->pemohon_nama.  '</span><br>';
                //     $html .= '<span class="text-muted" style="font-size:smaller!important;">Alamat: '.$layanan->pemohon_alamat.  '</span><br>';
                //     return $html;
                // })
                ->addColumn('pelayanan_perihal', function ($layanan) {
                    $html = '';
                    $html .= '<span>'.$layanan->perihal.  '</span><br>';
                    return $html;
                })
                ->editColumn('created_at', function ($layanan) {
                    return $layanan->created_at->format('Y-m-d H:i:s');
                })
                ->rawColumns(['action', 'pelayanan_perihal']);


            $units = UnitPengolah::all();

            $html_filter = '<div class="col-md-6">
                                <label for="unit_pengolah" class="form-label fw-bold">Unit Pengolah</label>

                                <select class="form-control select2-filter id_unit_pengolah_filter" id="id_unit_pengolah_filter">
                                    <option value="0">Semua Unit Pengolah</option>';
            foreach ($units as $key => $item) {
                $html_filter .= '<option value="' . $item->id_unit_pengolah . '">' . $item->name . '</option>';
            }
            $html_filter .= '  </select>

                            </div>';

            $html_filter .= '<div class="col-md-6 box-daftar-pelayanan">
                                <label for="id_layanan_filter" class="form-label fw-bold">Daftar Pelayanan</label>

                                <select class="form-control select2-filter id_layanan_filter" id="id_layanan_filter">
                                    <option value="0">Semua Pelayanan</option>';

            $html_filter .= '  </select>

                            </div>';


            $datatable->with([
                'html_filter' => $html_filter
            ]);

            return $datatable->make(true);
        }

        $jenis_all = JenisLayanan::all();
        $unit_all = UnitPengolah::all();
        $output_all = OutputLayanan::all();
        $daftarLayanan = DaftarLayanan::with('unit')->get();


        $dd = [
            'jenis_all' => $jenis_all,
            'unit_all' => $unit_all,
            'output_all' => $output_all
        ];

        return view('admin.daftar-pelayanan.index', [
            'title'  => 'Daftar Pelayanan Publik',
            'br1'  => 'Kelola',
            'br2'  => 'Daftar Pelayanan Publik',
            'br3'  => $status,
            'status'  => $status,
            'dd'   => $dd,
            'html_status' => '<span class="html-status">'.ucfirst($status).'</span>',
            'daftar_layanan'  => $daftarLayanan
        ])->render();
    }

    public function create()
    {
        $access_type = DB::table('access_type')->first();
        $type = $access_type->name;
        $daftarLayanan = \App\Models\DaftarLayanan::all();
        
        // $daftarLayanan = DaftarLayanan::whereHas('syarat')->get();
        return view('admin.daftar-pelayanan.create', [
            'title'  => 'Input - Lacak Pelayanan Publik',
            'type' => $type,
            'br1'  => 'Input - Lacak',
            'br2'  => 'Daftar Pelayanan Publik',
            'daftar_layanan'  => $daftarLayanan
        ])->render();
    }

    public function store(Request $request)
    {
        $success = false;
        $message = '';
        $code = 400;

        $data = $request->input();
        $newData = null;
        $summary = [];
        $recipient = null;
        $disposisi = null;

        try {
            config(['app.locale' => 'id']);
            Carbon::setLocale('id');
            date_default_timezone_set('Asia/Jakarta');

            $layanan = DaftarLayanan::where('id_layanan', $data['id_layanan'])->first();
            $pelayananCount = DaftarPelayanan::whereYear('created_at', '=', date('Y'))
                                                ->whereMonth('created_at', '=', date('m'))
                                                ->count();

            $pelayananCount = $pelayananCount == 0 ? 1 : ($pelayananCount + 1);

            // Create Pelayanan
            $pelayanan = new DaftarPelayanan();
            $pelayanan->id_layanan = $data['id_layanan'];
            $pelayanan->no_registrasi = "02".date('ymd').sprintf('%02d', $layanan->id_unit_pengolah).sprintf('%02d', $data['id_layanan']).sprintf('%03d', $pelayananCount);
            $pelayanan->perihal = $data['perihal'];
            $pelayanan->pemohon_nama = $data['pemohon_nama'];
            $pelayanan->pemohon_no_surat = $data['pemohon_no_surat'];
            $pelayanan->pemohon_tanggal_surat = $data['pemohon_tanggal_surat'];
            $pelayanan->pengirim_nama = $data['pengirim_nama'];
            $pelayanan->pemohon_alamat = $data['pemohon_alamat'];
            $pelayanan->pemohon_no_hp = $data['pemohon_no_hp'];
            $pelayanan->kelengkapan_syarat = $data['kelengkapan_syarat'];
            $pelayanan->status_pelayanan = $data['status_pelayanan'];
            $pelayanan->catatan = $data['catatan'];
            $pelayanan->created_by = Auth::user()->name;
            $pelayanan->id_unit_pengolah = $layanan->id_unit_pengolah;

            $pelayanan->save();
            $pelayanan->fresh();

            // Buat Arsip
            $arsip = new \App\Models\DaftarArsip();
            $arsip->id_pelayanan = $pelayanan->id_pelayanan;
            $arsip->save();
            $arsip->fresh();

            if ($data['status_pelayanan'] == 'Baru') {
                // Create Disposisi
                $disposisi = new DaftarDisposisi();
                $disposisi->id_pelayanan = $pelayanan->id_pelayanan;
                $disposisi->id_aksi_disposisi = 2; // aksi 'mohon_arahan'
                $disposisi->urutan_disposisi = 1;
                $disposisi->id_sender = Auth::user()->id;
                $disposisi->username_sender = Auth::user()->username;
                $recipient = \App\Models\User::whereHas('roles', function ($q) {
                    $q->where('name', 'manager');
                })->first();
                $disposisi->id_recipient = $recipient->id;
                $disposisi->username_recipient = $recipient->username;
                $disposisi->save();
                $disposisi->fresh();
                $disposisi->load('pelayanan');

                Notification::send($recipient, new NewPelayananNotification($disposisi));


                // Send Message
                $detailUrl = route('daftar-pelayanan.detail', $pelayanan->idx_pelayanan);
                $text = '```Yth, \n';
                $text .= '' . $recipient->name . ' \n';
                $text .= 'Ada Disposisi Baru \n \n';
                $text .= '==========================\n';
                $text .= 'No. Reg : '. $pelayanan->no_registrasi.'\n';
                $text .= 'Perihal : '. $pelayanan->perihal .'\n';
                $text .= 'Pemohon : '. $pelayanan->pemohon_nama .'\n';
                $text .= 'Alamat  : '. $pelayanan->pemohon_alamat .'\n';
                $text .= '==========================';
                $text .= '\n \n';
                $text .= 'Rincian Pelayanan dapat dilihat pada link dibawah ``` \n \n';
                $text .= '' . $detailUrl . '';

                // MessageController::sendMessage('6282298476941', $text);

                event(new \App\Events\DispositionProcessed($pelayanan, $recipient));
            }

            $newData = $pelayanan;

            $summary = DaftarPelayanan::select('status_pelayanan', DB::raw('count(*) as total'))
                                                ->whereYear('created_at', '=', date('Y'))
                                                ->whereMonth('created_at', '=', date('m'))
                                                ->groupBy('status_pelayanan')
                                                ->get();

            // Save File
            $files = $request->data_file;
            $urlAssets = [];
            foreach ($files as $file) {
                $dcd = json_decode($file);
                $file = $dcd[0];
                $tempFile = TemporaryFile::where('folder', $file)->first();


                if ($tempFile) {
                    $sourcePath = storage_path('app/public/temporary/' . $tempFile->folder);
                    $sourceFile = $sourcePath . '/' . $tempFile->filename;
                    $ext = pathinfo($sourceFile, PATHINFO_EXTENSION);



                    $destinationPath =  storage_path('app/public/files/' . date('Y-m') . '/' . $pelayanan->no_registrasi);
                    $destinationFile = $destinationPath . '/' . $tempFile->filename;
                    $asset = 'storage/files/' . date('Y-m') . '/' . $pelayanan->no_registrasi . '/' . $tempFile->filename;

                    if (!Storage::exists($destinationPath)) {
                        Storage::makeDirectory($destinationPath, 0777, true); //creates directory
                    }

                    File::ensureDirectoryExists($destinationPath);
                    File::move($sourceFile, $destinationFile);
                    // Storage::move( $sourceFile, $destinationFile );

                    // Delete File and Database
                    $this->rmdir_recursive($sourcePath);
                    $tempFile->delete();


                    // getAsset
                    // $urlAssets[] = asset($asset);
                    $urlAssets[] = [
                        'filename' => $tempFile->filename,
                        'file_url' => asset($asset),
                    ];
                }
            }

            $arsip->dokumen_masuk_url = $urlAssets;
            $arsip->save();

            $success = true;
            $code = 200;
            $message = 'Data Berhasil Disimpan';
        } catch (\Throwable $th) {
            $message = $th->getMessage();
        }

        return response()
            ->json([
                'success' => $success,
                'message' => $message,
                'data' => $newData,
                'summary' => $summary,
                'recipient' => $recipient,
                'disposisi' => $disposisi
            ]);
    }

    public function storeSimple(Request $request)
    {
        $success = false;
        $message = '';
        $code = 400;

        $data = $request->input();
        $newData = null;
        $summary = [];
        $recipient = null;
        $disposisi = null;

        try {
            config(['app.locale' => 'id']);
            Carbon::setLocale('id');
            date_default_timezone_set('Asia/Jakarta');

            $layanan = DaftarLayanan::where('id_layanan', $data['id_layanan2'])->first();
            $pelayananCount = DaftarPelayanan::whereYear('created_at', '=', date('Y'))
                                                ->whereMonth('created_at', '=', date('m'))
                                                ->count();

            $pelayananCount = $pelayananCount == 0 ? 1 : ($pelayananCount + 1);

            // Create Pelayanan
            $pelayanan = new DaftarPelayanan();
            $pelayanan->id_layanan = $data['id_layanan2'];
            $pelayanan->no_registrasi = "02".date('ymd').sprintf('%02d', $layanan->id_unit_pengolah).sprintf('%02d', $data['id_layanan2']).sprintf('%03d', $pelayananCount);
            $pelayanan->perihal = $data['perihal2'];
            $pelayanan->pemohon_nama = $data['pemohon_nama2'];
            $pelayanan->pemohon_no_hp = $data['pemohon_no_hp2'];
            $pelayanan->kelengkapan_syarat = $data['kelengkapan_syarat'];
            $pelayanan->status_pelayanan = $data['status_pelayanan'];
            $pelayanan->created_by = Auth::user()->name;
            $pelayanan->id_unit_pengolah = $layanan->id_unit_pengolah;

            $pelayanan->save();
            $pelayanan->fresh();

            // Buat Arsip
            $arsip = new \App\Models\DaftarArsip();
            $arsip->id_pelayanan = $pelayanan->id_pelayanan;
            $arsip->save();
            $arsip->fresh();

            if ($data['status_pelayanan'] == 'Baru') {
                // Create Disposisi
                $disposisi = new DaftarDisposisi();
                $disposisi->id_pelayanan = $pelayanan->id_pelayanan;
                $disposisi->id_aksi_disposisi = 2; // aksi 'mohon_arahan'
                $disposisi->urutan_disposisi = 1;
                $disposisi->id_sender = Auth::user()->id;
                $disposisi->username_sender = Auth::user()->username;
                $recipient = \App\Models\User::whereHas('roles', function ($q) {
                    $q->where('name', 'manager');
                })->first();
                $disposisi->id_recipient = $recipient->id;
                $disposisi->username_recipient = $recipient->username;
                $disposisi->save();
                $disposisi->fresh();
                $disposisi->load('pelayanan');

                Notification::send($recipient, new NewPelayananNotification($disposisi));


                // Send Message
                $detailUrl = route('daftar-pelayanan.detail', $pelayanan->idx_pelayanan);
                $text = '```Yth, \n';
                $text .= '' . $recipient->name . ' \n';
                $text .= 'Ada Disposisi Baru \n \n';
                $text .= '==========================\n';
                $text .= 'No. Reg : '. $pelayanan->no_registrasi.'\n';
                $text .= 'Perihal : '. $pelayanan->perihal .'\n';
                $text .= 'Pemohon : '. $pelayanan->pemohon_nama .'\n';
                $text .= 'Alamat  : '. $pelayanan->pemohon_alamat .'\n';
                $text .= '==========================';
                $text .= '\n \n';
                $text .= 'Rincian Pelayanan dapat dilihat pada link dibawah ``` \n \n';
                $text .= '' . $detailUrl . '';

                // MessageController::sendMessage('6282298476941', $text);

                event(new \App\Events\DispositionProcessed($pelayanan, $recipient));
            }

            $newData = $pelayanan;

            $summary = DaftarPelayanan::select('status_pelayanan', DB::raw('count(*) as total'))
                                                ->whereYear('created_at', '=', date('Y'))
                                                ->whereMonth('created_at', '=', date('m'))
                                                ->groupBy('status_pelayanan')
                                                ->get();

            // Save File
            $files = $request->data_file;
            $urlAssets = [];
            if($files) {
                foreach ($files as $file) {
                    $dcd = json_decode($file);
                    $file = $dcd[0];
                    $tempFile = TemporaryFile::where('folder', $file)->first();


                    if ($tempFile) {
                        $sourcePath = storage_path('app/public/temporary/' . $tempFile->folder);
                        $sourceFile = $sourcePath . '/' . $tempFile->filename;
                        $ext = pathinfo($sourceFile, PATHINFO_EXTENSION);



                        $destinationPath =  storage_path('app/public/files/' . date('Y-m') . '/' . $pelayanan->no_registrasi);
                        $destinationFile = $destinationPath . '/' . $tempFile->filename;
                        $asset = 'storage/files/' . date('Y-m') . '/' . $pelayanan->no_registrasi . '/' . $tempFile->filename;

                        if (!Storage::exists($destinationPath)) {
                            Storage::makeDirectory($destinationPath, 0777, true); //creates directory
                        }

                        File::ensureDirectoryExists($destinationPath);
                        File::move($sourceFile, $destinationFile);
                        // Storage::move( $sourceFile, $destinationFile );

                        // Delete File and Database
                        $this->rmdir_recursive($sourcePath);
                        $tempFile->delete();


                        // getAsset
                        // $urlAssets[] = asset($asset);
                        $urlAssets[] = [
                            'filename' => $tempFile->filename,
                            'file_url' => asset($asset),
                        ];
                    }
                }
            }

            $arsip->dokumen_masuk_url = $urlAssets;
            $arsip->save();

            $success = true;
            $code = 200;
            $message = 'Data Berhasil Disimpan';
        } catch (\Throwable $th) {
            $message = $th->getMessage();
        }

        return response()
            ->json([
                'success' => $success,
                'message' => $message,
                'data' => $newData,
                'summary' => $summary,
                'recipient' => $recipient,
                'disposisi' => $disposisi
            ]);
    }

    public function rmdir_recursive($dir)
    {
        foreach (scandir($dir) as $file) {
            if ('.' === $file || '..' === $file) {
                continue;
            }
            if (is_dir("$dir/$file")) {
                $this->rmdir_recursive("$dir/$file");
            } else {
                unlink("$dir/$file");
            }
        }
        rmdir($dir);
    }

    public function update(Request $request)
    {
        $success = false;
        $message = '';
        $code = 400;

        $data = $request->input();
        $newData = null;
        $summary = [];

        try {
            $pelayanan = DaftarPelayanan::where('id_pelayanan', $data['id_pelayanan'])->first();

            // Create Pelayanan
            $pelayanan->id_layanan = $data['id_layanan'];
            $layanan = DaftarLayanan::find($data['id_layanan']);
            $pelayanan->id_unit_pengolah = $layanan->id_unit_pengolah;
            $pelayanan->id_jenis_layanan = $layanan->id_jenis_layanan;
            $pelayanan->id_output_layanan = $layanan->id_output_layanan;
            $pelayanan->perihal = $data['perihal'];
            $pelayanan->pemohon_nama = $data['pemohon_nama'];
            $pelayanan->pemohon_no_surat = $data['pemohon_no_surat'];
            $pelayanan->pemohon_tanggal_surat = $data['pemohon_tanggal_surat'];
            $pelayanan->pengirim_nama = $data['pengirim_nama'];
            $pelayanan->pemohon_alamat = $data['pemohon_alamat'];
            $pelayanan->pemohon_no_hp = $data['pemohon_no_hp'];
            // $pelayanan->kelengkapan_syarat = $data['kelengkapan_syarat'];
            if (isset($data['status_pelayanan'])) {
                $pelayanan->status_pelayanan = $data['status_pelayanan'];
            }
            $pelayanan->catatan = $data['catatan'];
            $pelayanan->save();
            $pelayanan->fresh();

            $summary = DaftarPelayanan::select('status_pelayanan', DB::raw('count(*) as total'))
                                                ->whereYear('created_at', '=', date('Y'))
                                                ->whereMonth('created_at', '=', date('m'))
                                                ->groupBy('status_pelayanan')
                                                ->get();

            $success = true;
            $code = 200;
            $message = 'Data Berhasil Disimpan';
        } catch (\Throwable $th) {
            $message = $th->getMessage();
        }

        return response()
            ->json([
                'success' => $success,
                'message' => $message,
                'data' => $newData,
                'summary' => $summary,
            ]);
    }

    public function search(Request $request)
    {
        $data = $request->input();

        $pelayanans = DaftarPelayanan::where('no_registrasi', 'like', '%' .  $data['q'] . '%')
                        ->get();

        return response()->json($pelayanans);
    }

    public function fetch($id_pelayanan)
    {
        $success = false;
        $message = '';
        $data = null;

        try {
            $pelayanan = DaftarPelayanan::find($id_pelayanan);
            $pelayanan->load('layanan', 'layanan.unit', 'layanan.output', 'layanan.jenis', 'arsip');
            $data = $pelayanan;
            $url_detail = route('daftar-pelayanan.detail', $pelayanan->idx_pelayanan);
            $url_pdf = route('pdf.create', $pelayanan->idx_pelayanan);
            $success = true;
        } catch (\Exception $e) {
            $message = $e->getMessage();
        }

        return response()
            ->json([
                'success' => $success,
                'message' => $message,
                'data' => $data,
                'url_detail' => $url_detail,
                'url_pdf' => $url_pdf
            ]);
    }

    public function detail(Request $request, $idx)
    {
        $id_pelayanan = Hashids::decode($idx);

        if ($request->ajax()) {
            $success = false;
            $message = '';
            $data = null;

            try {
                $pelayanan = DaftarPelayanan::where('id_pelayanan', $id_pelayanan)->firstOrFail();
                $pelayanan->load('layanan', 'layanan.unit', 'layanan.output', 'layanan.jenis', 'disposisi.sender', 'disposisi.recipient', 'disposisi.aksi', 'arsip');
                $data = $pelayanan;

                $disposisiArr = $pelayanan->disposisi;
                $disposisiCurr = $disposisiArr->last();
                $lastRecipientUsername = $disposisiCurr->recipient ? $disposisiCurr->recipient->username : '';

                $userLoggedUsername = Auth::user()->username;
                $primary = [
                    'disposisiCurr' => $disposisiCurr,
                    'bisa_disposisi' => ($userLoggedUsername == $lastRecipientUsername)
                ];

                $success = true;
            } catch (\Exception $e) {
                $message = $e->getMessage();
            }

            return response()
                ->json([
                    'success' => $success,
                    'message' => $message,
                    'data' => $data,
                    'primary' => $primary,
                ]);
        }




        $daftarLayanan = DaftarLayanan::all();
        $pegawai = User::all()->except(Auth::id());
        $aksi = MasterAksiDisposisi::all();



        return view('admin.daftar-pelayanan.detail', [
            'title'  => 'Detail Pelayanan Publik',
            'br1'  => 'Pelayanan Publik',
            'br2'  => 'Detail',
            'daftar_layanan'  => $daftarLayanan,
            'id_pelayanan'  => $idx,
            'pegawai' => $pegawai,
            'aksi' => $aksi
        ])->render();
    }

    public function storeLanding(Request $request)
    {
        $success = false;
        $message = '';
        $code = 400;

        $data = $request->input();
        $newData = null;
        $summary = [];
        $recipient = null;
        $disposisi = null;

        try {
            config(['app.locale' => 'id']);
            Carbon::setLocale('id');
            date_default_timezone_set('Asia/Jakarta');

            $layanan = DaftarLayanan::where('id_layanan', $data['id_layanan'])->first();
            $pelayananCount = DaftarPelayanan::whereYear('created_at', '=', date('Y'))
                                                ->whereMonth('created_at', '=', date('m'))
                                                ->count();

            $pelayananCount = $pelayananCount == 0 ? 1 : ($pelayananCount + 1);

            // Create Pelayanan
            $pelayanan = new DaftarPelayanan();
            $pelayanan->id_layanan = $data['id_layanan'];
            $pelayanan->no_registrasi = "01".date('ymd').sprintf('%02d', $layanan->id_unit_pengolah).sprintf('%02d', $data['id_layanan']).sprintf('%03d', $pelayananCount);
            $pelayanan->perihal = $data['perihal'];
            $pelayanan->pemohon_nama = $data['pemohon_nama'];
            $pelayanan->pemohon_no_surat = $data['pemohon_no_surat'];
            $pelayanan->pemohon_tanggal_surat = $data['pemohon_tanggal_surat'];
            $pelayanan->pengirim_nama = $data['pengirim_nama'];
            $pelayanan->pemohon_alamat = $data['pemohon_alamat'];
            $pelayanan->pemohon_no_hp = $data['pemohon_no_hp'];
            $pelayanan->kelengkapan_syarat = $data['kelengkapan_syarat'];
            $pelayanan->status_pelayanan = $data['status_pelayanan'];
            $pelayanan->catatan = $data['catatan'];
            $pelayanan->id_unit_pengolah = $layanan->id_unit_pengolah;
            $pelayanan->created_by = Auth::user() ? Auth::user()->name : 'Sistem';
            $pelayanan->save();
            $pelayanan->fresh();

            // Create Arsip
            $arsip = new \App\Models\DaftarArsip();
            $arsip->id_pelayanan = $pelayanan['id_pelayanan'];
            if (isset($data['arsip_masuk_url_register']) && $data['arsip_masuk_url_register'] != 'empty') {
                $arsip->arsip_masuk_url = $data['arsip_masuk_url_register'];
                $arsip->created_by_masuk = $data['pemohon_nama'];
            }
            $arsip->save();
            $arsip->fresh();

            if ($data['status_pelayanan'] == 'Baru') {
                // Create Disposisi
                $disposisi = new DaftarDisposisi();
                $disposisi->id_pelayanan = $pelayanan->id_pelayanan;
                $disposisi->id_aksi_disposisi = 1; // aksi 'permohonan_baru'
                $disposisi->urutan_disposisi = 1;
                $recipient = \App\Models\User::whereHas('roles', function ($q) {
                    $q->where('name', 'manager');
                })->first();
                $disposisi->id_recipient = $recipient->id;
                $disposisi->username_recipient = $recipient->username;
                $disposisi->save();
                $disposisi->fresh();
                $disposisi->load('pelayanan');

                Notification::send($recipient, new NewPelayananNotification($disposisi));
                event(new \App\Events\DispositionProcessed($pelayanan, $recipient));

                // Send Notif to Operator
                $operator = \App\Models\User::whereHas('roles', function ($q) {
                    $q->where('name', 'operator');
                })->first();
                Notification::send($operator, new NewPelayananNotification($disposisi));
                event(new \App\Events\DispositionProcessed($pelayanan, $operator));
            }

            $newData = $pelayanan;

            $summary = DaftarPelayanan::select('status_pelayanan', DB::raw('count(*) as total'))
                                                ->whereYear('created_at', '=', date('Y'))
                                                ->whereMonth('created_at', '=', date('m'))
                                                ->groupBy('status_pelayanan')
                                                ->get();

            // Save File
            $files = $request->data_file;
            $urlAssets = [];
            foreach ($files as $file) {
                $dcd = json_decode($file);
                $file = $dcd[0];
                $tempFile = TemporaryFile::where('folder', $file)->first();


                if ($tempFile) {
                    $sourcePath = storage_path('app/public/temporary/' . $tempFile->folder);
                    $sourceFile = $sourcePath . '/' . $tempFile->filename;
                    $ext = pathinfo($sourceFile, PATHINFO_EXTENSION);



                    $destinationPath =  storage_path('app/public/files/' . date('Y-m') . '/' . $pelayanan->no_registrasi);
                    $destinationFile = $destinationPath . '/' . $tempFile->filename;
                    $asset = 'storage/files/' . date('Y-m') . '/' . $pelayanan->no_registrasi . '/' . $tempFile->filename;

                    if (!Storage::exists($destinationPath)) {
                        Storage::makeDirectory($destinationPath, 0777, true); //creates directory
                    }

                    File::ensureDirectoryExists($destinationPath);
                    File::move($sourceFile, $destinationFile);
                    // Storage::move( $sourceFile, $destinationFile );

                    // Delete File and Database
                    $this->rmdir_recursive($sourcePath);
                    $tempFile->delete();


                    // getAsset
                    // $urlAssets[] = asset($asset);
                    $urlAssets[] = [
                        'filename' => $tempFile->filename,
                        'file_url' => asset($asset),
                    ];
                }
            }

            $arsip->dokumen_masuk_url = $urlAssets;
            $arsip->save();

            $success = true;
            $code = 200;
            $message = 'Data Berhasil Disimpan';
        } catch (\Throwable $th) {
            $message = $th->getMessage();
        }

        return response()
            ->json([
                'success' => $success,
                'message' => $message,
                'data' => $newData,
                'summary' => $summary,
                'recipient' => $recipient,
                'disposisi' => $disposisi,
            ]);
    }


    public function destroy(DaftarPelayanan $pelayanan)
    {
        $success = false;
        $message = '';

        try {
            $pelayanan->disposisi()->delete();
            $pelayanan->delete();
            $success = true;
        } catch (\Exception $e) {
            $message = $e->getMessage();
        }

        return response()
            ->json(['success' => $success, 'message' => $message]);
    }


    public function collect($id_unit_pengolah)
    {
        $pelayanan = DaftarLayanan::where('id_unit_pengolah', $id_unit_pengolah)->get();
        return Datatables::of($pelayanan)
        ->addIndexColumn()
        ->make(true);
    }
}
