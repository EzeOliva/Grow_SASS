@include('pages.reports.clients.health.filter')
<div id="report-results-container">
    @include('pages.reports.clients.health.table')
</div>

<script>
window.printStageHealthReport = function () {
    var reportContainer = document.getElementById('report-results-container');
    if (!reportContainer) {
        window.print();
        return;
    }

    var printWindow = window.open('', '_blank', 'width=1200,height=800');
    var html = reportContainer.innerHTML;

    printWindow.document.write(`
        <html>
            <head>
                <title>Reporte de Salud por Etapas</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; color: #222; }
                    .card { border: 1px solid #e5e5e5; border-radius: 6px; margin-bottom: 18px; overflow: hidden; }
                    .card-header { background: #f8f9fa; padding: 12px 14px; }
                    .card-body { padding: 0; }
                    table { width: 100%; border-collapse: collapse; }
                    th, td { border: 1px solid #eaeaea; padding: 8px 10px; vertical-align: top; font-size: 12px; }
                    th { background: #f7f7f7; text-align: left; }
                    .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 11px; color: #fff; }
                    .badge-success { background: #28a745; }
                    .badge-warning { background: #f0ad4e; }
                    .badge-danger { background: #dc3545; }
                    .badge-info { background: #17a2b8; }
                    ul { margin: 0; padding-left: 16px; }
                    a { color: #2a6db0; text-decoration: none; }
                </style>
            </head>
            <body>
                <h2>Reporte de Salud por Etapas</h2>
                ${html}
            </body>
        </html>
    `);

    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
}
</script>
