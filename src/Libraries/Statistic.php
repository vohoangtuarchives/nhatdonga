<?php
namespace Tuezy\Libraries;

class Statistic
{
    function __construct(private $d, private $cache)
    {
    }

    public function getCounter()
    {
        $locktime = 15 * 60;
        $initialvalue = 1;
        $records = 100000;
        $day = date('d');
        $month = date('n');
        $year = date('Y');
        $daystart = mktime(0,0,0,$month,$day,$year);
        $monthstart = mktime(0,0,0,$month,1,$year);
        $weekday = date('w');
        $weekday--;
        if($weekday < 0) $weekday = 7;
        $weekday = $weekday * 24*60*60;
        $weekstart = $daystart - $weekday;
        $yesterdaystart = $daystart - (24*60*60);
        $now = time();
        $ip = $_SERVER['REMOTE_ADDR'];
        $t = $this->cache->get("select max(id) as total from #_counter", null, 'fetch', 1800);
        $all_visitors = $t['total'] ?? 0;
        
        if($all_visitors !== 0) $all_visitors += $initialvalue;
        else $all_visitors = $initialvalue;
        
        $temp = $all_visitors - $records;
        if($temp>0) $this->d->rawQuery("delete from #_counter where id < '$temp'");
        
        $vip = $this->d->rawQueryOne("select count(*) as visitip from #_counter where ip='$ip' and (tm+'$locktime')>'$now' limit 0,1");
        $items = $vip['visitip'] ?? 0;
        
        if(empty($items)) $this->d->rawQuery("insert into #_counter (tm, ip) values ('$now', '$ip')");
        
        $todayrec = $this->cache->get("select count(*) as todayrecord from #_counter where tm > '$daystart'", null, 'fetch', 1800);
        $yesrec = $this->cache->get("select count(*) as yesterdayrec from #_counter where tm > '$yesterdaystart' and tm < '$daystart'", null, 'fetch', 1800);
        $weekrec = $this->cache->get("select count(*) as weekrec from #_counter where tm >= '$weekstart'", null, 'fetch', 1800);
        $monthrec = $this->cache->get("select count(*) as monthrec from #_counter where tm >= '$monthstart'", null, 'fetch', 1800);
        $totalrec = $this->cache->get("select max(id) as totalrec from #_counter", null, 'fetch', 1800);
        
        $result['today'] = $todayrec['todayrecord'] ?? 0;
        $result['yesterday'] = $yesrec['yesterdayrec'] ?? 0;
        $result['week'] = $weekrec['weekrec'] ?? 0;
        $result['month'] = $monthrec['monthrec'] ?? 0;
        $result['total'] = $totalrec['totalrec'] ?? 0;
        
        return $result;
    }

    public function getOnline()
    {
        $session = session_id();
        $time = time();
        $time_check = $time - 600;
        $ip = $_SERVER['REMOTE_ADDR'];
        $result = $this->d->rawQuery("select * from #_user_online where session = ?",array($session));
        if(empty($result))
        {
            $this->d->rawQuery("insert into #_user_online(session,time,ip) values(?,?,?)",array($session,$time,$ip));
        }
        else
        {
            $this->d->rawQuery("update #_user_online set time = ? where session = ?",array($time,$session));
        }
        $this->d->rawQuery("delete from #_user_online where time < $time_check");
        $user_online = $this->d->rawQuery("select * from #_user_online");
        $user_online = is_array($user_online) ? count($user_online) : 0;
        return $user_online;
    }
}

