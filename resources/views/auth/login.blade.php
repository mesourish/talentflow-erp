<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In &bull; TalentFlow ERP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }
        .auth-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
            max-width: 960px;
            width: 100%;
            overflow: hidden;
        }
        .demo-pill {
            transition: all 0.2s ease;
            cursor: pointer;
            text-decoration: none;
        }
        .demo-pill:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="auth-card row g-0">
        <!-- Left Column: Form -->
        <div class="col-lg-6 p-4 p-md-5 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-center mb-4">
                    <span class="badge bg-primary p-2 me-2"><i class="bi bi-stars fs-5"></i></span>
                    <h3 class="fw-bold mb-0 text-dark">TalentFlow <span class="text-primary">ERP</span></h3>
                </div>
                <h5 class="fw-bold text-dark mb-1">Welcome back</h5>
                <p class="text-muted small mb-4">Sign in to your agency enterprise account to continue.</p>

                @if($errors->any())
                    <div class="alert alert-danger py-2 small mb-3">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Email address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" class="form-control bg-light border-start-0" value="{{ old('email', 'admin@demo.test') }}" required autofocus>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" class="form-control bg-light border-start-0" value="password" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input type="checkbox" name="remember" class="form-check-input" id="remember" checked>
                            <label class="form-check-label small text-muted" for="remember">Remember me</label>
                        </div>
                        <span class="small text-muted">Default password: <code>password</code></span>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                        Sign In to ERP <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                </form>
            </div>

            <div class="mt-4 pt-3 border-top text-center text-muted small">
                Hybrid Celebrity, Talent, Event & Brand Management Agency
            </div>
        </div>

        <!-- Right Column: 1-Click Demo Accounts -->
        <div class="col-lg-6 bg-light p-4 p-md-5 border-start">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-bold text-dark mb-0"><i class="bi bi-lightning-charge-fill text-warning me-1"></i> 1-Click Demo Sign-in</h6>
                <span class="badge bg-secondary-subtle text-secondary small">8 Blueprint Roles</span>
            </div>
            <p class="text-muted small mb-3">Select any pre-configured agency personnel to instantly authenticate and test role-specific workflows:</p>

            <div class="row g-2">
                @foreach($demoUsers as $u)
                    <div class="col-12 col-sm-6">
                        <a href="{{ route('quick-login', $u) }}" class="demo-pill card p-2 h-100 border text-start bg-white">
                            <div class="d-flex align-items-center">
                                <div class="badge rounded-circle bg-primary-subtle text-primary p-2 me-2">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                                <div class="overflow-hidden">
                                    <div class="fw-bold text-dark text-truncate small">{{ $u->name }}</div>
                                    <div class="text-muted text-truncate" style="font-size: 0.75rem;">{{ $u->getRoleNames()->first() }}</div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>

            <!-- <div class="card mt-3 p-3 bg-white border">
                <h6 class="fw-bold text-dark small mb-1"><i class="bi bi-info-circle text-primary me-1"></i> Sample Blueprint Scenario</h6>
                <p class="text-muted mb-0" style="font-size: 0.78rem;">
                    <strong>Brand X</strong> hires <strong>Celebrity A</strong> (INR 15,00,000 quotation) for nationwide campaigns. Follow the happy-path: CRM &rarr; Quotation &rarr; Project &rarr; Shoot Booking &rarr; Contract &rarr; Claims &rarr; Invoice &rarr; 20% Commission Payout.
                </p>
            </div> -->
        </div>
    </div>
</body>
</html>

