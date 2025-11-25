<?php

namespace Tuezy\API\Controller;

use Tuezy\Service\CommentService;
use Tuezy\Repository\CommentRepository;
use Tuezy\UploadHandler;

/**
 * CommentAPIController - Handles comment API requests
 */
class CommentAPIController extends BaseAPIController
{
    private CommentService $commentService;

    public function __construct($db, $cache, $func, $config, string $lang = 'vi', string $sluglang = 'slugvi')
    {
        parent::__construct($db, $cache, $func, $config, $lang, $sluglang);

        $commentRepo = new CommentRepository($db, $cache, $lang, $sluglang);
        $uploadHandler = new UploadHandler($func, $db);
        $this->commentService = new CommentService($commentRepo, $func, $uploadHandler);
    }

    /**
     * Get paginated comments
     * 
     * @return void Outputs JSON
     */
    public function limitLists(): void
    {
        $idVariant = (int)$this->get('id_variant', 0);
        $type = $this->sanitize($this->get('type', ''));
        $limitFrom = (int)$this->get('limit_from', 0);
        $limitGet = (int)$this->get('limit_get', 10);

        if ($idVariant <= 0 || empty($type)) {
            $this->error('Invalid parameters');
            return;
        }

        $result = $this->commentService->getPaginatedComments($idVariant, $type, false, $limitFrom, $limitGet);

        $this->success([
            'data' => $result['data'],
            'total' => $result['total'],
        ]);
    }

    /**
     * Get paginated replies
     * 
     * @return void Outputs JSON
     */
    public function limitReplies(): void
    {
        $idParent = (int)$this->get('id_parent', 0);
        $idVariant = (int)$this->get('id_variant', 0);
        $type = $this->sanitize($this->get('type', ''));
        $limitFrom = (int)$this->get('limit_from', 0);
        $limitGet = (int)$this->get('limit_get', 10);

        if ($idParent <= 0 || $idVariant <= 0 || empty($type)) {
            $this->error('Invalid parameters');
            return;
        }

        $result = $this->commentService->getPaginatedReplies($idParent, $idVariant, $type, false, $limitFrom, $limitGet);

        $this->success([
            'data' => $result['data'],
            'total' => $result['total'],
        ]);
    }

    /**
     * Add comment
     * 
     * @return void Outputs JSON
     */
    public function add(): void
    {
        $data = $_POST['data'] ?? [];
        $photos = $_POST['photos'] ?? [];

        // Sanitize data
        $data = $this->sanitize($data);

        $result = $this->commentService->addComment($data, $photos);

        if ($result['success']) {
            $this->success(['message' => 'Comment added successfully']);
        } else {
            $this->error(implode(', ', $result['errors']), 400);
        }
    }
}

