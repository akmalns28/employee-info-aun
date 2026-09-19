<aside class="navbar navbar-vertical navbar-expand-lg navbar-transparent">
    <div class="container-fluid">
        <!-- BEGIN NAVBAR TOGGLER -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu"
            aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- END NAVBAR TOGGLER -->
        <!-- BEGIN NAVBAR LOGO -->
        <div class="navbar-brand navbar-brand-autodark">
            <h2 class="mb-0">SIKAUN</h2>
        </div>
        <!-- END NAVBAR LOGO -->
        <div class="collapse navbar-collapse" id="sidebar-menu">
            <!-- BEGIN NAVBAR MENU -->
            <ul class="navbar-nav pt-lg-3">

                <li class="nav-item {{ Route::is('dashboard') ? 'active' : '' }}">
                    @haspermission('dashboard.view')
                        <a class="nav-link" href="{{ route('dashboard') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-dashboard">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M10 13a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                    <path d="M13.45 11.55l2.05 -2.05" />
                                    <path d="M6.4 20a9 9 0 1 1 11.2 0l-11.2 0" />
                                </svg></span>
                            <span class="nav-link-title"> Dashboard </span>
                        </a>
                    @endhaspermission

                </li>
                @hasanyrole(['super admin', 'admin'])
                    <li
                        class="nav-item dropdown {{ Route::is('permissions.*', 'role.*', 'hak-akses.*', 'departemen.*', 'user.*','posisi.*') ? 'active' : '' }}">
                        <a class="nav-link dropdown-toggle {{ Route::is('permissions.*', 'role.*', 'hak-akses.*', 'departemen.*', 'user.*','posisi.*') ? 'show' : '' }}"
                            href="#navbar-layout" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button"
                            aria-expanded="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-database">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M4 6a8 3 0 1 0 16 0a8 3 0 1 0 -16 0" />
                                    <path d="M4 6v6a8 3 0 0 0 16 0v-6" />
                                    <path d="M4 12v6a8 3 0 0 0 16 0v-6" />
                                </svg></span>
                            <span class="nav-link-title">Data Master</span>
                        </a>
                        <div
                            class="dropdown-menu {{ Route::is('permissions.*', 'role.*', 'hak-akses.*', 'departemen.*', 'user.*', 'posisi.*') ? 'show' : '' }}">
                            <div class="dropdown-menu-columns">
                                <div class="dropdown-menu-column">
                                    @php($user = auth()->user())

                                    @if ($user->hasPermissionTo('permission.view') || $user->hasAnyRole(['super admin']))

                                    <a class="dropdown-item {{ Route::is('permission*') ? 'active' : '' }}"
                                            href="{{ route('permissions.index') }}">
                                            Permission
                                        </a>
                                    @endif
                                    
                                    

                                    @if ($user->hasPermissionTo('role.view') || $user->hasAnyRole(['super admin']))
                                        <a class="dropdown-item {{ Route::is('role*') ? 'active' : '' }}"
                                            href="{{ route('role.index') }}">
                                            Role
                                        </a>
                                    @endif

                                    @if ($user->hasPermissionTo('departemen.view') || $user->hasAnyRole(['super admin', 'admin']))
                                        <a class="dropdown-item {{ Route::is('departemen*') ? 'active' : '' }}"
                                            href="{{ route('departemen.index') }}">
                                            Departemen
                                        </a>
                                    @endif

                                     @if ($user->hasPermissionTo('posisi.view') || $user->hasAnyRole(['super admin']))

                                    <a class="dropdown-item {{ Route::is('posisi*') ? 'active' : '' }}"
                                            href="{{ route('posisi.index') }}">
                                            Posisi
                                        </a>
                                    @endif

                                    @if ($user->hasPermissionTo('user.view') || $user->hasAnyRole(['super admin', 'admin']))
                                        <a class="dropdown-item {{ Route::is('user*') ? 'active' : '' }}"
                                            href="{{ route('user.index') }}">
                                            User
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </li>
                @endhasanyrole

                <hr class="my-2">
                <li class="nav-item {{ Route::is('karyawan.*') ? 'active' : '' }}">
                    @haspermission('karyawan.view')
                        <a class="nav-link" href="{{ route('karyawan.index') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-user-square-rounded">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M12 13a3 3 0 1 0 0 -6a3 3 0 0 0 0 6" />
                                    <path
                                        d="M12 3c7.2 0 9 1.8 9 9c0 7.2 -1.8 9 -9 9c-7.2 0 -9 -1.8 -9 -9c0 -7.2 1.8 -9 9 -9" />
                                    <path d="M6 20.05v-.05a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v.05" />
                                </svg></span>
                            <span class="nav-link-title"> Karyawan </span>
                        </a>
                    @endhaspermission

                </li>


                <li class="nav-item">
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                    <a class="nav-link" href="#"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round"
                                class="text-danger icon icon-tabler icons-tabler-outline icon-tabler-logout">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path
                                    d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" />
                                <path d="M9 12h12l-3 -3" />
                                <path d="M18 15l3 -3" />
                            </svg>
                        </span>
                        <span class="nav-link-title text-danger"> Keluar </span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</aside>
