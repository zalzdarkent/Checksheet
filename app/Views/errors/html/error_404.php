<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="<?= base_url('logo/CBI_logo.png') ?>" type="image/x-icon">
    <link rel="stylesheet" href="<?= base_url('assets/bootstrap/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/bootstrap/icon/bootstrap-icons.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/bootstrap/icon/bootstrap-icons.min.css') ?>">
    <title>404 - Page Not Found | CBI</title>
    <style>
        :root {
            --primary-color: #273749;
            --secondary-color: #3a4d63;
            --accent-color: #e74c3c;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e6e9ef 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .error-container {
            text-align: center;
            padding: 3rem;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(39, 55, 73, 0.1);
            max-width: 600px;
            width: 90%;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            overflow: hidden;
        }

        .error-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--primary-color), #4a6b8a);
        }

        .company-logo {
            max-width: 250px;
            margin: 0 auto 2.5rem auto;
            display: block;
            filter: drop-shadow(0 4px 6px rgba(39, 55, 73, 0.1));
        }

        .error-code {
            font-size: 8rem;
            font-weight: 800;
            color: var(--primary-color);
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(39, 55, 73, 0.1);
            letter-spacing: -5px;
        }

        .error-message {
            font-size: 1.8rem;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            font-weight: 600;
        }

        .error-description {
            color: var(--secondary-color);
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 2.5rem;
        }

        .btn-home {
            padding: 0.8rem 2.5rem;
            font-size: 1.1rem;
            background: linear-gradient(135deg, var(--primary-color), #4a6b8a);
            border: none;
            border-radius: 50px;
            color: white;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(39, 55, 73, 0.3);
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(39, 55, 73, 0.4);
            color: white;
            background: linear-gradient(135deg, #4a6b8a, var(--primary-color));
        }

        .btn-home i {
            font-size: 1.2rem;
        }

        @media (max-width: 576px) {
            .error-container {
                padding: 2rem;
            }
            
            .error-code {
                font-size: 6rem;
            }
            
            .error-message {
                font-size: 1.5rem;
            }
            
            .company-logo {
                max-width: 200px;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <img src="<?= base_url('logo/CBI_logo.png') ?>" alt="Company Logo" class="company-logo">
        <div class="error-code">404</div>
        <div class="error-message">Oops! Page Not Found</div>
        <p class="error-description">The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.</p>
        <a href="<?= base_url() ?>" class="btn btn-home">
            <i class="bi bi-house-door"></i> Back to Home
        </a>
    </div>
</body>
</html>
