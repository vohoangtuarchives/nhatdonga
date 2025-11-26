<?php

namespace Tuezy;

/**
 * PaginationHelper - Handles pagination logic
 * Refactors repetitive pagination code in API and sources files
 */
class PaginationHelper
{
    private $pagingAjax;
    private int $perPage;
    private int $currentPage;
    private string $baseUrl;

    public function __construct($pagingAjax, int $perPage = 12, string $baseUrl = '')
    {
        $this->pagingAjax = $pagingAjax;
        $this->perPage = $perPage;
        $this->currentPage = !empty($_GET['p']) ? (int)htmlspecialchars($_GET['p']) : 1;
        $this->baseUrl = $baseUrl;
    }

    /**
     * Build pagination query parameters
     * 
     * @param array $filters Additional filters
     * @return array ['where' => string, 'params' => array, 'pageLink' => string]
     */
    public function buildQuery(array $filters = []): array
    {
        $where = '';
        $params = [];
        $tempLink = '';
        $pageLink = $this->baseUrl . "?perpage=" . $this->perPage;

        // Add filters to where clause
        foreach ($filters as $key => $value) {
            if ($value !== null && $value !== '' && $value !== 'all') {
                $tempLink .= "&$key=" . urlencode($value);
                
                switch ($key) {
                    case 'idList':
                        $where .= " and id_list = ?";
                        $params[] = $value;
                        break;
                    case 'idc':
                        $where .= " and id_cat = ?";
                        $params[] = $value;
                        break;
                    case 'noibat':
                        $where .= " and find_in_set(?,status)";
                        $params[] = $value;
                        break;
                    // Add more filter types as needed
                }
            }
        }

        $tempLink .= "&p=";
        $pageLink .= $tempLink;

        return [
            'where' => $where,
            'params' => $params,
            'pageLink' => $pageLink,
            'start' => ($this->currentPage - 1) * $this->perPage,
            'perPage' => $this->perPage
        ];
    }

    /**
     * Get paginated results
     * 
     * @param string $sql Base SQL query
     * @param array $params Query parameters
     * @param $cache Cache object
     * @param string $where Additional WHERE clause
     * @return array ['items' => array, 'count' => int, 'pagination' => string]
     */
    public function getPaginatedResults(string $sql, array $params, $cache, string $where = ''): array
    {
        $query = $this->buildQuery();
        $fullWhere = $where . $query['where'];
        $fullParams = array_merge($params, $query['params']);

        $sqlWithWhere = $sql . $fullWhere;
        $sqlCache = $sqlWithWhere . " limit {$query['start']}, {$query['perPage']}";

        $items = $cache->get($sqlCache, $fullParams, 'result', 7200);
        $countItems = count($cache->get($sqlWithWhere, $fullParams, 'result', 7200));

        $pagination = '';
        if ($countItems > 0) {
            $eShow = htmlspecialchars($_GET['eShow'] ?? '');
            $pagination = $this->pagingAjax->getAllPageLinks($countItems, $query['pageLink'], $eShow);
        }

        return [
            'items' => $items,
            'count' => $countItems,
            'pagination' => $pagination
        ];
    }

    /**
     * Get current page
     * 
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * Get items per page
     * 
     * @return int
     */
    public function getPerPage(): int
    {
        return $this->perPage;
    }

    /**
     * Calculate start offset
     * 
     * @return int
     */
    public function getStart(): int
    {
        return ($this->currentPage - 1) * $this->perPage;
    }

    /**
     * Get pagination HTML
     * 
     * @param int $total Total items
     * @param string $url Base URL
     * @param string $elShow Element ID to show content (for AJAX pagination)
     * @param int|null $perPage Items per page (optional, uses instance perPage if not provided)
     * @return string Pagination HTML
     */
    public function getPagination(int $total, string $url, string $elShow = '', ?int $perPage = null): string
    {
        if ($total <= 0) {
            return '';
        }

        // Use provided perPage or instance perPage
        $itemsPerPage = $perPage ?? $this->perPage;
        
        // Set perPage for pagingAjax
        $this->pagingAjax->perpage = $itemsPerPage;
        
        // Build page link
        $urlpos = strpos($url, "?");
        $pageLink = ($urlpos) ? $url . "&" : $url . "?";
        $pageLink .= "p=";

        // Generate pagination using pagingAjax
        return $this->pagingAjax->getAllPageLinks($total, $pageLink, $elShow);
    }
}

