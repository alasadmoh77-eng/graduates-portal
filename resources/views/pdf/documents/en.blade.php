<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            padding: 50px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #1a237e;
            padding-bottom: 10px;
            margin-bottom: 30px;
        }
        .header h1 { color: #1a237e; margin-bottom: 5px; }
        .document-title {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 40px;
            text-transform: uppercase;
        }
        .content { font-size: 18px; line-height: 1.8; }
        .field { margin-bottom: 15px; }
        .footer {
            margin-top: 50px;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        .qr-section { text-align: right; position: absolute; bottom: 50px; right: 50px; }
        .qr-section img { width: 120px; }
        .signature-section { text-align: left; position: absolute; bottom: 50px; left: 50px; }
        .verification-info {
            position: absolute;
            bottom: 20px;
            width: 100%;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Sabaa Region University</h1>
        <h3>Office of Students Affairs - Admission & Registration</h3>
    </div>

    <div class="document-title">
        {{ $request->documentType->name_en }}
    </div>

    <div class="content">
        <div class="field">This is to certify that Mr/Ms: <strong>{{ $request->user->name }}</strong></div>
        <div class="field">Has successfully completed the requirements for the Bachelor's degree in: <strong>{{ $request->user->graduate->major->name_en }}</strong></div>
        <div class="field">Graduation Class of: <strong>{{ $request->user->graduate->graduation_year }}</strong></div>
        <div class="field">Tracking Code: {{ $request->tracking_code }}</div>
        <div class="field">Serial Number: {{ $serial_number }}</div>
        <div class="field">Date of Issue: {{ $issue_date }}</div>
    </div>

    <div class="signature-section">
        <p>University Registrar's Signature & Stamp</p>
        <div style="height: 80px;"></div>
        <p>........................................</p>
    </div>

    <div class="qr-section">
        <img src="data:image/svg+xml;base64,{{ $qr_code }}" alt="QR Code">
        <p style="font-size: 10px; margin-top: 5px;">Scan to verify document validity</p>
    </div>

    <div class="verification-info">
        Verify this document online at: {{ config('app.url') }}/verify/{{ $qr_token }}
    </div>
</body>
</html>
