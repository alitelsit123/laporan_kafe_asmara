<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\User;
use App\Support\Database\DB;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index() {
        $reports = Report::all();
        $users = User::all();
        $totalIncome = $reports->sum('total_income');
        $totalData = $reports->count();
        $totalAdmin = $users->count();
        return view('dashboard', compact('totalIncome', 'totalData', 'totalAdmin'));
    }

    public function listData() {
        $datas = Report::orderByDesc('id')->get();
        return view('data', compact('datas'));
    }

    public function chart() {
        $year = date('Y');

        $type = request('chart_type');
        if($type === 'daily') {
            $defaultQuery = Report::selectRaw('DAY(tanggal) as tanggal,sum(total_income) as total_income')
            ->whereRaw('YEAR(tanggal) = 2022 AND MONTH(tanggal) = '.(request('chart_month') ? request('chart_month'): date('m')) )
            ->groupByRaw('DAY(tanggal)')
            ->get()
            ->sortBy('tanggal');
            $array = $defaultQuery->all();
            $defaultQuerys = Report::selectRaw('MONTH(tanggal) as tanggal,sum(total_income) as total_income')
            ->whereRaw('YEAR(tanggal) = 2022')
            ->groupByRaw('MONTH(tanggal)')->get()
            ->sortBy('tanggal');
            $arrays = $defaultQuerys->all();
            // $labels = [];
            // $labels = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Desember'];
            $currentMonth = new Carbon('2022-'.(request('chart_month') ? request('chart_month'): date('m')));
            $totalDays = $currentMonth->daysInMonth;
            $labels = range(1,$totalDays);
            $labelMonths = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Desember'];
        } else {
            $defaultQuery = Report::selectRaw('MONTH(tanggal) as tanggal,sum(total_income) as total_income')
            ->whereRaw('YEAR(tanggal) = 2022')
            ->groupByRaw('MONTH(tanggal)')->get()
            ->sortBy('tanggal');
            $array = $defaultQuery->all();
            $labels = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Desember'];
            $labelMonths = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Desember'];
            $currentMonth = Carbon::parse('2022-'.(request('chart_month') ? request('chart_month'): date('m')).'-01');
            $totalDays = $currentMonth->daysInMonth;
        }

        $datas = $defaultQuery->toArray();
        if(isset($defaultQuerys)) {
            $dataMonths = $defaultQuerys->all();
        } else {
            $dataMonths = $defaultQuery;
        }
        $reduceData = array_column($datas, 'total_income');
        $realData = array_map(function($item) {
            return $item;
        }, $reduceData);

        $labelYears = range(2022, 2033);
        $dataYears = Report::selectRaw('YEAR(tanggal) as tanggal,sum(total_income) as total_income')
        ->groupByRaw('YEAR(tanggal)')
        ->get()
        ->sortBy('tanggal');
        $realDataYears = array_column($dataYears->toArray(), 'total_income');


        return view('chart', compact('year', 'labelYears', 'realDataYears', 'dataYears','datas', 'totalDays', 'labelMonths','labels',$dataMonths ? 'dataMonths': null, 'realData'));
    }
    public function detailData($slug) {
        $report = Report::findOrFail($slug);
        $details = $report->details;
        return view('detail-data', compact('report','details'));
    }
}
