<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\ReportDetail;
use App\Models\User;
use App\Models\Product;
use App\Support\Database\DB;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index() {
        $reports = Report::all();
        $users = User::all();
        $totalIncome = Report::selectRaw('sum(total_income) as total_income')->groupByRaw('MONTH(tanggal)')->whereRaw('MONTH(tanggal) = '.date('m'))->sum('total_income');
        $totalData = $reports->count();
        $totalAdmin = $users->count();
        return view('dashboard', compact('totalIncome', 'totalData', 'totalAdmin'));
    }

    public function listData() {
        $datas = Report::orderByDesc('tanggal')->get();
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
        $labelColors = ['red','green','yellow','black','purple','blue','lime','silver','gray','maroon','aqua','olive'];

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

        // $products = Product::withSum('solds','sub_total')->withSum('solds','quantity')->orderBy('name', 'desc');
        $products = null;
        $detail_products = ReportDetail::selectRaw('MONTH(reports.tanggal) month,sum(report_details.sub_total) as solds_sum_sub_total,sum(report_details.quantity) as solds_sum_quantity')
        ->join('reports', 'reports.id', 'report_details.report_id')
        ->orderByRaw('MONTH(reports.tanggal)');
        // return dd($detail_products);
        $filterMonthProduct = request('filter_month_product');
        $qiMonth = '';
        $qMonth = [];
        if($filterMonthProduct && $filterMonthProduct !=='all') {
            $filterMonthProduct = explode(',',$filterMonthProduct);
            $strFilterMonth = '\''.\implode('\',\'',$filterMonthProduct).'\'';
            foreach($filterMonthProduct as $rowMFilter) {
                $key = array_search($rowMFilter, $labelMonths);
                if($key !== false) {
                    if($key <= 9) {
                        array_push($qMonth,($key+1));
                    } else {
                        array_push($qMonth,$key+1);
                    }
                }
            }
            $qiMonth = '\''.\implode('\',\'',$qMonth).'\'';
            $detail_products = $detail_products
            ->groupByRaw('MONTH(reports.tanggal)')
            ->whereRaw('MONTH(reports.tanggal) in ('.$qiMonth.')');
            // return var_dump($qMonth);
        } else {
            $filterMonthProduct = [];
            $strFilterMonth = '\''.\implode('\',\'',$filterMonthProduct).'\'';
            foreach($labelMonths as $rowMFilter) {
                $key = array_search($rowMFilter, $labelMonths);
                if($key !== false) {
                    if($key <= 9) {
                        array_push($qMonth,($key+1));
                    } else {
                        array_push($qMonth,$key+1);
                    }
                }
            }
            $qiMonth = '\''.\implode('\',\'',$qMonth).'\'';
            $detail_products = $detail_products
            ->groupByRaw('MONTH(reports.tanggal)');
        }

        $detail_products = $detail_products->get();

        $qmonth = collect($qMonth)->sort()->all();


        $productLabels = Product::has('solds')->get();
        $productLabels = $productLabels->map(function($item){
            return $item->name;
        });

        return view('chart', compact(
            'labelColors',
            'qMonth',
            'detail_products',
            'filterMonthProduct',
            'products', 
            'productLabels',
            'year', 
            'labelYears', 
            'realDataYears', 
            'dataYears',
            'datas', 
            'totalDays', 
            'labelMonths',
            'labels',
            $dataMonths ? 'dataMonths': null, 
            'realData'
        ));
    }
    public function detailData($slug) {
        $report = Report::findOrFail($slug);
        $details = $report->details;
        return view('detail-data', compact('report','details'));
    }
}
