<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{route('admin.index')}}">
        <div class="sidebar-brand-icon rotate-n-75">
            <i class="fas fa-feather"></i>
        </div>
        <div class="sidebar-brand-text mx-3" style="font-family:'Times New Roman', Times, serif">
            <h2>Verse</h2>
        </div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ Request::is('admin*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.index') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Interface
    </div>

    <!-- users -->
    <li class="nav-item {{ Request::is('admin/users*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.users.index') }}">
            <i class="fas fa-fw fa-users"></i>
            <span>Users</span>
        </a>
    </li>

    <!-- category -->
    <li class="nav-item {{ Request::is('admin/category*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.category.index') }}">
            <i class="fas fa-fw fa-list"></i>
            <span>Category</span>
        </a>
    </li>

    <!-- tags -->
    <li class="nav-item {{ Request::is('admin/tags*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.tags.index') }}">
            <i class="fas fa-fw fa-tags"></i>
            <span>Tags</span>
        </a>
    </li>

    <!-- posts -->
    <li class="nav-item {{ Request::is('admin/post*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.post.index') }}">
            <i class="fas fa-fw fa-blog"></i>
            <span>Blogs</span>
        </a>
    </li>

    <!-- settings  -->
    <li class="nav-item {{ Request::is('admin/setting*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.setting.index') }}">
            <i class="fas fa-fw fa-cog"></i>
            <span>Settings</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>
<!-- End of Sidebar -->