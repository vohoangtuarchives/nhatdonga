<?php

namespace Tuezy\Service;

/**
 * StatisticService - Website statistics management
 * Refactored from class.Statistic.php
 * 
 * Handles visitor counter and online user tracking
 */
class StatisticService
{
    private $db;
    private $cache;
    private const LOCK_TIME = 15 * 60; // 15 minutes
    private const INITIAL_VALUE = 1;
    private const MAX_RECORDS = 100000;
    private const ONLINE_TIMEOUT = 600; // 10 minutes

    public function __construct($db, $cache)
    {
        $this->db = $db;
        $this->cache = $cache;
    }

    /**
     * Get visitor counter statistics
     * 
     * @return array Statistics ['today', 'yesterday', 'week', 'month', 'total']
     */
    public function getCounter(): array
    {
        $day = (int)date('d');
        $month = (int)date('n');
        $year = (int)date('Y');
        $now = time();

        // Calculate time boundaries
        $dayStart = mktime(0, 0, 0, $month, $day, $year);
        $monthStart = mktime(0, 0, 0, $month, 1, $year);
        
        // Week start (Monday)
        $weekday = (int)date('w');
        $weekday--;
        if ($weekday < 0) {
            $weekday = 7;
        }
        $weekStart = $dayStart - ($weekday * 24 * 60 * 60);
        
        // Yesterday start
        $yesterdayStart = $dayStart - (24 * 60 * 60);

        // Get total visitors
        $t = $this->cache->get(
            "SELECT MAX(id) as total FROM #_counter",
            null,
            'fetch',
            1800
        );

        $allVisitors = $t['total'] ?? null;
        if ($allVisitors !== null) {
            $allVisitors += self::INITIAL_VALUE;
        } else {
            $allVisitors = self::INITIAL_VALUE;
        }

        // Delete old records
        $temp = $allVisitors - self::MAX_RECORDS;
        if ($temp > 0) {
            $this->db->rawQuery("DELETE FROM #_counter WHERE id < ?", [$temp]);
        }

        // Check if IP already visited recently
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $vip = $this->db->rawQueryOne(
            "SELECT COUNT(*) as visitip FROM #_counter 
             WHERE ip = ? AND (tm + ?) > ? 
             LIMIT 0,1",
            [$ip, self::LOCK_TIME, $now]
        );

        $items = (int)($vip['visitip'] ?? 0);

        // Insert new visit if not recent
        if (empty($items)) {
            $this->db->rawQuery(
                "INSERT INTO #_counter (tm, ip) VALUES (?, ?)",
                [$now, $ip]
            );
        }

        // Get statistics with cache
        $todayRec = $this->cache->get(
            "SELECT COUNT(*) as todayrecord FROM #_counter WHERE tm > ?",
            [$dayStart],
            'fetch',
            1800
        );

        $yesRec = $this->cache->get(
            "SELECT COUNT(*) as yesterdayrec FROM #_counter 
             WHERE tm > ? AND tm < ?",
            [$yesterdayStart, $dayStart],
            'fetch',
            1800
        );

        $weekRec = $this->cache->get(
            "SELECT COUNT(*) as weekrec FROM #_counter WHERE tm >= ?",
            [$weekStart],
            'fetch',
            1800
        );

        $monthRec = $this->cache->get(
            "SELECT COUNT(*) as monthrec FROM #_counter WHERE tm >= ?",
            [$monthStart],
            'fetch',
            1800
        );

        $totalRec = $this->cache->get(
            "SELECT MAX(id) as totalrec FROM #_counter",
            null,
            'fetch',
            1800
        );

        return [
            'today' => (int)($todayRec['todayrecord'] ?? 0),
            'yesterday' => (int)($yesRec['yesterdayrec'] ?? 0),
            'week' => (int)($weekRec['weekrec'] ?? 0),
            'month' => (int)($monthRec['monthrec'] ?? 0),
            'total' => (int)($totalRec['totalrec'] ?? 0),
        ];
    }

    /**
     * Get online users count
     * 
     * @return int Number of online users
     */
    public function getOnline(): int
    {
        $session = session_id();
        $time = time();
        $timeCheck = $time - self::ONLINE_TIMEOUT;
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

        // Check if session exists
        $result = $this->db->rawQuery(
            "SELECT * FROM #_user_online WHERE session = ?",
            [$session]
        );

        if (count($result) == 0) {
            // Insert new session
            $this->db->rawQuery(
                "INSERT INTO #_user_online (session, time, ip) VALUES (?, ?, ?)",
                [$session, $time, $ip]
            );
        } else {
            // Update existing session
            $this->db->rawQuery(
                "UPDATE #_user_online SET time = ? WHERE session = ?",
                [$time, $session]
            );
        }

        // Delete expired sessions
        $this->db->rawQuery(
            "DELETE FROM #_user_online WHERE time < ?",
            [$timeCheck]
        );

        // Get online users count
        $userOnline = $this->db->rawQuery("SELECT * FROM #_user_online");
        return count($userOnline);
    }

    /**
     * Get detailed statistics
     * 
     * @return array Detailed statistics
     */
    public function getDetailedStats(): array
    {
        $counter = $this->getCounter();
        $online = $this->getOnline();

        return [
            'counter' => $counter,
            'online' => $online,
        ];
    }
}

