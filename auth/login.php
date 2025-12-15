<!DOCTYPE html>
<html lang="en">

<head>
    
</head>

<body>
    <div class="auth-wrapper">
        <div class="auth-card">

            

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