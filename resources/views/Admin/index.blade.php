@extends('admin.layouts.app')
@section('title', 'Dashboard')

@section('css')
<!-- Add Chart.js CSS if necessary -->
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
</div>

<div class="row">
    <!-- Current Time Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Current Time</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <span id="current-time"></span>
                        </div>
                        <small class="text-gray-500" id="current-date"></small>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(isset($data))
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Active posts</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">@if(isset($data['post'])){{$data['post']}}@endif</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-blog fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total Users</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">@if(isset($data['user'])){{$data['user']}}@endif</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Views Today</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">@if(isset($data['views_today'])){{$data['views_today']}}@endif</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-eye fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<div class="row">
    <!-- Pie Chart -->
    <div class="col-xl-6 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fa fa-eye"></i> Views by Category</h6>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="viewsByCategoryChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Function to update time and date
        function updateTime() {
            var now = new Date();
            var timeString = now.toLocaleTimeString('en-US', {
                hour12: false
            });
            var dateString = now.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            document.getElementById('current-time').innerText = timeString;
            document.getElementById('current-date').innerText = dateString;
        }

        // Initial call to display time and date
        updateTime();

        // Update time every second
        setInterval(updateTime, 1000);

        // Pie chart initialization
        var ctx = document.getElementById('viewsByCategoryChart').getContext('2d');
        var viewsByCategoryChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: @json($data['categoryLabels']),
                datasets: [{
                    data: @json($data['categoryViews']),
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                    hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#f4b619', '#d13223'],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                tooltips: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    caretPadding: 10,
                },
                legend: {
                    display: true
                },
                cutoutPercentage: 0,
            },
        });
    });
</script>
@endsection