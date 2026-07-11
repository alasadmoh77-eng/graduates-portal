<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>رسالة اتصال جديدة</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
            color: #333333;
            margin: 0;
            padding: 20px;
            direction: rtl;
            text-align: right;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            border: 1px solid #e1e8ed;
        }
        .header {
            background: linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%);
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
        }
        .header h2 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .content {
            padding: 30px 25px;
        }
        .detail-group {
            margin-bottom: 20px;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 12px;
        }
        .detail-group:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .label {
            font-weight: 700;
            color: #1e3a8a;
            font-size: 14px;
            margin-bottom: 6px;
            display: block;
        }
        .value {
            font-size: 16px;
            color: #334155;
            line-height: 1.5;
        }
        .message-box {
            background-color: #f8fafc;
            border-right: 4px solid #1e3a8a;
            padding: 15px;
            border-radius: 0 8px 8px 0;
            font-style: italic;
            white-space: pre-line;
            margin-top: 8px;
        }
        .footer {
            background-color: #f8fafc;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            border-top: 1px solid #f1f5f9;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>بوابة الخدمات الرقمية - رسالة اتصال جديدة</h2>
        </div>
        <div class="content">
            <div class="detail-group">
                <span class="label">الاسم الكامل:</span>
                <span class="value">{{ $contactMessage->name }}</span>
            </div>
            <div class="detail-group">
                <span class="label">البريد الإلكتروني:</span>
                <span class="value"><a href="mailto:{{ $contactMessage->email }}" style="color: #2563eb; text-decoration: none;">{{ $contactMessage->email }}</a></span>
            </div>
            <div class="detail-group">
                <span class="label">الموضوع:</span>
                <span class="value">{{ $contactMessage->subject }}</span>
            </div>
            <div class="detail-group">
                <span class="label">تاريخ الإرسال:</span>
                <span class="value">{{ $contactMessage->created_at->format('Y-m-d H:i:s') }}</span>
            </div>
            <div class="detail-group">
                <span class="label">نص الرسالة:</span>
                <div class="value message-box">{{ $contactMessage->message }}</div>
            </div>
        </div>
        <div class="footer">
            هذه الرسالة أُرسلت تلقائياً من نموذج "اتصل بنا" في بوابة الخدمات الرقمية لجامعة إقليم سبأ.
        </div>
    </div>
</body>
</html>
