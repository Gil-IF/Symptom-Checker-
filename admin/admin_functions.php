<?php
function getAdminSummary(PDO $pdo): array
{
    return [
        'totalUsers'      => (int) $pdo->query("SELECT COUNT(*) FROM mahasiswa")->fetchColumn(),
        'totalScreening'  => (int) $pdo->query("SELECT COUNT(*) FROM skrining")->fetchColumn(),
        'screeningToday'  => (int) $pdo->query("SELECT COUNT(*) FROM skrining WHERE DATE(tgl_skrining) = CURDATE()")->fetchColumn(),
        'activeQuestions' => (int) $pdo->query("SELECT COUNT(*) FROM variabel_skrining WHERE status_aktif = 1")->fetchColumn(),
    ];
}