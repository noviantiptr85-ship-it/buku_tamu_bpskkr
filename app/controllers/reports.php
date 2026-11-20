<?php
declare(strict_types=1);

function report_filters(): array
{
    $from = valid_date($_GET['from'] ?? null) ? (string) $_GET['from'] : date('Y-m-01');
    $to = valid_date($_GET['to'] ?? null) ? (string) $_GET['to'] : today();

    if ($from > $to) {
        [$from, $to] = [$to, $from];
    }

    $q = trim((string) ($_GET['q'] ?? ''));
    $status = in_array($_GET['status'] ?? '', ['aktif', 'selesai'], true) ? (string) $_GET['status'] : '';

    return ['from' => $from, 'to' => $to, 'q' => $q, 'status' => $status];
}

function report_data(array $filters): array
{
    return [
        'visits' => Visit::search($filters),
        'summary' => Visit::summary($filters['from'], $filters['to']),
        'top' => Visit::topInstitutions($filters['from'], $filters['to'], 5),
        'trend' => Visit::dailyTrend($filters['from'], $filters['to']),
    ];
}

function report_index(): void
{
    require_login();

    $filters = report_filters();
    $data = report_data($filters);

    view('reports/index', [
        'title' => 'Laporan Kunjungan',
        'filters' => $filters,
        'visits' => $data['visits'],
        'summary' => $data['summary'],
        'top' => $data['top'],
        'trend' => $data['trend'],
    ]);
}

function report_rows(array $visits): array
{
    $rows = [];
    $no = 1;

    foreach ($visits as $visit) {
        $minutes = '-';
        if ($visit['check_out_at']) {
            $minutes = (string) max(0, (int) round((strtotime($visit['check_out_at']) - strtotime($visit['check_in_at'])) / 60));
        }

        $rows[] = [
            $no++,
            $visit['visit_no'],
            $visit['name'],
            $visit['institution'],
            phone_for_view($visit['phone']),
            $visit['notes'],
            date('d/m/Y H:i', strtotime($visit['check_in_at'])),
            $visit['check_out_at'] ? date('d/m/Y H:i', strtotime($visit['check_out_at'])) : '-',
            $minutes,
            $visit['status'] === 'selesai' ? 'Selesai' : 'Aktif',
            $visit['source'] === 'manual' ? 'Manual' : 'Kiosk',
        ];
    }

    return $rows;
}

function report_export(): void
{
    require_login();

    $filters = report_filters();
    $visits = Visit::search($filters);
    $rows = report_rows($visits);
    $format = ($_GET['format'] ?? 'csv') === 'xls' ? 'xls' : 'csv';
    $filename = 'buku-tamu-' . $filters['from'] . '-sd-' . $filters['to'];

    $headers = [
        'No', 'Nomor Kunjungan', 'Nama', 'Instansi/Asal', 'No HP', 'Keterangan',
        'Jam Masuk', 'Jam Keluar', 'Durasi (menit)', 'Status', 'Sumber',
    ];

    if ($format === 'csv') {
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '.csv"');

        $out = fopen('php://output', 'wb');
        fwrite($out, "\xEF\xBB\xBF");
        fputcsv($out, $headers, ',', '"', '');

        foreach ($rows as $row) {
            $row = array_map(static function ($value): string {
                $value = (string) $value;
                if ($value !== '' && in_array($value[0], ['=', '+', '-', '@'], true)) {
                    return "'" . $value;
                }
                return $value;
            }, $row);
            fputcsv($out, $row, ',', '"', '');
        }

        fclose($out);
        exit;
    }

    header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '.xls"');

    echo "\xEF\xBB\xBF";
    echo '<html><head><meta charset="UTF-8"><style>
        table{border-collapse:collapse;font-family:Arial,sans-serif;font-size:12px}
        th,td{border:1px solid #999;padding:6px 8px}
        th{background:#F58220;color:#fff}
        h2{font-family:Arial,sans-serif}
    </style></head><body>';
    echo '<h2>Buku Tamu BPS Kabupaten Kubu Raya</h2>';
    echo '<p>Periode: ' . e(tanggal_id($filters['from'], false)) . ' s.d. ' . e(tanggal_id($filters['to'], false)) . '</p>';
    echo '<table><thead><tr>';
    foreach ($headers as $header) {
        echo '<th>' . e($header) . '</th>';
    }
    echo '</tr></thead><tbody>';
    foreach ($rows as $row) {
        echo '<tr>';
        foreach ($row as $value) {
            echo '<td>' . e((string) $value) . '</td>';
        }
        echo '</tr>';
    }
    echo '</tbody></table></body></html>';
    exit;
}

function report_print(): void
{
    require_login();

    $filters = report_filters();
    $data = report_data($filters);

    view('reports/print', [
        'title' => 'Cetak Laporan',
        'filters' => $filters,
        'visits' => $data['visits'],
        'summary' => $data['summary'],
    ], 'layout_print');
}
