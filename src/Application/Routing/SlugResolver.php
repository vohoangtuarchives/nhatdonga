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
                
                // Debug
                if(isset($_GET['debug_routing'])) {
                    echo "<div style='background:yellow;padding:10px;margin:5px;'>";
                    echo "<strong>SlugResolver Debug:</strong><br>";
                    echo "Com: $com<br>";
                    echo "Table: $urlTbl<br>";
                    echo "Type: $urlType<br>";
                    echo "Slug lang: $sluglang<br>";
                    echo "Row type: " . gettype($row) . "<br>";
                    echo "Row found: " . (is_array($row) && !empty($row['id']) ? "YES (ID: {$row['id']})" : "NO") . "<br>";
                    echo "</div>";
                }
                
                // Fix: rawQueryOne can return false, not just null/empty array
                if (is_array($row) && !empty($row['id'])) {
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

