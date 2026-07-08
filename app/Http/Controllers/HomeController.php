<?php

namespace App\Http\Controllers;

use App\Models\TotalLayananPerHari;
use App\Models\TotalLayananPerMinggu;
use App\Models\DaftarLayanan;
use Illuminate\Http\Request;
use App\Models\DaftarPelayanan;
use Illuminate\Support\Facades\Hash;
use Auth;
use DB;
use DateTime;
use DateInterval;
use DatePeriod;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    protected function _getPeriodRange($start, $end)
    {
        $start    = (new DateTime($start))->modify('first day of this month');
        $end      = (new DateTime($end))->modify('first day of this month');
        $interval = DateInterval::createFromDateString('1 month');
        $period   = new DatePeriod($start, $interval, $end);

        $ret = [];
        foreach ($period as $dt) {
            $ret[] = [
                'title' => $dt->format("M Y"),
                'title_old' => $dt->format("F Y"),
                'year' => $dt->format("Y"),
                'month' => $dt->format("m"),
            ];
        }

        // return array_reverse($ret);
        return $ret;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $access_type = DB::table('access_type')->first();
        $type = $access_type->name;

        $statusPelayanan = [];
        $statusPelayanan = [
            [
                'name' => 'Baru',
                'color' => 'danger',
                'total' => 0,
            ],
            [
                'name' => 'Proses',
                'color' => 'secondary',
                'total' => 0,
            ],
            [
                'name' => 'Selesai',
                'color' => 'primary',
                'total' => 0,
            ],
            [
                'name' => 'Ambil',
                'color' => 'success',
                'total' => 0,
            ]
        ];

        // $pelayananColl = DaftarPelayanan::whereYear('created_at', '=', date('Y'))
            //                                         ->whereMonth('created_at', '=', date('m'))
        $pelayananColl = DaftarPelayanan::with('unit')
                                                ->get();

        $pByStatus = $pelayananColl->groupBy('status_pelayanan');

        foreach ($statusPelayanan as $key => $item) {
            $countItem = isset($pByStatus[$item['name']]) ? $pByStatus[$item['name']]->count() : 0;
            $statusPelayanan[$key]['total'] = $countItem;
        }

        $sUnit = [];
        if ($type != 'MINIMAL') {
            $units = \App\Models\UnitPengolah::all();
            foreach ($units as $key => $item) {
                $sUnit[] = [
                    'name' => $item->name,
                    'full_name' => $item->name,
                    'value' => 0
                ];
            }

            $pByUnit = $pelayananColl->groupBy('unit.name');

            if ($pByUnit) {
                foreach ($sUnit as $key => $item) {
                    $countItem = isset($pByUnit[$item['name']]) ? $pByUnit[$item['name']]->count() : 0;
                    $sUnit[$key]['value'] = $countItem;

                    if (isset($pByUnit[$item['name']])) {
                        $byStat = $pByUnit[$item['name']]->groupBy('status_pelayanan');
                        $sUnit[$key]['Baru'] = isset($byStat['Baru']) ? $byStat['Baru']->count() : 0;
                        $sUnit[$key]['Proses'] = isset($byStat['Proses']) ? $byStat['Proses']->count() : 0;
                        $sUnit[$key]['Selesai'] = isset($byStat['Selesai']) ? $byStat['Selesai']->count() : 0;
                    } else {
                        $sUnit[$key]['Baru'] = 0;
                        $sUnit[$key]['Proses'] = 0;
                        $sUnit[$key]['Selesai'] = 0;
                    }

                    if ($sUnit[$key]['name'] == 'Subbagian Tata Usaha') {
                        $sUnit[$key]['name'] = 'SubbagTU';
                    }

                    if ($sUnit[$key]['name'] == 'Seksi Pendidikan Madrasah') {
                        $sUnit[$key]['name'] = 'Seksi PenMad';
                    }

                    if ($sUnit[$key]['name'] == 'Seksi Pendidikan Agama Islam') {
                        $sUnit[$key]['name'] = 'Seksi PAIs';
                    }

                    if ($sUnit[$key]['name'] == 'Seksi Pendidikan Diniyah dan Pondok Pesantren') {
                        $sUnit[$key]['name'] = 'Seksi PD Pontren';
                    }

                    if ($sUnit[$key]['name'] == 'Seksi Penyelenggaraan Haji dan Umrah') {
                        $sUnit[$key]['name'] = 'Seksi PHU';
                    }

                    if ($sUnit[$key]['name'] == 'Seksi Bimbingan Masyarakat Islam') {
                        $sUnit[$key]['name'] = 'Seksi BiMas';
                    }

                    if ($sUnit[$key]['name'] == 'Seksi Penyelenggara Zakat dan Wakaf') {
                        $sUnit[$key]['name'] = 'Seksi ZaWa';
                    }
                }
            }
        }

        // Berdasarkan Timeline
        $dataDaily = null;
        $dataWeekly = null;
        if ($type != 'MINIMAL') {
            // $total = TotalLayananPerMinggu::orderBy('id_total_layanan_perminggu', 'DESC')->take(8)->get();
            $total = TotalLayananPerMinggu::orderBy('id_total_layanan_perminggu', 'DESC')->get();
            $total = $total->sortBy("id_total_layanan_perminggu");

            $series [] = [
                'name' => 'Total Pelayanan',
                'data' => $total->pluck('total_pelayanan')
            ];

            // $categories = $total->pluck('week_range');
            $categories = $total->pluck('week_end_date');
            // return $categories;

            $dataWeekly = [
                'series' => $series,
                'categories' => $categories
            ];

            $totalD = TotalLayananPerHari::get();

            $seriesD [] = [
                'name' => 'Total Pelayanan',
                'data' => $totalD->pluck('total_pelayanan')
            ];

            $categoriesD = $totalD->pluck('date');

            $dataDaily = [
                'series' => $seriesD,
                'categories' => $categoriesD
            ];
        }

        // Tabel Rekapitulasi Bulanan
        $daftarpelayanangrouped = null;
        if ($type != 'MINIMAL') {
            $daftarpelayanan = DB::select('
                SELECT c.id_unit_pengolah as id_unit, c.name as unit, a.name as layanan, COALESCE(count(b.id_pelayanan)) as total
                FROM daftar_layanan as a
                LEFT JOIN daftar_pelayanan as b ON a.id_layanan = b.id_layanan
                LEFT JOIN daftar_unit_pengolah as c ON a.id_unit_pengolah = c.id_unit_pengolah
                GROUP BY c.id_unit_pengolah, c.name, a.name
                ORDER BY c.id_unit_pengolah ASC');
            $daftarpelayanangrouped = collect($daftarpelayanan)->groupBy('unit');
        }

        // Ringkasan Rekapitulasi
        $fixData = null;
        if ($type != 'MINIMAL') {
            $pelayananS = DaftarPelayanan::orderBy('created_at', 'asc')->first();
            $pelayananE = DaftarPelayanan::orderBy('created_at', 'desc')->first();

            $start = $pelayananS->created_at;
            $end = $pelayananE->created_at;
            $range = $this->_getPeriodRange($start, $end);

            $collData = [];
            foreach ($range as $item) {
                $getData = DB::select("
            SELECT b.id_unit_pengolah, b.name, COALESCE(COUNT(a.id_pelayanan)) as total_layanan
            FROM daftar_unit_pengolah as b
            LEFT JOIN daftar_pelayanan as a ON b.id_unit_pengolah = a.id_unit_pengolah AND MONTH(a.created_at) = ? AND YEAR(a.created_at) = ?
            GROUP BY b.id_unit_pengolah, b.name
            ORDER BY b.id_unit_pengolah", [$item['month'], $item['year']]);
                $collData[$item['title']] = $getData;
            }

            $fixData = [];
            $fixData['header'][] = 'Nama Unit';



            // return $collData;
            $counter = 1;
            foreach ($collData as $bulan => $item) {
                $fixData['header'][] = $bulan;
                foreach ($item as $k => $coll) {
                    if (!isset($fixData[$coll->name])) {
                        $fixData[$coll->name][] = $coll->name;
                        $fixData[$coll->name][] = $coll->total_layanan;
                    } else {
                        $fixData[$coll->name][] = $coll->total_layanan;
                    }

                    $counter++;
                }
            }

            $totalperBulan = [];
            $totalperBulan[] = 'Total';
            foreach ($collData as $bulan => $item) {
                $totalperBulan[] = array_sum(array_column($item, 'total_layanan'));
            }

            $fixData['Total'] = $totalperBulan;
            // return $fixData;
        }

        // Disposisi
        $undoneDisp = [];
        if ($type != 'MINIMAL') {
            $disposisiNotDone = \App\Models\DaftarDisposisi::whereDoesntHave('child')
                        ->whereNotNull('id_aksi_disposisi')
                        ->whereNotNull('id_recipient')
                        ->with('recipient')
                        ->get();


            $grouped =  $disposisiNotDone->groupBy('recipient.name');

            $res = [];
            foreach ($grouped as $key => $value) {
                $res[] = [
                    'name' => $key,
                    'count' => count($value)
                ];
            }

            $undoneDisp = $res;
        }

        return view('admin.home.index', [
            'title'  => 'Halaman Beranda',
            'br1'  => 'Home',
            'br2'  => 'Beranda',
            'summaryPelayanan'  => $statusPelayanan,
            'undoneDisp'  => $undoneDisp,
            'totalByUnit'  => $sUnit,
            'dataWeekly'  => $dataWeekly,
            'dataDaily'  => $dataDaily,
            'daftarpelayanangrouped'  => $daftarpelayanangrouped,
            'fixData'  => $fixData,
            'greeting' => $this->_getGreeting(),
            'type' => $type,
        ]);
    }

    public function showChangePasswordForm()
    {
        return view('auth.changepassword', [
            'title' => 'Ubah Password'
        ]);
    }

    public function _getGreeting()
    {
        $greeting = '';
        $b = time();
        $hour = date("G", $b);

        if ($hour>=0 && $hour<=11) {
            $greeting = "Selamat Pagi ";
        } elseif ($hour >=12 && $hour<=14) {
            $greeting = "Selamat Siang ";
        } elseif ($hour >=15 && $hour<=17) {
            $greeting = "Selamat Sore ";
        } elseif ($hour >=17 && $hour<=18) {
            $greeting = "Selamat Petang ";
        } elseif ($hour >=19 && $hour<=23) {
            $greeting = "Selamat Malam ";
        }

        return $greeting;
    }

    public function changePassword(Request $request)
    {
        if (!(Hash::check($request->get('current-password'), Auth::user()->password))) {
            // The passwords matches
            return redirect()->back()->with("error", "Your current password does not matches with the password you provided. Please try again.");
        }

        if (strcmp($request->get('current-password'), $request->get('new-password')) == 0) {
            //Current password and new password are same
            return redirect()->back()->with("error", "New Password cannot be same as your current password. Please choose a different password.");
        }

        $validatedData = $request->validate([
            'current-password' => 'required',
            'new-password' => 'required|string|min:6|confirmed',
        ]);

        //Change Password
        $user = Auth::user();
        $user->password = bcrypt($request->get('new-password'));
        $user->plain_password = $request->get('new-password');
        $user->save();

        return redirect()->route('home')->with("success", "Password changed successfully !");
    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function guest(Request $request)
    {
        $access_type = DB::table('access_type')->first();
        $type = $access_type->name;

        $statusPelayanan = [];
        $statusPelayanan = [
            [
                'name' => 'Baru',
                'color' => 'danger',
                'total' => 0,
            ],
            [
                'name' => 'Proses',
                'color' => 'secondary',
                'total' => 0,
            ],
            [
                'name' => 'Selesai',
                'color' => 'primary',
                'total' => 0,
            ],
            [
                'name' => 'Ambil',
                'color' => 'success',
                'total' => 0,
            ]
        ];

        // $pelayananColl = DaftarPelayanan::whereYear('created_at', '=', date('Y'))
            //                                         ->whereMonth('created_at', '=', date('m'))
        $pelayananColl = DaftarPelayanan::with('unit')
                                                ->get();

        $pByStatus = $pelayananColl->groupBy('status_pelayanan');

        foreach ($statusPelayanan as $key => $item) {
            $countItem = isset($pByStatus[$item['name']]) ? $pByStatus[$item['name']]->count() : 0;
            $statusPelayanan[$key]['total'] = $countItem;
        }

        $sUnit = [];
        if ($type != 'MINIMAL') {
            $units = \App\Models\UnitPengolah::all();
            foreach ($units as $key => $item) {
                $sUnit[] = [
                    'name' => $item->name,
                    'full_name' => $item->name,
                    'value' => 0
                ];
            }

            $pByUnit = $pelayananColl->groupBy('unit.name');

            if ($pByUnit) {
                foreach ($sUnit as $key => $item) {
                    $countItem = isset($pByUnit[$item['name']]) ? $pByUnit[$item['name']]->count() : 0;
                    $sUnit[$key]['value'] = $countItem;

                    if (isset($pByUnit[$item['name']])) {
                        $byStat = $pByUnit[$item['name']]->groupBy('status_pelayanan');
                        $sUnit[$key]['Baru'] = isset($byStat['Baru']) ? $byStat['Baru']->count() : 0;
                        $sUnit[$key]['Proses'] = isset($byStat['Proses']) ? $byStat['Proses']->count() : 0;
                        $sUnit[$key]['Selesai'] = isset($byStat['Selesai']) ? $byStat['Selesai']->count() : 0;
                    } else {
                        $sUnit[$key]['Baru'] = 0;
                        $sUnit[$key]['Proses'] = 0;
                        $sUnit[$key]['Selesai'] = 0;
                    }

                    if ($sUnit[$key]['name'] == 'Subbagian Tata Usaha') {
                        $sUnit[$key]['name'] = 'SubbagTU';
                    }

                    if ($sUnit[$key]['name'] == 'Seksi Pendidikan Madrasah') {
                        $sUnit[$key]['name'] = 'Seksi PenMad';
                    }

                    if ($sUnit[$key]['name'] == 'Seksi Pendidikan Agama Islam') {
                        $sUnit[$key]['name'] = 'Seksi PAIs';
                    }

                    if ($sUnit[$key]['name'] == 'Seksi Pendidikan Diniyah dan Pondok Pesantren') {
                        $sUnit[$key]['name'] = 'Seksi PD Pontren';
                    }

                    if ($sUnit[$key]['name'] == 'Seksi Penyelenggaraan Haji dan Umrah') {
                        $sUnit[$key]['name'] = 'Seksi PHU';
                    }

                    if ($sUnit[$key]['name'] == 'Seksi Bimbingan Masyarakat Islam') {
                        $sUnit[$key]['name'] = 'Seksi BiMas';
                    }

                    if ($sUnit[$key]['name'] == 'Seksi Penyelenggara Zakat dan Wakaf') {
                        $sUnit[$key]['name'] = 'Seksi ZaWa';
                    }
                }
            }
        }

        // Berdasarkan Timeline
        $dataDaily = null;
        $dataWeekly = null;
        if ($type != 'MINIMAL') {
            // $total = TotalLayananPerMinggu::orderBy('id_total_layanan_perminggu', 'DESC')->take(8)->get();
            $total = TotalLayananPerMinggu::orderBy('id_total_layanan_perminggu', 'DESC')->get();
            $total = $total->sortBy("id_total_layanan_perminggu");

            $series [] = [
                'name' => 'Total Pelayanan',
                'data' => $total->pluck('total_pelayanan')
            ];

            // $categories = $total->pluck('week_range');
            $categories = $total->pluck('week_end_date');
            // return $categories;

            $dataWeekly = [
                'series' => $series,
                'categories' => $categories
            ];

            $totalD = TotalLayananPerHari::get();

            $seriesD [] = [
                'name' => 'Total Pelayanan',
                'data' => $totalD->pluck('total_pelayanan')
            ];

            $categoriesD = $totalD->pluck('date');

            $dataDaily = [
                'series' => $seriesD,
                'categories' => $categoriesD
            ];
        }

        // Tabel Rekapitulasi Bulanan
        $daftarpelayanangrouped = null;
        if ($type != 'MINIMAL') {
            $daftarpelayanan = DB::select('
                SELECT c.id_unit_pengolah as id_unit, c.name as unit, a.name as layanan, COALESCE(count(b.id_pelayanan)) as total
                FROM daftar_layanan as a
                LEFT JOIN daftar_pelayanan as b ON a.id_layanan = b.id_layanan
                LEFT JOIN daftar_unit_pengolah as c ON a.id_unit_pengolah = c.id_unit_pengolah
                GROUP BY c.id_unit_pengolah, c.name, a.name
                ORDER BY c.id_unit_pengolah ASC');
            $daftarpelayanangrouped = collect($daftarpelayanan)->groupBy('unit');
        }

        // Ringkasan Rekapitulasi
        $fixData = null;
        if ($type != 'MINIMAL') {
            $pelayananS = DaftarPelayanan::orderBy('created_at', 'asc')->first();
            $pelayananE = DaftarPelayanan::orderBy('created_at', 'desc')->first();

            $start = $pelayananS->created_at;
            $end = $pelayananE->created_at;
            $range = $this->_getPeriodRange($start, $end);

            $collData = [];
            foreach ($range as $item) {
                $getData = DB::select("
            SELECT b.id_unit_pengolah, b.name, COALESCE(COUNT(a.id_pelayanan)) as total_layanan
            FROM daftar_unit_pengolah as b
            LEFT JOIN daftar_pelayanan as a ON b.id_unit_pengolah = a.id_unit_pengolah AND MONTH(a.created_at) = ? AND YEAR(a.created_at) = ?
            GROUP BY b.id_unit_pengolah, b.name
            ORDER BY b.id_unit_pengolah", [$item['month'], $item['year']]);
                $collData[$item['title']] = $getData;
            }

            $fixData = [];
            $fixData['header'][] = 'Nama Unit';



            // return $collData;
            $counter = 1;
            foreach ($collData as $bulan => $item) {
                $fixData['header'][] = $bulan;
                foreach ($item as $k => $coll) {
                    if (!isset($fixData[$coll->name])) {
                        $fixData[$coll->name][] = $coll->name;
                        $fixData[$coll->name][] = $coll->total_layanan;
                    } else {
                        $fixData[$coll->name][] = $coll->total_layanan;
                    }

                    $counter++;
                }
            }

            $totalperBulan = [];
            $totalperBulan[] = 'Total';
            foreach ($collData as $bulan => $item) {
                $totalperBulan[] = array_sum(array_column($item, 'total_layanan'));
            }

            $fixData['Total'] = $totalperBulan;
            // return $fixData;
        }

        // Disposisi
        $undoneDisp = [];
        if ($type != 'MINIMAL') {
            $disposisiNotDone = \App\Models\DaftarDisposisi::whereDoesntHave('child')
                        ->whereNotNull('id_aksi_disposisi')
                        ->whereNotNull('id_recipient')
                        ->with('recipient')
                        ->get();


            $grouped =  $disposisiNotDone->groupBy('recipient.name');

            $res = [];
            foreach ($grouped as $key => $value) {
                $res[] = [
                    'name' => $key,
                    'count' => count($value)
                ];
            }

            $undoneDisp = $res;
        }

        return view('guest.index', [
            'title'  => 'Statistik Pelayanan Informasi Publik secara Terpadu Satu Pintu',
            'br1'  => 'Home',
            'br2'  => 'Statistik Pelayanan',
            'summaryPelayanan'  => $statusPelayanan,
            'undoneDisp'  => $undoneDisp,
            'totalByUnit'  => $sUnit,
            'dataWeekly'  => $dataWeekly,
            'dataDaily'  => $dataDaily,
            'daftarpelayanangrouped'  => $daftarpelayanangrouped,
            'fixData'  => $fixData,
            'greeting' => $this->_getGreeting(),
            'type' => $type,
        ]);
    }
}
