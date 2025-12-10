<?php

namespace Tuezy\Application\Routing;

class SlugResolver
{
    public static function resolve(string $com, \PDODb $db, string $sluglang, array $requick): array
    {
        $resolved = [
            'com' => $com,
            'type' => null,
            'table' => null,
        ];
        if (empty($com)) return $resolved;
        foreach ($requick as $v) {
            $urlTbl = $v['tbl'] ?? '';
            $urlType = $v['type'] ?? '';
            $urlField = $v['field'] ?? '';
            $urlCom = $v['com'] ?? '';
            if (!empty($urlTbl) && !in_array($urlTbl, ['static', 'photo'])) {
                $row = $db->rawQueryOne("select id from #_{$urlTbl} where {$sluglang} = ? and type = ? and find_in_set('hienthi',status) limit 0,1", [$com, $urlType]);
                if (!empty($row['id'])) {
                    $_GET[$urlField] = $row['id'];
                    $resolved['com'] = $urlCom;
                    if (!empty($urlType)) {
                        $resolved['type'] = $urlType;
                    }
                    $resolved['table'] = $urlTbl;
                    break;
                }
            }
        }
        return $resolved;
    }
}

