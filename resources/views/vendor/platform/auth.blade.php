@extends('platform::app')

@section('body')
    <div class="admin-auth">
        <div class="admin-auth-bg" aria-hidden="true"></div>

        <div class="container">
            <div class="admin-auth-shell">
                <a class="admin-auth-brand" href="{{ url('/') }}" aria-label="{{ $siteName ?? 'AVENUE' }}">
                    <span class="admin-auth-badge" aria-hidden="true">A</span>
                    <span class="admin-auth-name">{{ $siteName ?? 'AVENUE' }}</span>
                </a>

                <div class="admin-auth-card">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <style>
        .admin-auth{
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            padding: 2.25rem 0;
        }

        .admin-auth-bg{
            position: absolute;
            inset: 0;
            background:
                radial-gradient(900px 450px at 15% 15%, rgba(240, 194, 75, .16), transparent 60%),
                radial-gradient(700px 380px at 85% 35%, rgba(240, 194, 75, .10), transparent 55%),
                linear-gradient(135deg, #0f1115 0%, #151821 45%, #0f1115 100%);
        }

        .admin-auth-shell{
            position: relative;
            margin: 0 auto;
            max-width: 520px;
            width: 100%;
        }

        .admin-auth-brand{
            display: inline-flex;
            align-items: center;
            gap: .6rem;
            text-decoration: none;
            margin-bottom: 1rem;
        }

        .admin-auth-badge{
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: linear-gradient(135deg, #f7d86d, #e2b93e 65%, #b8851d);
            color: #17130b;
            font-weight: 800;
            font-family: "Cinzel", ui-serif, Georgia, serif;
            box-shadow: 0 10px 28px rgba(240, 194, 75, .22);
        }

        .admin-auth-name{
            color: rgba(255, 255, 255, .92);
            font-weight: 800;
            letter-spacing: .12em;
            font-family: "Cinzel", ui-serif, Georgia, serif;
        }

        .admin-auth-card{
            border-radius: 16px;
            background: rgba(18, 18, 20, .82);
            border: 1px solid rgba(240, 194, 75, .18);
            backdrop-filter: blur(16px) saturate(150%);
            box-shadow: 0 30px 70px rgba(0, 0, 0, .45);
            padding: 1.25rem;
        }

        .admin-auth-card h1,
        .admin-auth-card h2,
        .admin-auth-card h3{
            color: rgba(255,255,255,.95);
        }

        .admin-auth-card .form-label{
            color: rgba(255,255,255,.78);
            font-size: .9rem;
        }

        .admin-auth-card .form-control{
            border-radius: 12px;
            border: 1px solid rgba(240, 194, 75, .18);
            background: rgba(255,255,255,.06);
            color: rgba(255,255,255,.92);
            padding: .7rem .85rem;
        }

        .admin-auth-card .form-control:focus{
            box-shadow: 0 0 0 .2rem rgba(240, 194, 75, .20);
            border-color: rgba(240, 194, 75, .45);
        }

        .admin-auth-card .form-check-label{
            color: rgba(255,255,255,.78);
        }

        .admin-auth-card .btn-admin-auth{
            border-radius: 12px;
            background: linear-gradient(135deg, #f7d86d, #e2b93e 65%, #b8851d);
            border: none;
            color: #17130b;
            font-weight: 800;
            padding: .75rem 1rem;
            width: 100%;
            transition: transform .15s ease, filter .15s ease, box-shadow .15s ease;
            box-shadow: 0 16px 36px rgba(240, 194, 75, .18);
        }

        .admin-auth-card .btn-admin-auth:hover{
            filter: brightness(1.02) saturate(1.05);
            transform: translateY(-1px);
        }

        .admin-auth-card .btn-admin-auth:active{
            transform: translateY(0);
        }

        @media (max-width: 575.98px){
            .admin-auth{
                padding: 1.25rem 0;
            }
            .admin-auth-card{
                padding: 1rem;
            }
        }
    </style>
@endsection
