<?php
declare(strict_types=1);

class Visit
{
    public static function find(int $id): ?array
    {
        $stmt = db()->prepare('SELECT * FROM visits WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Membuat kunjungan baru dengan nomor unik BT-YYYYMMDD-NNN.
     * Retry otomatis jika terjadi benturan nomor.
     */
    public static function create(array $data): array
    {
        $pdo = db();

        for ($attempt = 0; $attempt < 5; $attempt++) {
            try {
                $pdo->beginTransaction();

                $prefix = 'BT-' . date('Ymd') . '-';
                $stmt = $pdo->prepare('SELECT visit_no FROM visits WHERE visit_no LIKE ? ORDER BY visit_no DESC LIMIT 1 FOR UPDATE');
                $stmt->execute([$prefix . '%']);
                $last = $stmt->fetchColumn();
                $sequence = $last ? ((int) substr((string) $last, -3)) + 1 : 1;
                $visitNo = $prefix . str_pad((string) $sequence, 3, '0', STR_PAD_LEFT);

                $insert = $pdo->prepare(
                    'INSERT INTO visits (visit_no, name, institution, phone, notes, check_in_at, status, source, created_by)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
                );
                $insert->execute([
                    $visitNo,
                    $data['name'],
                    $data['institution'],
                    $data['phone'],
                    $data['notes'],
                    $data['check_in_at'] ?? date('Y-m-d H:i:s'),
                    'aktif',
                    $data['source'] ?? 'kiosk',
                    $data['created_by'] ?? null,
                ]);

                $id = (int) $pdo->lastInsertId();
                $pdo->commit();

                return self::find($id) ?? [];
            } catch (PDOException $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                if ($e->getCode() !== '23000') {
                    throw $e;
                }
                usleep(50000);
            }
        }

        throw new RuntimeException('Gagal membuat nomor kunjungan. Silakan coba lagi.');
    }

    public static function checkout(int $id): bool
    {
        $stmt = db()->prepare(
            "UPDATE visits SET check_out_at = ?, status = 'selesai'
             WHERE id = ? AND check_out_at IS NULL"
        );
        $stmt->execute([date('Y-m-d H:i:s'), $id]);
        return $stmt->rowCount() > 0;
    }

    public static function search(array $filters = []): array
    {
        $sql = 'SELECT * FROM visits WHERE 1 = 1';
        $params = [];

        if (!empty($filters['from'])) {
            $sql .= ' AND check_in_at >= ?';
            $params[] = $filters['from'] . ' 00:00:00';
        }
        if (!empty($filters['to'])) {
            $sql .= ' AND check_in_at <= ?';
            $params[] = $filters['to'] . ' 23:59:59';
        }
        if (!empty($filters['status']) && in_array($filters['status'], ['aktif', 'selesai'], true)) {
            $sql .= ' AND status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['q'])) {
            $sql .= ' AND (name LIKE ? OR institution LIKE ? OR phone LIKE ?)';
            $like = '%' . $filters['q'] . '%';
            array_push($params, $like, $like, $like);
        }

        $sql .= ' ORDER BY check_in_at DESC, id DESC LIMIT 500';

        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function stats(?string $date = null): array
    {
        $date = $date ?? today();
        $stmt = db()->prepare(
            "SELECT
                COUNT(*) AS total,
                COALESCE(SUM(status = 'aktif'), 0) AS aktif,
                COALESCE(SUM(status = 'selesai'), 0) AS selesai,
                AVG(TIMESTAMPDIFF(MINUTE, check_in_at, check_out_at)) AS rata_menit
             FROM visits
             WHERE DATE(check_in_at) = ?"
        );
        $stmt->execute([$date]);
        $row = $stmt->fetch() ?: [];

        return [
            'total' => (int) ($row['total'] ?? 0),
            'aktif' => (int) ($row['aktif'] ?? 0),
            'selesai' => (int) ($row['selesai'] ?? 0),
            'rata_menit' => $row['rata_menit'] !== null ? (int) round((float) $row['rata_menit']) : 0,
        ];
    }

    public static function summary(string $from, string $to): array
    {
        $stmt = db()->prepare(
            "SELECT
                COUNT(*) AS total,
                COALESCE(SUM(status = 'aktif'), 0) AS aktif,
                COALESCE(SUM(status = 'selesai'), 0) AS selesai,
                AVG(TIMESTAMPDIFF(MINUTE, check_in_at, check_out_at)) AS rata_menit
             FROM visits
             WHERE check_in_at BETWEEN ? AND ?"
        );
        $stmt->execute([$from . ' 00:00:00', $to . ' 23:59:59']);
        $row = $stmt->fetch() ?: [];

        return [
            'total' => (int) ($row['total'] ?? 0),
            'aktif' => (int) ($row['aktif'] ?? 0),
            'selesai' => (int) ($row['selesai'] ?? 0),
            'rata_menit' => $row['rata_menit'] !== null ? (int) round((float) $row['rata_menit']) : 0,
        ];
    }

    public static function topInstitutions(string $from, string $to, int $limit = 5): array
    {
        $stmt = db()->prepare(
            'SELECT institution, COUNT(*) AS total
             FROM visits
             WHERE check_in_at BETWEEN ? AND ?
             GROUP BY institution
             ORDER BY total DESC, institution ASC
             LIMIT ' . max(1, min(20, $limit))
        );
        $stmt->execute([$from . ' 00:00:00', $to . ' 23:59:59']);
        return $stmt->fetchAll();
    }

    public static function dailyTrend(string $from, string $to): array
    {
        $stmt = db()->prepare(
            'SELECT DATE(check_in_at) AS tanggal, COUNT(*) AS total
             FROM visits
             WHERE check_in_at BETWEEN ? AND ?
             GROUP BY DATE(check_in_at)
             ORDER BY tanggal ASC'
        );
        $stmt->execute([$from . ' 00:00:00', $to . ' 23:59:59']);
        return $stmt->fetchAll();
    }
}
