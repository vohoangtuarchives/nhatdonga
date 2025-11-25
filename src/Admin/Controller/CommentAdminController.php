<?php

namespace Tuezy\Admin\Controller;

use Tuezy\Service\CommentService;
use Tuezy\SecurityHelper;
use Tuezy\Admin\AdminAuthHelper;
use Tuezy\Admin\AdminPermissionHelper;

/**
 * CommentAdminController - Handles comment admin requests
 */
class CommentAdminController extends BaseAdminController
{
    private CommentService $commentService;

    public function __construct(
        $db,
        $cache,
        $func,
        array $config,
        AdminAuthHelper $authHelper,
        AdminPermissionHelper $permissionHelper
    ) {
        parent::__construct($db, $cache, $func, $config, $authHelper, $permissionHelper);

        $commentRepo = new \Tuezy\Repository\CommentRepository($db, $cache, 'vi', 'slugvi');
        $uploadHandler = new \Tuezy\UploadHandler($func, $db);
        $this->commentService = new CommentService($commentRepo, $func, $uploadHandler);
    }

    /**
     * List comments
     * 
     * @param string $variant Variant (product, news, etc.)
     * @param string $type Type
     * @param int $idParent Parent ID
     * @param int $page Current page
     * @param int $perPage Items per page
     * @return array View data
     */
    public function man(string $variant, string $type, int $idParent = 0, int $page = 1, int $perPage = 20): array
    {
        $this->requireAuth();

        // Get parent item if idParent is provided
        $item = null;
        if ($idParent > 0) {
            $item = $this->db->rawQueryOne(
                "SELECT * FROM #_{$variant} WHERE id = ? AND type = ? LIMIT 0,1",
                [$idParent, $type]
            );
        }

        // Get comments using CommentService
        $start = ($page - 1) * $perPage;
        $comments = $this->commentService->getCommentsList($idParent, $type, true);
        $total = $this->commentService->getStatistics($idParent, $type, true)['total'];

        // Build URL for pagination
        $this->urlHelper->reset();
        if ($idParent > 0) {
            $this->urlHelper->addParam('id', $idParent);
        }
        $url = $this->urlHelper->getUrl('comment', 'man');
        $paging = $this->func->pagination($total, $perPage, $page, $url);

        return [
            'item' => $item,
            'comments' => $comments,
            'total' => $total,
            'paging' => $paging,
            'variant' => $variant,
            'type' => $type,
        ];
    }

    /**
     * Update comment status
     * 
     * @param int $id Comment ID
     * @param string $status Status value
     * @return array ['success' => bool, 'errors' => array]
     */
    public function updateStatus(int $id, string $status): array
    {
        $this->requireAuth();
        return $this->commentService->updateStatus($id, $status);
    }

    /**
     * Delete comment
     * 
     * @param int $id Comment ID
     * @return array ['success' => bool, 'errors' => array]
     */
    public function delete(int $id): array
    {
        $this->requireAuth();
        return $this->commentService->deleteComment($id);
    }
}

