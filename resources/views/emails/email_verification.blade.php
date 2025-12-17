<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #2d3748;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .email-header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
            position: relative;
        }
        .email-header::before {
            content: '✉️';
            font-size: 60px;
            display: block;
            margin-bottom: 15px;
        }
        .email-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        .email-body {
            padding: 45px 35px;
        }
        .email-body p {
            margin-bottom: 20px;
            font-size: 16px;
            color: #4a5568;
        }
        .email-highlight {
            color: #4f46e5;
            font-weight: 600;
        }
        .verify-button {
            display: inline-block;
            padding: 18px 45px;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white !important;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 17px;
            text-align: center;
            margin: 25px 0;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.4);
        }
        .verify-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.5);
        }
        .button-container {
            text-align: center;
            margin: 35px 0;
        }
        .alternative-link {
            margin-top: 35px;
            padding: 20px;
            background: #f7fafc;
            border: 2px dashed #cbd5e0;
            border-radius: 8px;
            font-size: 13px;
            word-break: break-all;
        }
        .alternative-link p {
            margin: 0 0 10px 0;
            color: #4a5568;
            font-weight: 600;
        }
        .alternative-link a {
            color: #4f46e5;
            text-decoration: none;
        }
        .email-footer {
            padding: 25px 35px;
            background: #f7fafc;
            text-align: center;
            font-size: 14px;
            color: #718096;
            border-top: 1px solid #e2e8f0;
        }
        .warning-box {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 16px 20px;
            margin-top: 30px;
            border-radius: 8px;
        }
        .warning-box p {
            margin: 0;
            color: #92400e;
            font-size: 14px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }
        .warning-icon {
            font-size: 20px;
            flex-shrink: 0;
        }
        .info-box {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 10px;
            padding: 20px;
            margin: 25px 0;
        }
        .info-box p {
            margin: 0 0 12px 0;
            color: #1e40af;
            font-weight: 700;
            font-size: 15px;
        }
        .info-box ul {
            margin: 0;
            padding-left: 22px;
            color: #1e3a8a;
        }
        .info-box li {
            margin-bottom: 8px;
            font-size: 14px;
        }
        .info-box li strong {
            color: #1e40af;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>📧 Verify Your Email Address</h1>
        </div>
        
        <div class="email-body">
            <p>Hello,</p>
            
            <p>Thank you for providing your email address <strong class="email-highlight">{{ $clientEmail->email }}</strong> to <strong>Bansal Immigration</strong>.</p>
            
            <p>To complete your email verification and ensure we can communicate with you effectively, please click the button below:</p>
            
            <div class="button-container">
                <a href="{{ $verificationUrl }}" class="verify-button">
                    ✓ Verify My Email Address
                </a>
            </div>
            
            <div class="info-box">
                <p style="margin: 0;"><strong>Important:</strong></p>
                <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                    <li>This link expires in <strong>{{ $expiresAt->diffForHumans() }}</strong> ({{ $expiresAt->format('M j, Y g:i A') }})</li>
                    <li>This link can only be used once</li>
                    <li>If you didn't request this verification, please ignore this email</li>
                </ul>
            </div>
            
            <div class="alternative-link">
                <p><strong>If the button doesn't work, copy and paste this link into your browser:</strong></p>
                <p><a href="{{ $verificationUrl }}" style="color: #4f46e5;">{{ $verificationUrl }}</a></p>
            </div>
            
            <div class="warning-box">
                <p>
                    <span class="warning-icon">⚠️</span>
                    <span>Never share this link with anyone. Our staff will never ask you for this link.</span>
                </p>
            </div>
        </div>
        
        <div class="email-footer">
            <p>This is an automated email from <strong>Bansal Immigration</strong>.</p>
            <p>If you have any questions, please contact our office.</p>
            <p style="margin-top: 15px; font-size: 12px;">
                © {{ date('Y') }} Bansal Immigration. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
