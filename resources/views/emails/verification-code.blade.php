<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .code {
            font-size: 32px;
            font-weight: bold;
            color: #4F46E5;
            letter-spacing: 5px;
            text-align: center;
            padding: 20px;
            background: #F3F4F6;
            border-radius: 8px;
            margin: 20px 0;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #E5E7EB;
            font-size: 12px;
            color: #6B7280;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Hello {{ $firstName }},</h2>

        <p>Thank you for registering with our Healthcare Platform.</p>

        <p>Your verification code is:</p>

        <div class="code">{{ $code }}</div>

        <p>This code will expire in {{ App\Utilities\Constants::EXPIRATION_VERIFICATION_CODE_TIME_IN_MINUTES}} minutes.
        </p>

        <p>If you didn't request this code, please ignore this email.</p>

        <div class="footer">
            <p>This is an automated message, please do not reply.</p>
        </div>
    </div>
</body>

</html>