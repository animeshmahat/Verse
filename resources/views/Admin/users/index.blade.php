@extends('admin.layouts.app')

@section('title', 'Users')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
<style>
    .profile:hover {
        transform: scale(1.05);
        transition: 0.25s ease-in-out;
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col">
        <h2>Users Table</h2>
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
        </div>
        @endif
        @if(session('update_success'))
        <div class="alert alert-primary alert-dismissible fade show" role="alert">
            {{ session('update_success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
        </div>
        @endif
        @if(session('delete_success'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('delete_success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
        </div>
        @endif
        <a href="{{ route('admin.users.create') }}" class="btn btn-success btn-sm mb-2"><i class="fa fa-add"></i>Add User</a>
        @if(isset($data['row']))
        <p class="text-center"><span class="btn btn-disabled btn-sm btn-outline-primary">Total Users: {{$data['row']->where('role', 'user')->count()}}</span>&nbsp;&nbsp;<span class="btn btn-disabled btn-sm btn-outline-danger">Total Admins: {{$data['row']->where('role', 'superadmin')->count()}}</span></p>
        @endif
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Users Table</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>S.N</th>
                                <th>Name (Username)</th>
                                <th>Role</th>
                                <th>Posts</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>S.N</th>
                                <th>Name (Username)</th>
                                <th>Role</th>
                                <th>Posts</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            @if(isset($data['row']))
                            @foreach ($data['row'] as $key => $row)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $row->name }} ({{ $row->username }})</td>
                                <td>
                                    @if($row->role == "superadmin")
                                    <span class="badge badge-danger">Admin</span>
                                    @else
                                    <span class="badge badge-primary">User</span>
                                    @endif
                                </td>
                                <td>{{ $row->posts_count }}</td>
                                <td>
                                    @if($row->status == "1")
                                    <span class="badge bg-success">ACTIVE</span>
                                    @elseif($row->status == "0")
                                    <span class="badge bg-danger">INACTIVE</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-row">
                                        <a href="{{ route('admin.users.view', ['id' => $row->id]) }}" class="btn-circle btn-info m-1"><i class="fa-regular fa-eye"></i></a>
                                        @if($row->role != 'superadmin')
                                        <a href="{{ route('admin.users.edit', ['id' => $row->id]) }}" class="btn-circle btn-warning m-1"><i class="fa-regular fa-pen-to-square"></i></a>
                                        <a href="{{ route('admin.users.delete', ['id' => $row->id]) }}" class="btn-circle btn-danger m-1" onclick="return confirm('Delete user permanently?')"><i class="fa fa-trash"></i></a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('assets/admin/js/datatables-simple-demo.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
@endsection