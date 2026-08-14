@extends('store.layouts.app')

@section('content')
<div class="account-page-wrapper py-4 py-md-5">
    <div class="container account-container">

        @if(session('success'))
            <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center gap-3">
                <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                <div class="flex-grow-1 fw-bold">{{ session('success') }}</div>
                <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center gap-3">
                <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
                <div class="flex-grow-1 fw-bold">{{ session('error') }}</div>
                <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
            </div>
        @endif

        {{-- PAGE TOP HEADER --}}
        <div class="account-top-header mb-4">
            <h1 class="account-main-title">{{ __('حسابك') }}</h1>
            <p class="account-user-subtitle">
                <strong class="text-dark">{{ Auth::user()->name }}</strong>
                @if(Auth::user()->email)
                    <span class="text-muted ms-1 me-1">• {{ __('البريد الإلكتروني:') }} {{ Auth::user()->email }}</span>
                @endif
                @if(Auth::user()->phone)
                    <span class="text-muted ms-1 me-1">• {{ __('الهاتف:') }} {{ Auth::user()->phone }}</span>
                @endif
            </p>
        </div>

        {{-- DEDICATED FLEX GRID LAYOUT --}}
        <div class="account-grid-layout">

            {{-- SIDEBAR COLUMN --}}
            <aside class="account-sidebar-col">
                <div class="account-sidebar-card">
                    <nav class="account-nav-list">
                        <a href="{{ route('store.account.orders') }}" class="account-nav-item {{ request()->routeIs('store.account.orders') ? 'active' : '' }}">
                            <span class="nav-icon"><i class="bi bi-box-seam-fill"></i></span>
                            <span class="nav-text">{{ __('طلباتي') }}</span>
                        </a>

                        <a href="{{ route('store.account.profile') }}" class="account-nav-item {{ request()->routeIs('store.account.profile') ? 'active' : '' }}">
                            <span class="nav-icon"><i class="bi bi-person-badge-fill"></i></span>
                            <span class="nav-text">{{ __('البيانات والأمان') }}</span>
                        </a>

                        <a href="{{ route('store.account.favorites') }}" class="account-nav-item {{ request()->routeIs('store.account.favorites') ? 'active' : '' }}">
                            <span class="nav-icon"><i class="bi bi-heart-fill"></i></span>
                            <span class="nav-text">{{ __('المفضلة') }}</span>
                        </a>

                        <div class="nav-divider"></div>

                        <a href="{{ route('store.about') }}#contact" class="account-nav-item">
                            <span class="nav-icon"><i class="bi bi-chat-dots-fill"></i></span>
                            <span class="nav-text">{{ __('الدعم والتواصل') }}</span>
                        </a>

                        <form action="{{ route('store.auth.logout') }}" method="POST" class="m-0 p-0 border-0">
                            @csrf
                            <button type="submit" class="account-nav-item logout-item border-0 bg-transparent">
                                <span class="nav-icon logout-icon"><i class="bi bi-box-arrow-right"></i></span>
                                <span class="nav-text">{{ __('تسجيل الخروج') }}</span>
                            </button>
                        </form>
                    </nav>
                </div>
            </aside>

            {{-- MAIN CONTENT COLUMN --}}
            <main class="account-content-col">
                @yield('account_content')
            </main>

        </div>
    </div>
</div>

<style>
/* ===== PAGE BASE ===== */
.account-page-wrapper {
    background-color: #ffffff;
    min-height: 80vh;
    font-family: 'Cairo', 'Segoe UI', sans-serif;
}
.account-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 15px;
}

/* ===== TOP HEADER ===== */
.account-top-header {
    margin-bottom: 25px;
}
html[dir="rtl"] .account-top-header { text-align: right; }
html[dir="ltr"] .account-top-header { text-align: left; }

.account-main-title {
    font-size: 32px;
    font-weight: 900;
    color: #0f172a;
    margin: 0 0 4px 0;
    letter-spacing: -0.5px;
}
.account-user-subtitle {
    font-size: 14px;
    color: #64748b;
    margin: 0;
}

/* ===== FLEX GRID LAYOUT ===== */
.account-grid-layout {
    display: flex;
    gap: 30px;
    align-items: flex-start;
}
.account-sidebar-col {
    width: 250px;
    flex-shrink: 0;
}
.account-content-col {
    flex-grow: 1;
    min-width: 0;
}

/* ===== SIDEBAR NAV LIST ===== */
.account-sidebar-card {
    background: transparent;
}
.account-nav-list {
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.account-nav-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    border-radius: 12px;
    text-decoration: none;
    color: #334155;
    font-weight: 700;
    font-size: 15px;
    transition: all 0.2s ease-in-out;
    background: transparent;
    border: none !important;
    outline: none !important;
    box-shadow: none !important;
    width: 100%;
    cursor: pointer;
}
html[dir="rtl"] .account-nav-item { text-align: right; }
html[dir="ltr"] .account-nav-item { text-align: left; }

.account-nav-item:hover {
    background: #f1f5f9;
    color: #0f172a;
}
.account-nav-item.active {
    background: #fef2f2;
    color: #EE1E26;
    font-weight: 800;
}
.account-nav-item.active .nav-icon {
    color: #EE1E26;
}
.nav-icon {
    font-size: 18px;
    color: #64748b;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    flex-shrink: 0;
}
.nav-text {
    flex-grow: 1;
}
.nav-divider {
    height: 1px;
    background: #e2e8f0;
    margin: 10px 0;
}
.logout-item {
    color: #dc2626 !important;
    background: transparent !important;
    border: none !important;
}
.logout-item:hover {
    background: #fef2f2 !important;
}
.logout-icon {
    color: #dc2626 !important;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 991px) {
    .account-grid-layout {
        flex-direction: column;
    }
    .account-sidebar-col {
        width: 100%;
    }
    .account-nav-list {
        flex-direction: row;
        overflow-x: auto;
        padding-bottom: 8px;
        margin-bottom: 10px;
    }
    .account-nav-item {
        white-space: nowrap;
        padding: 10px 16px;
    }
    .nav-divider {
        display: none;
    }
}
</style>
@endsection
