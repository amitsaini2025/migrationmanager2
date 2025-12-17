<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Failed</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            padding: 20px;
        }
        .error-container {
            background: white;
            padding: 60px 50px;
            border-radius: 20px;
            text-align: center;
            max-width: 550px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            animation: slideUp 0.5s ease-out;
        }
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .error-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            animation: scaleIn 0.6s ease-out 0.2s both;
        }
        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }
        .error-icon::before {
            content: '✗';
            font-size: 60px;
            color: white;
            font-weight: bold;
        }
        h1 {
            color: #991b1b;
            margin-bottom: 20px;
            font-size: 32px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        p {
            color: #4b5563;
            font-size: 17px;
            line-height: 1.7;
            margin-bottom: 15px;
        }
        .error-message {
            background: #fef3c7;
            border: 2px solid #fbbf24;
            border-radius: 12px;
            padding: 20px;
            margin: 25px 0;
            text-align: left;
        }
        .error-message p {
            margin: 0;
            color: #92400e;
            font-weight: 600;
            font-size: 15px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }
        .error-message p::before {
            content: '⚠️';
            font-size: 20px;
            flex-shrink: 0;
        }
        .reasons-box {
            background: #f9fafb;
            border-radius: 12px;
            padding: 25px;
            margin: 25px 0;
            text-align: left;
        }
        .reasons-box p {
            margin-bottom: 15px;
            font-weight: 700;
            color: #374151;
            font-size: 16px;
        }
        .reasons-box ul {
            margin: 0;
            padding-left: 25px;
            color: #6b7280;
        }
        .reasons-box li {
            margin-bottom: 10px;
            font-size: 15px;
            line-height: 1.6;
        }
        .help-box {
            background: #eff6ff;
            border-left: 4px solid #3b82f6;
            border-radius: 8px;
            padding: 20px;
            margin-top: 30px;
            text-align: left;
        }
        .help-box p {
            margin: 0;
            color: #1e40af;
        }
        .help-box p:first-child {
            font-weight: 700;
            font-size: 16px;
            margin-bottom: 10px;
        }
        .company-name {
            margin-top: 30px;
            color: #9ca3af;
            font-size: 14px;
        }
        .company-name strong {
            color: #991b1b;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon"></div>
        <h1>Verification Failed</h1>
        
        <div class="error-message">
            <p>{{ $message }}</p>
        </div>
        
        <div class="reasons-box">
            <p>Possible reasons:</p>
            <ul>
                <li>The verification link has expired (links are valid for 24 hours)</li>
                <li>The link has already been used</li>
                <li>The link is invalid or corrupted</li>
            </ul>
        </div>
        
        <div class="help-box">
            <p>Need help?</p>
            <p>Please contact Bansal Immigration office to request a new verification email.</p>
        </div>
        
        <div class="company-name">
            <strong>Bansal Immigration</strong>
        </div>
    </div>
</body>
</html>
