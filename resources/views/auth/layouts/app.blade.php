<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - {{ config('app.name') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Scripts & Styles -->
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/6-all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth-v2.css?v=' . time()) }}">
</head>
<body>
    <div class="auth-container">
        <!-- Left Panel: Form -->
        <div class="auth-form-panel">
            <div class="auth-form-content">
                @yield('content')
            </div>

            <div class="auth-footer">
                <div>
                    <a href="#">Terms & Conditions</a>
                </div>
            </div>
        </div>

        <!-- Right Panel: Inset Panel with Background and Widgets -->
        <div class="auth-image-panel">
            <div class="auth-image-panel-inner">
                <!-- Top Row: Logo and Review Card Aligned -->
                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem;">
                    <!-- Logo -->
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo-right" style="margin-bottom: 0;">

                    <!-- Weekly Review Widget -->
                    <div class="glass-widget" style="width: 220px; position: relative; top: 0; right: 0;">
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                            <div style="width: 8px; height: 8px; background: #FFD25D; border-radius: 50%;"></div>
                            <span style="font-size: 0.7rem; font-weight: 600; text-transform: uppercase; color: #FFD25D;">Weekly Review</span>
                        </div>
                        <div style="font-size: 0.85rem; font-weight: 500;">Asset compliance audit</div>
                        <div style="font-size: 0.7rem; opacity: 0.8; margin-top: 0.25rem;">Ongoing • 11:30 AM</div>
                    </div>
                </div>

                <!-- Features List in Container - Shifted down via CSS margin-top: auto -->
                <div class="features-container">
                    <div style="color: white; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; margin-bottom: 1rem; opacity: 0.8;">Platform Features</div>
                    <ul class="features-list">
                        <li class="feature-item">
                            <i class="fa fa-circle-check feature-icon"></i>
                            Unified Asset Management
                        </li>
                        <li class="feature-item">
                            <i class="fa fa-circle-check feature-icon"></i>
                            Regional Court Registry
                        </li>
                        <li class="feature-item">
                            <i class="fa fa-circle-check feature-icon"></i>
                            Secure Judicial Records
                        </li>
                    </ul>
                </div>

                <!-- Unified Information Card at the bottom -->
                <div style="margin-top: auto; background: rgba(255,255,255,0.9); padding: 1.5rem; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                    <div class="info-card-title">Judicial Service - Inventory Management System</div>
                    <div style="font-size: 0.85rem; color: #757575;">Smart Infrastructure Management System</div>
                </div>
            </div>
        </div>
    </div>
            </div>
        </div>
    </div>
</body>
</html>
