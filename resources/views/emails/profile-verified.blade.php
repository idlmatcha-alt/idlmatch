<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Impact, Haettenschweiler, 'Arial Narrow Bold', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #881337, #C2185B);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            background: #f9fafb;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .success-icon {
            font-size: 48px;
            text-align: center;
            margin: 20px 0;
        }
        .button {
            display: inline-block;
            background: #881337;
            color: white !important;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: bold;
        }
        .button:hover {
            background: #C2185B;
        }
        .info-box {
            background: white;
            border-left: 4px solid #881337;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .footer {
            text-align: center;
            color: #666;
            font-size: 12px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎉 Profile Verified!</h1>
    </div>
    
    <div class="content">
        <div class="success-icon">✅</div>
        
        <p>Dear {{ $user->first_name }},</p>
        
        <p><strong>Great news!</strong> Your profile has been successfully verified by our team.</p>
        
        <div class="info-box">
            <p style="margin: 0;"><strong>What's Next?</strong></p>
            <ul style="margin: 10px 0;">
                <li>Your profile is now visible to other members</li>
                <li>You can start browsing and connecting with matches</li>
                <li>Complete your profile to increase your chances of finding the perfect match</li>
            </ul>
        </div>
        
        <p>Your profile ID: <strong>{{ $profile->display_id }}</strong></p>
        
        <div style="text-align: center;">
            <a href="{{ route('profiles.show', $profile->display_id) }}" class="button">
                View My Profile
            </a>
        </div>
        
        <p>If you have any questions or need assistance, feel free to reach out to our support team.</p>
        
        <p>Best regards,<br>
        <strong>The Ideal Marriage Center Team</strong></p>
    </div>
    
    <div class="footer">
        <p>© {{ date('Y') }} Ideal Marriage Center. All rights reserved.</p>
        <p>This is an automated message. Please do not reply to this email.</p>
    </div>
</body>
</html>
