@extends('site.layouts.app')

@section('title', 'Engagement')

@section('css')
<style>
    .post-img {
        width: 100px;
        height: 50px;
        object-fit: contain;
        border-radius: 5px;
        margin-right: 10px;
    }

    .card-title {
        font-size: 1.2rem;
        margin-bottom: 1rem;
    }
</style>
@endsection

@section('content')
<section>
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Most Viewed Post</h5>
                        @if($mostViewedPost)
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('uploads/post/' . $mostViewedPost->thumbnail) }}" alt="Post Image"
                                    class="post-img">
                                <p>{{ $mostViewedPost->title }} ({{ $mostViewedPost->views }} views)</p>
                            </div>
                        @else
                            <p>No posts available.</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Most Liked Post</h5>
                        @if($mostLikedPost)
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('uploads/post/' . $mostLikedPost->thumbnail) }}" alt="Post Image"
                                    class="post-img">
                                <p>{{ $mostLikedPost->title }} ({{ $mostLikedPost->likes_count }} likes)</p>
                            </div>
                        @else
                            <p>No posts available.</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Views</h5>
                        <p>{{ $totalViews }} total views</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Posts</h5>
                        <p>{{ $totalPosts }} posts</p>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Post Views and Likes</h5>
                        <canvas id="postChart" height="44"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('postChart').getContext('2d');

        const postTitles = @json($posts->pluck('title'));
        const postViews = @json($posts->pluck('views'));
        const postLikes = @json($posts->pluck('likes_count'));

        const postChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: postTitles,
                datasets: [
                    {
                        label: 'Views',
                        data: postViews,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 0.5
                    },
                    {
                        label: 'Likes',
                        data: postLikes,
                        backgroundColor: 'rgba(255, 99, 132, 0.6)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 0.5
                    }
                ]
            },
            options: {
                indexAxis: 'y', // Horizontal bar chart
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    datalabels: {
                        color: '#fff',
                        anchor: 'end',
                        align: 'start',
                        formatter: (value) => value,
                    }
                },
                scales: {
                    x: {
                        stacked: true,
                        beginAtZero: true
                    },
                    y: {
                        stacked: true
                    }
                }
            }
        });
    });
</script>
@endsection