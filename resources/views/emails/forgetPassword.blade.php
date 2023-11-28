<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forget Password Email</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-body">
                <h1 class="card-title">Forget Password Email</h1>
                <p class="card-text">
                    You can reset your password using the link below:
                </p>
                <a href="{{ $verificationLink }}" class="btn btn-primary">Reset Password</a>
            </div>
        </div>
    </div>
</body>
</html>
