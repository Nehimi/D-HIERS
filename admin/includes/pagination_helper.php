<?php
/**
 * Generate pagination HTML
 * 
 * @param int $currentPage Current page number (1-indexed)
 * @param int $totalItems Total number of items
 * @param int $itemsPerPage Items per page
 * @param string $baseUrl Base URL for pagination links
 * @return array Pagination data
 */
function getPaginationData($currentPage, $totalItems, $itemsPerPage = 10, $baseUrl = '') {
    $totalPages = ceil($totalItems / $itemsPerPage);
    $currentPage = max(1, min($currentPage, $totalPages));
    
    $offset = ($currentPage - 1) * $itemsPerPage;
    $showingStart = $totalItems > 0 ? $offset + 1 : 0;
    $showingEnd = min($offset + $itemsPerPage, $totalItems);
    
    // Calculate page numbers to show
    $pages = [];
    $maxPagesToShow = 5;
    
    if ($totalPages <= $maxPagesToShow) {
        // Show all pages
        for ($i = 1; $i <= $totalPages; $i++) {
            $pages[] = $i;
        }
    } else {
        // Show a subset of pages
        $halfMax = floor($maxPagesToShow / 2);
        $startPage = max(1, $currentPage - $halfMax);
        $endPage = min($totalPages, $currentPage + $halfMax);
        
        // Adjust if we're near the beginning or end
        if ($currentPage <= $halfMax) {
            $endPage = min($totalPages, $maxPagesToShow);
        } elseif ($currentPage >= $totalPages - $halfMax) {
            $startPage = max(1, $totalPages - $maxPagesToShow + 1);
        }
        
        // Add first page if not included
        if ($startPage > 1) {
            $pages[] = 1;
            if ($startPage > 2) {
                $pages[] = '...';
            }
        }
        
        // Add middle pages
        for ($i = $startPage; $i <= $endPage; $i++) {
            $pages[] = $i;
        }
        
        // Add last page if not included
        if ($endPage < $totalPages) {
            if ($endPage < $totalPages - 1) {
                $pages[] = '...';
            }
            $pages[] = $totalPages;
        }
    }
    
    return [
        'currentPage' => $currentPage,
        'totalPages' => $totalPages,
        'totalItems' => $totalItems,
        'itemsPerPage' => $itemsPerPage,
        'offset' => $offset,
        'showingStart' => $showingStart,
        'showingEnd' => $showingEnd,
        'pages' => $pages,
        'hasPrevious' => $currentPage > 1,
        'hasNext' => $currentPage < $totalPages,
        'previousPage' => max(1, $currentPage - 1),
        'nextPage' => min($totalPages, $currentPage + 1),
        'baseUrl' => $baseUrl
    ];
}

/**
 * Render pagination HTML
 * 
 * @param array $paginationData Data from getPaginationData()
 * @return string HTML for pagination
 */
function renderPagination($paginationData) {
    extract($paginationData);
    
    $html = '<div class="table-footer">';
    $html .= '<div class="showing-info">';
    $html .= "Showing <strong>$showingStart</strong> to <strong>$showingEnd</strong> of <strong>$totalItems</strong> results";
    $html .= '</div>';
    $html .= '<div class="pagination">';
    
    // Previous button
    if ($hasPrevious) {
        $html .= "<button class='page-btn' onclick='navigateToPage($previousPage)'>";
        $html .= '<i class="fa-solid fa-chevron-left"></i>';
        $html .= '</button>';
    } else {
        $html .= '<button class="page-btn" disabled>';
        $html .= '<i class="fa-solid fa-chevron-left"></i>';
        $html .= '</button>';
    }
    
    // Page numbers
    foreach ($pages as $page) {
        if ($page === '...') {
            $html .= '<button class="page-btn" disabled>...</button>';
        } else {
            $activeClass = ($page == $currentPage) ? 'active' : '';
            $html .= "<button class='page-btn $activeClass' onclick='navigateToPage($page)'>$page</button>";
        }
    }
    
    // Next button
    if ($hasNext) {
        $html .= "<button class='page-btn' onclick='navigateToPage($nextPage)'>";
        $html .= '<i class="fa-solid fa-chevron-right"></i>';
        $html .= '</button>';
    } else {
        $html .= '<button class="page-btn" disabled>';
        $html .= '<i class="fa-solid fa-chevron-right"></i>';
        $html .= '</button>';
    }
    
    $html .= '</div>';
    $html .= '</div>';
    
    return $html;
}
?>
