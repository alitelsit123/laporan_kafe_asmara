@extends('template')

@section('title')
Analisis
@endsection

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Chart</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Chart</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Main row -->
        <div class="row">
          <!-- Left col -->
          <section class="col-lg-12 connectedSortable">
            <!-- Custom tabs (Charts with tabs)-->
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="fas fa-chart-pie mr-1"></i>
                  Graph
                  <i style="color:gray;font-size: 12px;">Tahun: 2022</i>
                </h3>
                <div class="card-tools">
                  <ul class="nav nav-pills ml-auto">
                    <li class="nav-item">
                      <a class="nav-link active" href="#revenue-chart" data-toggle="tab">Area</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="#sales-chart" data-toggle="tab">Donut</a>
                    </li>
                  </ul>
                </div>
              </div><!-- /.card-header -->
              <div class="card-body">
                      <div class="d-flex align-items-center justify-content-between mb-4">
                        <div class="d-flex align-items-center">
                            <a href="{{ url('/chart?'.http_build_query(array_merge(request()->query(), ['chart_type' => 'daily','chart_month' => (request('chart_month') ? request('chart_month'):ltrim((string)date('m'), '0'))]))) }}" class="btn @if(request('chart_type') === 'daily') btn-success @else btn-default @endif">
                                @if(request('chart_type') === 'daily')
                                <i class="fas fa-check"></i>
                                @endif
                                Chart Harian
                            </a>
                            <a href="{{ url('/chart?'.http_build_query(array_merge(request()->query(), ['chart_type' => 'monthly','chart_month' => (request('chart_month') ? request('chart_month'):ltrim((string)date('m'), '0'))]))) }}" class="btn @if(!request('chart_type') || request('chart_type') === 'monthly') btn-success @else btn-default @endif ml-2">
                                @if(request('chart_type') === 'monthly')
                                <i class="fas fa-check"></i>
                                @endif
                                Chart Bulanan
                            </a>
                        </div>
                        @if(request('chart_type') && request('chart_type') === 'daily')
                            <div class="d-flex align-items-center">
                                <div class="form-group form-horizontal mb-0">
                                    <div>
                                    </div>
                                    <form action="" method="get">
                                        @php
                                        $querys = request()->except(['chart_month']);
                                        @endphp
                                        @foreach($querys as $query_row => $val)
                                        <input type="hidden" name="{{$query_row}}" value="{{$val}}">
                                        @endforeach
                                        <select name="chart_month" onchange="console.log($(this).parent().submit())" id="" class="form-control">
                                            @foreach($dataMonths as $row => $val)
                                            @php
                                            $m = (string)ltrim((string)date('m'), '0');
                                            @endphp
                                            <option value="{{$row+1}}" @if((string)($row+1) == request('chart_month')) selected="selected" @endif>Bulan {{ $labelMonths[$row] ?? null }}</option>
                                            @endforeach

                                        </select>
                                    </form>
                                </div>
                                {{-- <button type="button" class="btn btn-danger ml-2">Hapus Filter</button>
                                <button type="button" class="btn btn-success ml-2">Filter</button> --}}
                            </div>
                        @endif
                  </div>
                <div class="tab-content p-0">
                  <!-- Morris chart - Sales -->
                  <div class="chart tab-pane active" id="revenue-chart"
                       style="position: relative; height: 300px;">
                      <canvas id="revenue-chart-canvas" height="300" style="height: 300px;"></canvas>
                   </div>
                  <div class="chart tab-pane" id="sales-chart" style="position: relative; height: 300px;">
                    <canvas id="sales-chart-canvas" height="300" style="height: 300px;"></canvas>
                  </div>
                </div>
              </div><!-- /.card-body -->
            </div>
            <!-- /.card -->




          </section>
          <!-- /.Left col -->
          <!-- right col (We are only adding the ID to make the widgets sortable)-->
          <section class="col-lg-12 connectedSortable">

            <!-- solid sales graph -->
            <div class="card bg-gradient-info">
              <div class="card-header border-0">
                <h3 class="card-title">
                  <i class="fas fa-th mr-1"></i>
                    Graph Tahunan
                  <i style="color:white;font-size: 12px;">per 10 Tahun</i>
                </h3>

                <div class="card-tools">
                  <button type="button" class="btn bg-info btn-sm" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                  <button type="button" class="btn bg-info btn-sm" data-card-widget="remove">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
              </div>
              <div class="card-body">
                <canvas class="chart" id="line-chart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
              </div>
              <!-- /.card-body -->

            </div>
            <!-- /.card -->


          </section>
          {{-- <section class="col-lg-5 connectedSortable">

            <!-- Calendar -->
            <div class="card bg-gradient-success">
                <div class="card-header border-0">

                  <h3 class="card-title">
                    <i class="far fa-calendar-alt"></i>
                    Calendar
                  </h3>
                  <!-- tools card -->
                  <div class="card-tools">
                    <!-- button with a dropdown -->
                    <div class="btn-group">
                      <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" data-offset="-52">
                        <i class="fas fa-bars"></i>
                      </button>
                      <div class="dropdown-menu" role="menu">
                        <a href="#" class="dropdown-item">Add new event</a>
                        <a href="#" class="dropdown-item">Clear events</a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item">View calendar</a>
                      </div>
                    </div>
                    <button type="button" class="btn btn-success btn-sm" data-card-widget="collapse">
                      <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-success btn-sm" data-card-widget="remove">
                      <i class="fas fa-times"></i>
                    </button>
                  </div>
                  <!-- /. tools -->
                </div>
                <!-- /.card-header -->
                <div class="card-body pt-0">
                  <!--The calendar -->
                  <div id="calendar" style="width: 100%"></div>
                </div>
                <!-- /.card-body -->
              </div>
              <!-- /.card -->
          </section>
          <!-- right col --> --}}
        </div>
        <!-- /.row (main row) -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

