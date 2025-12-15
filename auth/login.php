<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Coffee House</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <div class="auth-wrapper">
        <div class="auth-card">

            <div class="mb-4">
                <i class="fas fa-mug-hot fa-3x" style="color: #6c5ce7;"></i>
                <h1 class="brand-title mt-2">Coffee House</h1>
                <p class="auth-subtitle">Welcome back! Please login.</p>
            </div>

            <form action="validation.php" method="post">

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> Registration successful!
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php
                        if ($_GET['error'] == 'invalid')
                            echo 'Invalid username or password!';
                        if ($_GET['error'] == 'nouser')
                            echo 'Account does not exist!';
                        ?>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <input type="text" name="user" class="form-control rounded-pill" placeholder="Username" required>
                </div>

                <div class="form-group">
                    <input type="password" name="password" class="form-control rounded-pill" placeholder="Password"
                        required>
                </div>

                <div class="form-group text-left pl-3">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" name="remember_me" id="rememberCheck">
                        <label class="custom-control-label text-muted" for="rememberCheck">Remember me</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-auth mt-2">
                    LOGIN <i class="fas fa-arrow-right ml-2"></i>
                </button>

            </form>

            <div class="mt-4 text-muted" style="font-size: 0.9rem;">
                Don't have an account?
                <a href="register.php" class="auth-link ml-1">Register Here</a>
            </div>

        </div>
    </div>

</body>

</html>
