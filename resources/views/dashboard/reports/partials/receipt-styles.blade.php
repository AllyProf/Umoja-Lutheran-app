<style>
.receipt-report-container {
    max-width: 800px;
    margin: 30px auto;
    background: #fff;
    padding: 30px;
    border: 1px solid #ddd;
    position: relative;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.receipt-report-container::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) rotate(-45deg);
    width: 500px;
    height: 500px;
    background-image: url('{{ asset('royal-master/image/logo/Logo.png') }}');
    background-repeat: no-repeat;
    background-position: center;
    background-size: contain;
    opacity: 0.12;
    z-index: 0;
    pointer-events: none;
    filter: grayscale(100%) brightness(1.2);
}

.receipt-report-container > * {
    position: relative;
    z-index: 1;
}

.receipt-report-header {
    text-align: center;
    border-bottom: 3px solid #e07632;
    padding-bottom: 20px;
    margin-bottom: 30px;
}

.receipt-report-header .logo-container {
    margin-bottom: 15px;
}

.receipt-report-header .logo-container img {
    max-height: 80px;
    max-width: 300px;
    height: auto;
    width: auto;
}

.receipt-report-header h1 {
    color: #e07632;
    font-size: 28px;
    margin-bottom: 5px;
    font-weight: bold;
}

.receipt-report-header p {
    color: #666;
    font-size: 14px;
    margin: 2px 0;
}

.receipt-report-title {
    text-align: center;
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 30px;
    color: #333;
}

.receipt-report-number {
    text-align: right;
    margin-bottom: 20px;
    color: #666;
    font-size: 11px;
}

.print-button-section {
    text-align: center;
    margin: 30px 0;
}

.print-button-section button {
    background-color: #e07632;
    color: #fff;
    border: none;
    padding: 12px 30px;
    font-size: 14px;
    cursor: pointer;
    border-radius: 4px;
    font-weight: bold;
    margin: 0 5px;
}

.print-button-section button:hover {
    background-color: #c86528;
}

.receipt-info-section {
    margin-bottom: 25px;
}

.receipt-info-section h3 {
    color: #e07632;
    font-size: 14px;
    border-bottom: 2px solid #e07632;
    padding-bottom: 8px;
    margin-bottom: 15px;
    font-weight: bold;
}

.receipt-info-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    padding: 5px 0;
}

.receipt-info-label {
    font-weight: bold;
    color: #555;
    width: 45%;
}

.receipt-info-value {
    color: #333;
    width: 55%;
    text-align: right;
}

.receipt-two-column {
    display: flex;
    gap: 30px;
    margin-bottom: 25px;
}

.receipt-column {
    flex: 1;
}

.receipt-details-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

.receipt-details-table th {
    background-color: #f8f9fa;
    color: #333;
    padding: 12px;
    text-align: left;
    border: 1px solid #ddd;
    font-weight: bold;
    font-size: 11px;
}

.receipt-details-table td {
    padding: 12px;
    border: 1px solid #ddd;
    font-size: 11px;
}

.receipt-details-table tbody tr:nth-child(even) {
    background-color: #f9f9f9;
}

.receipt-details-table .total-row {
    background-color: #f8f9fa !important;
    font-weight: bold;
}

.receipt-details-table .total-row td {
    font-size: 13px;
    padding: 15px 12px;
}

.receipt-footer {
    margin-top: 40px;
    padding-top: 20px;
    border-top: 1px solid #ddd;
    text-align: center;
    color: #666;
    font-size: 10px;
}

.receipt-footer p {
    margin: 5px 0;
}

.receipt-footer .powered-by {
    color: #940000;
    font-weight: bold;
    margin-top: 15px;
    font-size: 9px;
}

.amount-highlight {
    font-size: 20px;
    color: #e07632;
    font-weight: bold;
}

.text-right {
    text-align: right;
}

.recommendation-box {
    padding: 15px;
    margin-bottom: 15px;
    border-left: 4px solid;
    border-radius: 4px;
    background-color: #f8f9fa;
}

.recommendation-box.success {
    border-left-color: #28a745;
    background-color: #d4edda;
}

.recommendation-box.warning {
    border-left-color: #ffc107;
    background-color: #fff3cd;
}

.recommendation-box.danger {
    border-left-color: #dc3545;
    background-color: #f8d7da;
}

.recommendation-box.info {
    border-left-color: #17a2b8;
    background-color: #d1ecf1;
}

.recommendation-box h4 {
    margin: 0 0 8px 0;
    font-size: 14px;
    font-weight: bold;
    display: flex;
    align-items: center;
    gap: 8px;
}

.recommendation-box p {
    margin: 0;
    font-size: 12px;
    color: #333;
}

/* Print Styles */
@media print {
    @page {
        margin: 10mm !important;
        size: A4;
    }
    
    .app-header,
    .app-sidebar,
    .app-sidebar__overlay,
    .app-title,
    .app-breadcrumb,
    .report-filter-section,
    .report-filter-section *,
    body > *:not(.app-content),
    .app-content > *:not(.receipt-report-container) {
        display: none !important;
    }
    
    .print-button-section {
        display: none !important;
    }
    
    body {
        background: white !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    
    .app-content {
        margin: 0 !important;
        padding: 0 !important;
    }
    
    .receipt-report-container {
        display: block !important;
        visibility: visible !important;
        border: none !important;
        padding: 10px 15px !important;
        margin: 0 !important;
        box-shadow: none !important;
        max-width: 100% !important;
        page-break-inside: avoid !important;
        background: white !important;
    }
    
    .receipt-report-header h1 {
        font-size: 22px !important;
    }
    
    .receipt-report-header p {
        font-size: 11px !important;
    }
    
    .receipt-report-title {
        font-size: 16px !important;
    }
    
    .receipt-info-section h3 {
        font-size: 12px !important;
        page-break-after: avoid !important;
    }
    
    .receipt-info-row {
        font-size: 10px !important;
    }
    
    .receipt-details-table {
        font-size: 9px !important;
        page-break-inside: auto !important;
    }
    
    .receipt-details-table thead {
        display: table-header-group !important;
    }
    
    .receipt-details-table th,
    .receipt-details-table td {
        padding: 6px 4px !important;
        font-size: 9px !important;
    }
    
    .receipt-details-table tr {
        page-break-inside: avoid !important;
        page-break-after: auto !important;
    }
    
    .receipt-report-container::before {
        opacity: 0.08 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    
    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
}

/* Mobile Responsiveness */
@media screen and (max-width: 600px) {
    .receipt-report-container {
        padding: 15px;
        margin: 10px auto;
        border: none;
        box-shadow: none;
    }
    
    .receipt-report-header h1 {
        font-size: 20px;
    }
    
    .receipt-report-header p {
        font-size: 11px;
    }
    
    .receipt-report-header .logo-container img {
        max-height: 60px;
    }
    
    .receipt-two-column {
        flex-direction: column;
        gap: 20px;
    }
    
    .receipt-info-row {
        flex-wrap: wrap;
    }
    
    .receipt-info-label {
        width: 100%;
        text-align: left;
        margin-bottom: 2px;
    }
    
    .receipt-info-value {
        width: 100%;
        text-align: left;
        font-weight: bold;
    }
    
    .receipt-report-title {
        font-size: 16px;
    }
    
    .print-button-section button {
        width: 100%;
        margin: 5px 0;
    }
    
    .receipt-details-table {
        display: block;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    .receipt-report-container::before {
        width: 300px;
        height: 300px;
    }
    
    .receipt-report-number {
        text-align: center;
        margin-bottom: 15px;
    }
}
</style>