@endsection

@section('script')
<script>

    /* Chart.js Charts */
      // Sales chart
      var salesChartCanvas = document.getElementById('revenue-chart-canvas').getContext('2d')
      // $('#revenue-chart').get(0).getContext('2d');

      var salesChartData = {
        labels: JSON.parse('{!! \json_encode($labels) !!}'),
        datasets: [
          {
            label: 'Pendapatan (Rupiah)',
            backgroundColor: 'rgba(60,141,188,0.9)',
            borderColor: 'rgba(60,141,188,0.8)',
            pointRadius: false,
            pointColor: '#3b8bba',
            pointStrokeColor: 'rgba(60,141,188,1)',
            pointHighlightFill: '#fff',
            pointHighlightStroke: 'rgba(60,141,188,1)',
            data: JSON.parse('{!! \json_encode($realData) !!}')
          },
        //   {
        //     label: 'Electronics',
        //     backgroundColor: 'rgba(210, 214, 222, 1)',
        //     borderColor: 'rgba(210, 214, 222, 1)',
        //     pointRadius: false,
        //     pointColor: 'rgba(210, 214, 222, 1)',
        //     pointStrokeColor: '#c1c7d1',
        //     pointHighlightFill: '#fff',
        //     pointHighlightStroke: 'rgba(220,220,220,1)',
        //     data: [65, 59, 80, 81, 56, 55, 40]
        //   }
        ]
      }

      var salesChartOptions = {
        maintainAspectRatio: false,
        responsive: true,
        legend: {
          display: false
        },
        scales: {
          xAxes: [{
            gridLines: {
              display: false
            }
          }],
          yAxes: [{
            gridLines: {
              display: false
            }
          }]
        }
      }

      // This will get the first returned node in the jQuery collection.
      // eslint-disable-next-line no-unused-vars
      var salesChart = new Chart(salesChartCanvas, { // lgtm[js/unused-local-variable]
        type: 'line',
        data: salesChartData,
        options: salesChartOptions
      })

      // Donut Chart
      var pieChartCanvas = $('#sales-chart-canvas').get(0).getContext('2d')
      var pieData = {
        labels: [
          'Instore Sales',
          'Download Sales',
          'Mail-Order Sales'
        ],
        datasets: [
          {
            data: [30, 12, 20],
            backgroundColor: ['#f56954', '#00a65a', '#f39c12']
          }
        ]
      }
      var pieOptions = {
        legend: {
          display: false
        },
        maintainAspectRatio: false,
        responsive: true
      }
      // Create pie or douhnut chart
      // You can switch between pie and douhnut using the method below.
      // eslint-disable-next-line no-unused-vars
      var pieChart = new Chart(pieChartCanvas, { // lgtm[js/unused-local-variable]
        type: 'doughnut',
        data: pieData,
        options: pieOptions
      })

      // Sales graph chart
      var salesGraphChartCanvas = $('#line-chart').get(0).getContext('2d')
      // $('#revenue-chart').get(0).getContext('2d');

      var salesGraphChartData = {
        labels: JSON.parse('{!! \json_encode($labelYears) !!}'),
        datasets: [
          {
            label: 'Pendapatan',
            fill: false,
            borderWidth: 2,
            lineTension: 0,
            spanGaps: true,
            borderColor: '#efefef',
            pointRadius: 3,
            pointHoverRadius: 7,
            pointColor: '#efefef',
            pointBackgroundColor: '#efefef',
            data: JSON.parse('{!! \json_encode($realDataYears) !!}'),
          }
        ]
      }

      var salesGraphChartOptions = {
        maintainAspectRatio: false,
        responsive: true,
        legend: {
          display: false
        },
        scales: {
          xAxes: [{
            ticks: {
              fontColor: '#efefef'
            },
            gridLines: {
              display: false,
              color: '#efefef',
              drawBorder: false
            }
          }],
          yAxes: [{
            ticks: {
              stepSize: 5000,
              fontColor: '#efefef'
            },
            gridLines: {
              display: true,
              color: '#efefef',
              drawBorder: false
            }
          }]
        }
      }

      // This will get the first returned node in the jQuery collection.
      // eslint-disable-next-line no-unused-vars
      var salesGraphChart = new Chart(salesGraphChartCanvas, { // lgtm[js/unused-local-variable]
        type: 'line',
        data: salesGraphChartData,
        options: salesGraphChartOptions
      })

    </script>
@endsection
