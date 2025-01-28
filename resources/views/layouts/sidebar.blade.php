<div id="sidebar" class="active">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header position-relative">
            <div class="d-flex justify-content-between align-items-center">
                <div class="logo">
                    <a href="/">
                        <img src="{{ asset('mazer') }}/logo.png" alt="Logo" style="height: 60px">
                    </a>
                </div>
                <div class="sidebar-toggler x">
                    <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                </div>
            </div>
        </div>
        <div class="sidebar-menu">
            <ul class="menu">
                <div id="server-status"></div>
                <hr>
                @auth
                    <li class="sidebar-item{{ request()->is('/') || request()->is('dashboard') ? ' active' : '' }}">
                        <a class="sidebar-link" href="/">
                            <i class="bi bi-speedometer"></i>
                            <span> {{ __('Dashboard') }}</span>
                        </a>
                    </li>
                @endauth

                @foreach (config('generator.sidebars') as $sidebar)
                    @if (isset($sidebar['permissions']))
                        @canany($sidebar['permissions'])
                            @foreach ($sidebar['menus'] as $menu)
                                @php
                                    $permissions = empty($menu['permission'])
                                        ? $menu['permissions']
                                        : [$menu['permission']];
                                @endphp

                                @canany($permissions)
                                    @if (empty($menu['submenus']))
                                        @can($menu['permission'])
                                            <li class="sidebar-item{{ is_active_menu($menu['route']) }}">
                                                <a href="{{ route(str($menu['route'])->remove('/') . '.index') }}"
                                                    class="sidebar-link">
                                                    {!! $menu['icon'] !!}
                                                    <span>{{ __($menu['title']) }}</span>
                                                </a>
                                            </li>
                                        @endcan
                                    @else
                                        <li class="sidebar-item has-sub {{ is_active_menu($menu['route']) }}">
                                            <a href="#" class="sidebar-link">
                                                {!! $menu['icon'] !!}
                                                <span>{{ __($menu['title']) }}</span>
                                            </a>
                                            <ul class="submenu {{ is_active_submenu($menu['route']) }}">
                                                @canany($menu['permissions'])
                                                    @foreach ($menu['submenus'] as $submenu)
                                                        @can($submenu['permission'])
                                                            <li class="submenu-item{{ is_active_menu($submenu['route']) }}">
                                                                <a href="{{ route(str($submenu['route'])->remove('/') . '.index') }}">
                                                                    {{ __($submenu['title']) }}
                                                                </a>
                                                            </li>
                                                        @endcan
                                                    @endforeach
                                                @endcanany
                                            </ul>
                                        </li>
                                    @endif
                                @endcanany
                            @endforeach
                        @endcanany
                    @endif
                @endforeach
            </ul>
        </div>
        <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
    </div>
</div>
