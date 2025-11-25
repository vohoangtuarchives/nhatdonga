<?php

namespace Tuezy\Helper;

/**
 * PaginationAjaxHelper - AJAX pagination
 * Refactored from class.PaginationsAjax.php
 * 
 * Generates AJAX pagination links
 */
class PaginationAjaxHelper
{
    public int $perPage = 1;

    /**
     * Get all page links
     * 
     * @param int $count Total items
     * @param string $href Base URL
     * @param string $elShow Element ID to show content
     * @return string HTML pagination links
     */
    public function getAllPageLinks(int $count, string $href, string $elShow): string
    {
        $page = (int)($_GET['p'] ?? 1);
        $output = '';

        if ($this->perPage == 0) {
            return $output;
        }

        $pages = (int)ceil($count / $this->perPage);

        if ($pages <= 1) {
            return $output;
        }

        // First and Prev buttons
        if ($page == 1) {
            $output .= '<a class="first disabled">First</a><a class="prev disabled">Prev</a>';
        } else {
            $firstHref = htmlspecialchars($href . '1', ENT_QUOTES);
            $prevHref = htmlspecialchars($href . ($page - 1), ENT_QUOTES);
            $elShowEscaped = htmlspecialchars($elShow, ENT_QUOTES);
            $output .= '<a class="first" onclick="loadPaging(\'' . $firstHref . '\',\'' . $elShowEscaped . '\')">First</a>';
            $output .= '<a class="prev" onclick="loadPaging(\'' . $prevHref . '\',\'' . $elShowEscaped . '\')">Prev</a>';
        }

        // First page link
        if (($page - 3) > 0) {
            if ($page == 1) {
                $output .= '<a id="1" class="current">1</a>';
            } else {
                $firstHref = htmlspecialchars($href . '1', ENT_QUOTES);
                $elShowEscaped = htmlspecialchars($elShow, ENT_QUOTES);
                $output .= '<a onclick="loadPaging(\'' . $firstHref . '\',\'' . $elShowEscaped . '\')">1</a>';
            }
        }

        // Dots before current range
        if (($page - 3) > 1) {
            $output .= '<a class="dot">...</a>';
        }

        // Current range (page-2 to page+2)
        for ($i = ($page - 2); $i <= ($page + 2); $i++) {
            if ($i < 1) {
                continue;
            }
            if ($i > $pages) {
                break;
            }

            if ($page == $i) {
                $output .= '<a id="' . $i . '" class="current">' . $i . '</a>';
            } else {
                $pageHref = htmlspecialchars($href . $i, ENT_QUOTES);
                $elShowEscaped = htmlspecialchars($elShow, ENT_QUOTES);
                $output .= '<a onclick="loadPaging(\'' . $pageHref . '\',\'' . $elShowEscaped . '\')">' . $i . '</a>';
            }
        }

        // Dots after current range
        if (($pages - ($page + 2)) > 1) {
            $output .= '<a class="dot">...</a>';
        }

        // Last page link
        if (($pages - ($page + 2)) > 0) {
            if ($page == $pages) {
                $output .= '<a id="' . $pages . '" class="current">' . $pages . '</a>';
            } else {
                $lastHref = htmlspecialchars($href . $pages, ENT_QUOTES);
                $elShowEscaped = htmlspecialchars($elShow, ENT_QUOTES);
                $output .= '<a onclick="loadPaging(\'' . $lastHref . '\',\'' . $elShowEscaped . '\')">' . $pages . '</a>';
            }
        }

        // Next and Last buttons
        if ($page < $pages) {
            $nextHref = htmlspecialchars($href . ($page + 1), ENT_QUOTES);
            $lastHref = htmlspecialchars($href . $pages, ENT_QUOTES);
            $elShowEscaped = htmlspecialchars($elShow, ENT_QUOTES);
            $output .= '<a class="next" onclick="loadPaging(\'' . $nextHref . '\',\'' . $elShowEscaped . '\')">Next</a>';
            $output .= '<a class="last" onclick="loadPaging(\'' . $lastHref . '\',\'' . $elShowEscaped . '\')">Last</a>';
        } else {
            $output .= '<a class="next disabled">Next</a><a class="last disabled">Last</a>';
        }

        return $output;
    }

    /**
     * Get prev/next links only
     * 
     * @param int $count Total items
     * @param string $href Base URL
     * @param string $elShow Element ID to show content
     * @return string HTML prev/next links
     */
    public function getPrevNext(int $count, string $href, string $elShow): string
    {
        $page = (int)($_GET['p'] ?? 1);
        $output = '';

        if ($this->perPage == 0) {
            return $output;
        }

        $pages = (int)ceil($count / $this->perPage);

        if ($pages <= 1) {
            return $output;
        }

        // Prev button
        if ($page == 1) {
            $output .= '<a class="prev disabled">Prev</a>';
        } else {
            $prevHref = htmlspecialchars($href . ($page - 1), ENT_QUOTES);
            $elShowEscaped = htmlspecialchars($elShow, ENT_QUOTES);
            $output .= '<a class="prev" onclick="loadPaging(\'' . $prevHref . '\',\'' . $elShowEscaped . '\')">Prev</a>';
        }

        // Next button
        if ($page < $pages) {
            $nextHref = htmlspecialchars($href . ($page + 1), ENT_QUOTES);
            $elShowEscaped = htmlspecialchars($elShow, ENT_QUOTES);
            $output .= '<a class="next" onclick="loadPaging(\'' . $nextHref . '\',\'' . $elShowEscaped . '\')">Next</a>';
        } else {
            $output .= '<a class="next disabled">Next</a>';
        }

        return $output;
    }
}

