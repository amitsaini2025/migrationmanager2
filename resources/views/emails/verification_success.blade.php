<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verified Successfully</title>
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
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            padding: 20px;
        }
        .success-container {
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
        .success-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
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
        .success-icon::before {
            content: '✓';
            font-size: 60px;
            color: white;
            font-weight: bold;
        }
        h1 {
            color: #065f46;
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
        .email-display {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border: 2px solid #6ee7b7;
            padding: 18px 25px;
            border-radius: 12px;
            font-weight: 700;
            color: #065f46;
            margin: 30px 0;
            font-size: 18px;
            word-break: break-all;
        }
        .success-message {
            background: #f0fdf4;
            border-left: 4px solid #10b981;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
            text-align: left;
        }
        .success-message p {
            margin: 0;
            color: #065f46;
            font-size: 15px;
        }
        .close-button {
            margin-top: 35px;
            padding: 16px 40px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 17px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
        }
        .close-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.5);
        }
        .company-name {
            margin-top: 30px;
            color: #9ca3af;
            font-size: 14px;
        }
        .company-name strong {
            color: #065f46;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon"></div>
        <h1>Email Verified Successfully!</h1>
        <p>Your email address has been verified:</p>
        <div class="email-display">{{ $clientEmail->email }}</div>
        
        <div class="success-message">
            <p><strong>✓ Verification Complete</strong></p>
            <p style="margin-top: 10px;">You can now receive important updates and communications from Bansal Immigration.</p>
        </div>
        
        <p>Thank you for confirming your email address. You can now close this window.</p>
        <button class="close-button" onclick="window.close()">Close Window</button>
        
        <div class="company-name">
            Verified by <strong>Bansal Immigration</strong>
        </div>
    </div>
</body>
</html>
