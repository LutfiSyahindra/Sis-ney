<nav class="sidebar">
    <div class="sidebar-header">
        <a href="#" class="sidebar-brand">
            Sis<span>NEY</span>
        </a>
        <div class="sidebar-toggler not-active">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
    <div class="sidebar-body">
        <ul class="nav">
            <li class="nav-item nav-category">Main</li>
            <li class="nav-item">
                <a href="{{ route("dashboard") }}" class="nav-link">
                    <i class="link-icon" data-feather="box"></i>
                    <span class="link-title">Dashboard</span>
                </a>
            </li>

            <li class="nav-item nav-category">Users Setting</li>
            @can("SISNEY.SETTINGS")
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#settings" role="button" aria-expanded="false"
                        aria-controls="settings">
                        <i class="link-icon" data-feather="settings"></i>
                        <span class="link-title">Settings</span>
                        <i class="link-arrow" data-feather="chevron-down"></i>
                    </a>
                    <div class="collapse" id="settings">
                        <ul class="nav sub-menu">
                            <li class="nav-item">
                                <a href="{{ route("users.users") }}" class="nav-link">Users</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route("roles.utama") }}" class="nav-link">Roles</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route("permissions.main") }}" class="nav-link">Permissions</a>
                            </li>
                        </ul>
                    </div>
                </li>
            @endcan

            <li class="nav-item nav-category">Menu</li>
            @can("SISNEY.MENU.KATEGORI_TRANSAKSI")
                <li class="nav-item">
                    <a href="{{ route("kategoriTransaksi.kategori") }}"
                        class="nav-link {{ request()->routeIs("kategoriTransaksi.kategori") ? "active" : "" }}">
                        <i class="link-icon" data-feather="tag"></i>
                        <span class="link-title">Kategori Transaksi</span>
                    </a>
                </li>
            @endcan
            @can("SISNEY.MENU.ASET_TABUNGAN")
                <li class="nav-item">
                    <a href="{{ route("asetTabungan.view") }}"
                        class="nav-link {{ request()->routeIs("asetTabungan.view") ? "active" : "" }}">
                        <i class="link-icon" data-feather="credit-card"></i>
                        <span class="link-title">Aset Tabungan</span>
                    </a>
                </li>
            @endcan
            @can("SISNEY.MENU.TRANSFER")
                <li class="nav-item">
                    <a href="{{ route("transfer.transfer") }}"
                        class="nav-link {{ request()->routeIs("transfer.transfer") ? "active" : "" }}">
                        <i class="link-icon" data-feather="repeat"></i>
                        <span class="link-title">Transfer</span>
                    </a>
                </li>
            @endcan
            @can("SISNEY.MENU.TRANSAKSI")
                <li class="nav-item">
                    <a href="{{ route("transaksi.transaksi") }}"
                        class="nav-link {{ request()->routeIs("transaksi.transaksi") ? "active" : "" }}">
                        <i class="link-icon" data-feather="dollar-sign"></i>
                        <span class="link-title">Transaksi</span>
                    </a>
                </li>
            @endcan
            @can("SISNEY.MENU.BUDGET")
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#budgets" role="button" aria-expanded="false"
                        aria-controls="budgets">
                        <i class="link-icon" data-feather="bar-chart-2"></i>
                        <span class="link-title">Budgets</span>
                        <i class="link-arrow" data-feather="chevron-down"></i>
                    </a>
                    <div class="collapse" id="budgets">
                        <ul class="nav sub-menu">
                            <li class="nav-item">
                                <a href="/apps/budgets/DaftarBudgets" class="nav-link">
                                    Daftar Budget
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/apps/budgets/KategoriBudgets" class="nav-link">
                                    Kategori Budget
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/apps/budgetPlan/budgetPlan" class="nav-link">
                                    Budget Plan
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/apps/budgetPlanDetail/budgetPlanDetail/view" class="nav-link">
                                    Budget Plan Detail
                                </a>
                            </li>
                        </ul>
                    </div>

                </li>
            @endcan
            @can("SISNEY.MENU.REPORT")
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#report" role="button" aria-expanded="false"
                        aria-controls="report">
                        <i class="link-icon" data-feather="file-text"></i>
                        <span class="link-title">Report</span>
                        <i class="link-arrow" data-feather="chevron-down"></i>
                    </a>
                    <div class="collapse" id="report">
                        <ul class="nav sub-menu">
                            <li class="nav-item">
                                <a href="/apps/report/TransaksiReport" class="nav-link">
                                    Transaksi Report
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/apps/report/asetTabunganReport" class="nav-link">
                                    Aset Tabungan Report
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/apps/report/budgetReport" class="nav-link">
                                    Budget Report
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            @endcan
        </ul>
    </div>
</nav>
