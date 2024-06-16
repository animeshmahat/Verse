@extends('admin.layouts.app')

@section('title', 'User Details')

@section('css')
<style>
    .user-details {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .user-details label {
        font-weight: bold;
    }

    .badge {
        padding: 0.5em 1em;
        font-size: 0.875em;
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col">
        <h2>User Details</h2>
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">User Details</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- User details -->
                    <div class="col-md-6">
                        <div class="user-details">
                            <div>
                                <label>Name:</label>
                                <span>{{ $data['user']->name }}</span>
                            </div>
                            <div>
                                <label>Username:</label>
                                <span>{{ $data['user']->username }}</span>
                            </div>
                            <div>
                                <label>Email:</label>
                                <span>{{ $data['user']->email }}</span>
                            </div>
                            <div>
                                <label>Role:</label>
                                <span>
                                    @if($data['user']->role == "superadmin")
                                    <span class="badge badge-danger">SuperAdmin</span>
                                    @else
                                    <span class="badge badge-primary">Blogger</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                    <!-- Posts details -->
                    <div class="col-md-6">
                        <div class="user-details">
                            <div>
                                <label>Total Views:</label>
                                <span>{{ $data['total_views'] }}</span>
                            </div>
                            <div>
                                <label>Views Last Month:</label>
                                <span>{{ $data['views_last_month'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col">
                        <h3>Views Prediction for Next Month</h3>
                        <canvas id="viewsPredictionChart" width="400" height="200"></canvas>
                    </div>
                </div>
                <a href="{{ route('admin.users.index') }}" class="btn btn-primary mt-3">Back to Users List</a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>
<script>
    var ctx = document.getElementById('viewsPredictionChart').getContext('2d');
    var viewsPredictionChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($data['labels']),
            datasets: [{
                label: 'Views Last Month',
                data: @json($data['views_last_month']),
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }, {
                label: 'Predicted Views',
                data: @json($data['predicted_views']),
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });
</script>
@endsection