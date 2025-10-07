<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #232526 0%, #00bcd4 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            border-radius: 1.5rem;
            box-shadow: 0 6px 32px rgba(0,0,0,0.18);
            background: #fff;
            padding: 2.5rem 2rem;
            min-width: 340px;
        }
        .login-title {
            font-weight: 700;
            color: #232526;
        }
        .form-control, .btn {
            border-radius: 2rem;
        }
        .modern-btn-primary {
            background: linear-gradient(90deg, #232526 0%, #00bcd4 100%);
            color: #fff;
            border: none;
        }
        .modern-btn-primary:hover {
            background: linear-gradient(90deg, #00bcd4 0%, #232526 100%);
            color: #fff;
        }
        .alert {
            border-radius: 1rem;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <h1 class="login-title mb-4">Login</h1>
        {if isset($msg)}<div class="alert alert-danger">{$msg}</div>{/if}
        <form method="post">
            <div class="mb-3">
                <input type="text" class="form-control" name="username" placeholder="Username" required>
            </div>
            <div class="mb-3">
                <input type="password" class="form-control" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="btn modern-btn-primary w-100">Login</button>
        </form>
    </div>
</body>
</html>
