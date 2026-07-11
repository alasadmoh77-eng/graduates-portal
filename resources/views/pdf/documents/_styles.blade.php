<style>
    @page {
        size: A4 portrait;
        margin: 5mm 6mm;
    }

    * {
        box-sizing: border-box;
    }

    @font-face {
        font-family: 'PdfArabic';
        src: url('C:/dompdf_fonts/Amiri-Regular.ttf') format('truetype'),
             url('{{ storage_path("fonts/Amiri-Regular.ttf") }}') format('truetype');
        font-weight: normal;
        font-style: normal;
    }
    @font-face {
        font-family: 'PdfArabic';
        src: url('C:/dompdf_fonts/Amiri-Bold.ttf') format('truetype'),
             url('{{ storage_path("fonts/Amiri-Bold.ttf") }}') format('truetype');
        font-weight: bold;
        font-style: normal;
    }

    body, .academic-page-content,
    table, td, th, div, span, p, strong, em {
        font-family: 'PdfArabic', 'DejaVu Sans', sans-serif;
    }

    body, .academic-page-content {
        color: #000000;
        margin: 0;
        padding: 0;
        line-height: 1.3;
        font-size: 9.5px;
        direction: rtl;
        text-align: right;
    }


    /* Fixed Page Border - Disabled */
    .page-border {
        display: none;
    }

    /* Document Body Container Wrapper */
    .doc-body-container {
        border: 1.5px solid #000000;
        padding: 3mm 0;
        margin-bottom: 1mm;
        box-sizing: border-box;
    }

    .doc-content-wrapper {
        margin: 0 4mm;
    }

    /* Page border wrapper */
    .page-wrap {
        position: relative;
        background-color: #ffffff;
        padding: 2.5mm 3.5mm 2mm;
    }

    /* Header Table */
    .header-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 0.8mm;
        direction: ltr !important;
    }

    .header-table td {
        vertical-align: middle;
    }

    .header-right {
        text-align: right;
        font-size: 9.5px;
        font-weight: bold;
        line-height: 1.15;
        width: 38%;
        direction: rtl;
    }

    .header-center {
        text-align: center;
        width: 24%;
        direction: rtl;
    }

    .header-left {
        text-align: left;
        width: 38%;
        direction: rtl;
    }

    .uni-logo {
        height: 42px;
        width: auto;
        display: block;
        margin: 0 auto 1px;
    }

    .uni-name-under-logo {
        font-size: 9.5px;
        font-weight: bold;
        text-align: center;
        margin-top: 1px;
    }

    /* doc-meta-box styling */
    .doc-meta-box {
        width: 196px;
        border: 1.3px solid #000;
        border-collapse: collapse;
        table-layout: fixed;
        font-size: 7.5px;
        line-height: 1.4;
        background: #fff;
        direction: ltr;
        margin-left: 0;
        margin-right: auto;
    }

    .doc-meta-box td {
        border: none;
        padding: 4px 5px;
        vertical-align: middle;
        white-space: nowrap;
    }

    .doc-meta-box .meta-label {
        width: 42px;
        text-align: right;
        direction: rtl;
        font-weight: bold;
        background-color: #f1f5f9;
    }

    .doc-meta-box .meta-value {
        width: 150px;
        text-align: left;
        direction: ltr;
        unicode-bidi: isolate;
        font-family: 'DejaVu Sans', sans-serif;
        font-size: 7px;
        overflow: hidden;
    }

    /* Separate QR block in the header (sits to the right of the meta box,
       in the empty space between the metadata box and the centered title) */
    .pdf-header-qr-box {
        border: 1px solid #000000;
        padding: 2px;
        background: #fff;
        display: inline-block;
        text-align: center;
        line-height: 0;
    }

    /* Document Title */
    .header-doc-title {
        font-size: 13px;
        font-weight: bold;
        text-align: center;
        margin-top: 5px;
        margin-bottom: 5px;
        display: block;
    }

    /* Student Information Panel */
    .student-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #000000;
        margin-bottom: 0.8mm;
        font-size: 9.5px;
    }

    .student-table td {
        border: 1px solid #000000;
        padding: 2.5px 4.5px;
        vertical-align: middle;
        line-height: 1.1;
    }

    /* Introductory Paragraph */
    .intro-container {
        margin-bottom: 0.8mm;
        padding: 0 1px;
    }

    .intro-text {
        text-align: justify;
        direction: ltr; /* Fix line stacking bug in DomPDF */
        font-size: 9.5px;
        line-height: 1.15;
        margin: 0;
        border: 1px solid #000000;
        padding: 3px 5px;
        background-color: #fdfdfd;
    }

    /* Level Blocks and Semesters */
    .level-block {
        margin-bottom: 0.6mm;
        page-break-inside: avoid;
    }

    .level-header-bar {
        width: 100%;
        border-collapse: collapse;
        border: 1.5px solid #000000;
        margin-bottom: 0.3mm;
        font-size: 8px;
    }

    .level-header-bar td {
        border: 1px solid #000000;
        padding: 1px 3px;
        vertical-align: middle;
        text-align: right;
    }

    .sem-grid {
        width: 100%;
        border-collapse: collapse;
        direction: ltr;
        table-layout: fixed;
    }

    .sem-grid td {
        width: 50%;
        vertical-align: top;
    }

    .sem-cell {
        direction: rtl;
        text-align: right;
    }

    .first-sem {
        padding-left: 2px;
    }

    .second-sem {
        padding-right: 2px;
    }

    .sem-title {
        font-size: 8px;
        font-weight: bold;
        text-align: center;
        margin-bottom: 0.1mm;
        background-color: #f1f5f9;
        border: 1px solid #000000;
        padding: 0.3px 0;
        line-height: 1.0;
    }

    .subjects-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 7px;
        direction: ltr !important;
        table-layout: fixed;
    }

    .subjects-table th {
        border: 1px solid #000000;
        padding: 0.6px 2px;
        text-align: center;
        font-weight: bold;
        background: #e2e8f0;
        font-size: 7.5px;
        line-height: 1.0;
    }

    .subjects-table td {
        border: 1px solid #000000;
        padding: 0.6px 2px;
        text-align: center;
        height: 10.5px; /* Compact height */
        line-height: 1.0;
        font-size: 7px;
    }

    .subjects-table .subj-name {
        text-align: right;
        font-weight: bold;
        direction: rtl;
    }

    .sem-footer-row {
        font-weight: bold;
        background: #f1f5f9;
    }

    /* Signatures Table */
    .sig-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        direction: ltr;
        margin-top: 2px;
        page-break-inside: auto;
        page-break-before: auto;
    }

    .sig-table td {
        width: 25%;
        text-align: center;
        vertical-align: bottom;
        direction: rtl;
        padding: 4px 3px;
    }

    .sig-title {
        font-weight: bold;
        font-size: 9px;
        min-height: 16px;
        margin-bottom: 4px;
        line-height: 1.3;
    }

    .sig-line {
        border-top: 1px solid #333;
        width: 75%;
        margin: 2px auto 0;
    }

    .sig-img {
        max-height: 40px;
        max-width: 90px;
        display: block;
        margin: 0 auto 1px;
        object-fit: contain;
    }

    .sig-signer-name {
        font-size: 7px;
        font-weight: bold;
        display: block;
        text-align: center;
        margin-top: 1px;
    }

    .sig-date {
        font-size: 6px;
        color: #666;
        display: block;
        text-align: center;
    }

    .sig-pending {
        font-size: 7px;
        color: #999;
        font-style: italic;
        display: block;
        text-align: center;
        margin-top: 6px;
    }

    /* Footer Section */
    .footer-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 0.5mm;
    }

    .footer-table td {
        vertical-align: middle;
        padding-left: 3.5mm;
        padding-right: 3.5mm;
    }

    .qr-container {
        border: 1px solid #000000;
        padding: 3px;
        background-color: #ffffff;
        width: 190px;
        display: block;
    }

    .qr-table {
        width: 100%;
        border-collapse: collapse;
    }

    .qr-table td {
        padding: 0;
        vertical-align: middle;
    }

    .qr-img {
        width: 60px;
        height: 60px;
        display: block;
    }

    .qr-img-header {
        width: 66px;
        height: 66px;
        display: block;
        margin-left: 0;
        margin-right: 0;
    }

    .qr-text {
        font-size: 7.5px;
        font-weight: bold;
        line-height: 1.35;
        padding-right: 4px;
    }

    .verify-text {
        font-size: 8.5px;
        font-weight: bold;
        text-align: left; /* LTR aligned verification link on the left side of footer */
    }

    .url-link {
        font-family: monospace;
        font-size: 7.5px;
        color: #000000;
        text-decoration: none;
    }

    /* LTR Support overrides */
    .ltr, .ltr .academic-page-content {
        direction: ltr;
        text-align: left;
    }

    .ltr .header-right {
        text-align: left;
    }

    .ltr .header-left {
        text-align: right;
    }

    .ltr .date-box-table {
        float: right;
    }

    .ltr .student-table .label {
        background-color: #f8fafc;
    }

    .ltr .subjects-table .subj-name {
        text-align: left;
    }

    .ltr .verify-text {
        text-align: right;
    }
    
    .ltr .qr-text {
        padding-right: 0;
        padding-left: 3px;
        text-align: left;
    }

    .ltr .sem-grid > tr > td.first-sem {
        padding-left: 0;
        padding-right: 2px;
    }

    .ltr .sem-grid > tr > td.second-sem {
        padding-right: 0;
        padding-left: 2px;
    }

    /* Visual Boldness Enhancements for Important Elements */
    .important-label {
        font-weight: bold !important;
    }
    .important-value {
        font-weight: bold !important;
    }
    .header-doc-title {
        font-weight: bold !important;
    }
    .subjects-table th {
        font-weight: bold !important;
    }
    .level-header-bar td,
    .level-header-text {
        font-weight: bold !important;
    }
    .sig-title {
        font-weight: bold !important;
    }
    .sem-title {
        font-weight: bold !important;
    }
    .doc-meta-box .meta-value {
        font-weight: bold !important;
    }
</style>
