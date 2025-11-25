<?php

namespace Tuezy\Service;

use Tuezy\Repository\CommentRepository;
use Tuezy\SecurityHelper;
use Tuezy\UploadHandler;

/**
 * CommentService - Business logic for comments
 * Refactored from class.Comments.php
 * 
 * Handles comment operations: add, list, calculate ratings, etc.
 */
class CommentService
{
    private CommentRepository $repository;
    private $func;
    private UploadHandler $uploadHandler;
    private int $limitParentShow = 2;
    private int $limitParentGet = 1;
    private int $limitChildShow = 2;
    private int $limitChildGet = 1;

    public function __construct(CommentRepository $repository, $func, UploadHandler $uploadHandler)
    {
        $this->repository = $repository;
        $this->func = $func;
        $this->uploadHandler = $uploadHandler;
    }

    /**
     * Get comment statistics
     * 
     * @param int $idVariant Variant ID
     * @param string $type Type
     * @param bool $isAdmin Include hidden if admin
     * @return array Statistics
     */
    public function getStatistics(int $idVariant, string $type, bool $isAdmin = false): array
    {
        $total = $this->repository->getTotal($idVariant, $type, $isAdmin);
        $countStar = $this->getCountStar($idVariant, $type);
        $star = json_decode($countStar, true) ?: [];
        $totalStar = $this->repository->getTotalStars($idVariant, $type);

        return [
            'total' => $total,
            'count_star' => $countStar,
            'star' => $star,
            'total_star' => $totalStar,
        ];
    }

    /**
     * Get count of stars (1-5)
     * 
     * @param int $idVariant Variant ID
     * @param string $type Type
     * @return string JSON encoded star counts
     */
    public function getCountStar(int $idVariant, string $type): string
    {
        $count = [];
        for ($i = 1; $i <= 5; $i++) {
            $count[$i] = $this->repository->getStarCount($idVariant, $type, $i);
        }
        return json_encode($count);
    }

    /**
     * Get comments list
     * 
     * @param int $idVariant Variant ID
     * @param string $type Type
     * @param bool $isAdmin Include hidden if admin
     * @return array Comments with photos, videos, and replies
     */
    public function getCommentsList(int $idVariant, string $type, bool $isAdmin = false): array
    {
        $comments = $this->repository->getByVariant(
            $idVariant,
            $type,
            $isAdmin,
            0,
            $this->limitParentShow
        );

        $result = [];
        foreach ($comments as $comment) {
            $comment['photo'] = $this->repository->getPhotos($comment['id']);
            $comment['video'] = $this->repository->getVideo($comment['id']);
            $comment['replies'] = $this->repository->getReplies(
                $comment['id'],
                $idVariant,
                $type,
                $isAdmin,
                0,
                $this->limitChildShow
            );
            $result[] = $comment;
        }

        return $result;
    }

    /**
     * Get paginated comments
     * 
     * @param int $idVariant Variant ID
     * @param string $type Type
     * @param bool $isAdmin Include hidden if admin
     * @param int $limitFrom Offset
     * @param int $limitGet Limit
     * @return array ['data' => string, 'total' => int]
     */
    public function getPaginatedComments(int $idVariant, string $type, bool $isAdmin = false, int $limitFrom = 0, int $limitGet = 10): array
    {
        $comments = $this->repository->getByVariant($idVariant, $type, $isAdmin, $limitFrom, $limitGet);
        $total = $this->repository->getTotal($idVariant, $type, $isAdmin);

        $data = '';
        $markdownType = $isAdmin ? 'admin' : 'customer';

        foreach ($comments as $comment) {
            $params = [
                'id_variant' => $idVariant,
                'type' => $type,
                'is_admin' => $isAdmin,
                'lists' => $comment,
            ];
            $params['lists']['photo'] = $this->repository->getPhotos($comment['id']);
            $params['lists']['video'] = $this->repository->getVideo($comment['id']);
            $params['lists']['replies'] = $this->repository->getReplies(
                $comment['id'],
                $idVariant,
                $type,
                $isAdmin,
                0,
                $this->limitChildShow
            );

            $data .= $this->markdown($markdownType . '/lists', $params);
        }

        return [
            'data' => $data,
            'total' => $total,
        ];
    }

    /**
     * Get paginated replies
     * 
     * @param int $idParent Parent comment ID
     * @param int $idVariant Variant ID
     * @param string $type Type
     * @param bool $isAdmin Include hidden if admin
     * @param int $limitFrom Offset
     * @param int $limitGet Limit
     * @return array ['data' => string, 'total' => int]
     */
    public function getPaginatedReplies(int $idParent, int $idVariant, string $type, bool $isAdmin = false, int $limitFrom = 0, int $limitGet = 10): array
    {
        $replies = $this->repository->getReplies($idParent, $idVariant, $type, $isAdmin, $limitFrom, $limitGet);
        $total = $this->repository->getTotalReplies($idParent, $idVariant, $type, $isAdmin);

        $data = '';
        $markdownType = $isAdmin ? 'admin' : 'customer';

        if (!empty($replies)) {
            $params = ['replies' => $replies];
            $data = $this->markdown($markdownType . '/replies', $params);
        }

        return [
            'data' => $data,
            'total' => $total,
        ];
    }

    /**
     * Calculate percentage score for a star rating
     * 
     * @param int $star Star rating (1-5)
     * @param int $total Total comments
     * @param int $idVariant Variant ID
     * @param string $type Type
     * @return float Percentage
     */
    public function perScore(int $star, int $total, int $idVariant, string $type): float
    {
        if (empty($total)) {
            return 0;
        }

        $starCount = $this->repository->getStarCount($idVariant, $type, $star);
        
        return round(($starCount * 100) / $total, 1);
    }

    /**
     * Calculate average point
     * 
     * @param int $total Total comments
     * @param int $totalStar Total stars
     * @return float Average point
     */
    public function avgPoint(int $total, int $totalStar): float
    {
        if (empty($total)) {
            return 0;
        }
        return round($totalStar / $total, 1);
    }

    /**
     * Calculate average star percentage
     * 
     * @param int $total Total comments
     * @param int $totalStar Total stars
     * @return float Average star percentage
     */
    public function avgStar(int $total, int $totalStar): float
    {
        if (empty($total)) {
            return 0;
        }
        return ($totalStar * 100) / ($total * 5);
    }

    /**
     * Calculate star score percentage
     * 
     * @param int $star Star rating (1-5)
     * @return float Score percentage
     */
    public function scoreStar(int $star): float
    {
        if (empty($star)) {
            return 0;
        }
        return ($star * 100) / 5;
    }

    /**
     * Get initials from name
     * 
     * @param string $name Full name
     * @return string Initials
     */
    public function subName(string $name): string
    {
        if (empty($name)) {
            return '';
        }

        $arr = explode(' ', $name);
        
        if (count($arr) > 1) {
            return substr($arr[0], 0, 1) . substr(end($arr), 0, 1);
        }

        return substr($arr[0], 0, 1);
    }

    /**
     * Add comment (customer)
     * 
     * @param array $data Comment data
     * @param array $photos Photo files
     * @return array ['success' => bool, 'errors' => array]
     */
    public function addComment(array $data, array $photos = []): array
    {
        global $config;

        $errors = [];

        // Sanitize data
        foreach ($data as $key => $value) {
            $data[$key] = htmlspecialchars(SecurityHelper::sanitize($value));
        }

        $data['status'] = 'new-admin';
        $data['date_posted'] = time();

        // Validation
        if (empty($data['star'])) {
            $errors[] = 'Chưa chọn đánh giá sao';
        } elseif (!$this->func->isNumber($data['star'])) {
            $errors[] = 'Đánh giá sao không hợp lệ';
        }

        if (empty($data['title'])) {
            $errors[] = 'Chưa nhập tiêu đề đánh giá';
        }

        if (empty($data['content']) || (!empty($data['fullname_parent']) && trim($data['content']) == $data['fullname_parent'])) {
            $errors[] = 'Chưa nhập nội dung đánh giá';
        } else {
            unset($data['fullname_parent']);
        }

        if (empty($data['fullname'])) {
            $errors[] = 'Chưa nhập họ tên liên hệ';
        }

        if (empty($data['phone'])) {
            $errors[] = 'Chưa nhập số điện thoại liên hệ';
        } elseif (!$this->func->isPhone($data['phone'])) {
            $errors[] = 'Số điện thoại không hợp lệ';
        }

        if (empty($data['email'])) {
            $errors[] = 'Chưa nhập email liên hệ';
        } elseif (!$this->func->isEmail($data['email'])) {
            $errors[] = 'Email không hợp lệ';
        }

        if (count($photos) > 3) {
            $errors[] = 'Hình ảnh không được vượt quá 3 hình';
        }

        // Video validation
        if ($this->func->hasFile('review-file-video') && !$this->func->hasFile('review-poster-video')) {
            $errors[] = 'Hình đại diện video không được trống';
        }

        if (!$this->func->hasFile('review-file-video') && $this->func->hasFile('review-poster-video')) {
            $errors[] = 'Tập tin video không được trống';
        }

        if ($this->func->hasFile('review-file-video') && !$this->func->checkExtFile('review-file-video')) {
            $errors[] = 'Chỉ cho phép tập tin video với định dạng: ' . implode(',', $config['website']['video']['extension']);
        }

        if ($this->func->hasFile('review-file-video') && !$this->func->checkFile('review-file-video')) {
            $sizeVideo = $this->func->formatBytes($config['website']['video']['max-size']);
            $errors[] = 'Tập tin video không được vượt quá ' . $sizeVideo['numb'] . ' ' . $sizeVideo['ext'];
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // Save comment
        $commentId = $this->repository->create($data);
        if (!$commentId) {
            return ['success' => false, 'errors' => ['Lưu bình luận thất bại']];
        }

        // Save photos
        if (!empty($photos)) {
            $this->savePhotos($commentId, $photos);
        }

        // Save video
        if ($this->func->hasFile('review-file-video')) {
            $this->saveVideo($commentId);
        }

        return ['success' => true, 'errors' => []];
    }

    /**
     * Add admin comment
     * 
     * @param array $data Comment data
     * @param array $adminData Admin session data
     * @return array ['success' => bool, 'errors' => array]
     */
    public function addAdminComment(array $data, array $adminData): array
    {
        $errors = [];

        // Sanitize data
        foreach ($data as $key => $value) {
            $data[$key] = htmlspecialchars(SecurityHelper::sanitize($value));
        }

        $data['fullname'] = $adminData['fullname'] ?? '';
        $data['phone'] = $adminData['phone'] ?? '';
        $data['email'] = $adminData['email'] ?? '';
        $data['status'] = 'hienthi';
        $data['date_posted'] = time();

        // Validation
        if (empty($data['content']) || (!empty($data['fullname_parent']) && trim($data['content']) == $data['fullname_parent'])) {
            $errors[] = 'Chưa nhập nội dung đánh giá';
        } else {
            unset($data['fullname_parent']);
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // Save comment
        if (!$this->repository->create($data)) {
            return ['success' => false, 'errors' => ['Phản hồi thất bại. Vui lòng thử lại sau']];
        }

        return ['success' => true, 'errors' => []];
    }

    /**
     * Update comment status
     * 
     * @param int $id Comment ID
     * @param string $status Status to toggle
     * @return array ['success' => bool, 'errors' => array]
     */
    public function updateStatus(int $id, string $status): array
    {
        $errors = [];

        $comment = $this->repository->getById($id);
        if (!$comment) {
            return ['success' => false, 'errors' => ['Dữ liệu không hợp lệ']];
        }

        $statusArray = !empty($comment['status']) ? explode(',', $comment['status']) : [];

        // Toggle status
        $key = array_search($status, $statusArray);
        if ($key !== false) {
            unset($statusArray[$key]);
        } else {
            $statusArray[] = $status;
        }

        // Remove 'new-admin' when toggling
        $newAdminKey = array_search('new-admin', $statusArray);
        if ($newAdminKey !== false) {
            unset($statusArray[$newAdminKey]);
        }

        $data = ['status' => !empty($statusArray) ? implode(',', $statusArray) : ''];

        if (!$this->repository->update($id, $data)) {
            return ['success' => false, 'errors' => ['Cập nhật trạng thái thất bại. Vui lòng thử lại sau']];
        }

        return ['success' => true, 'errors' => []];
    }

    /**
     * Delete comment
     * 
     * @param int $id Comment ID
     * @return array ['success' => bool, 'errors' => array]
     */
    public function deleteComment(int $id): array
    {
        $errors = [];

        $comment = $this->repository->getById($id);
        if (!$comment) {
            return ['success' => false, 'errors' => ['Dữ liệu không hợp lệ']];
        }

        // If parent comment, delete related data
        if ($comment['id_parent'] == 0) {
            // Delete photos
            $photos = $this->repository->getPhotos($id);
            foreach ($photos as $photo) {
                $this->func->deleteFile(ROOT . UPLOAD_PHOTO_L . $photo['photo']);
            }
            $this->repository->deletePhotos($id);

            // Delete video
            $video = $this->repository->getVideo($id);
            if ($video) {
                $this->func->deleteFile(ROOT . UPLOAD_PHOTO_L . $video['photo']);
                $this->func->deleteFile(ROOT . UPLOAD_VIDEO_L . $video['video']);
                $this->repository->deleteVideo($id);
            }

            // Delete replies
            $this->repository->deleteReplies($id);
        }

        // Delete main comment
        if (!$this->repository->delete($id)) {
            return ['success' => false, 'errors' => ['Xóa bình luận thất bại. Vui lòng thử lại sau']];
        }

        return ['success' => true, 'errors' => []];
    }

    /**
     * Format time ago
     * 
     * @param int $time Timestamp
     * @return string Formatted time
     */
    public function timeAgo(int $time): string
    {
        $lang = [
            'now' => 'Vài giây trước',
            'ago' => 'trước',
            'vi' => [
                'y' => 'năm',
                'm' => 'tháng',
                'd' => 'ngày',
                'h' => 'giờ',
                'i' => 'phút',
                's' => 'giây',
            ],
        ];

        $ago = time() - $time;

        if ($ago < 1) {
            return $lang['now'];
        }

        $units = [
            365 * 24 * 60 * 60 => 'y',
            30 * 24 * 60 * 60 => 'm',
            24 * 60 * 60 => 'd',
            60 * 60 => 'h',
            60 => 'i',
            1 => 's',
        ];

        foreach ($units as $secs => $key) {
            $timeValue = $ago / $secs;
            if ($timeValue >= 1) {
                $timeValue = round($timeValue);
                $unitName = $lang['vi'][$key];
                return $timeValue . ' ' . $unitName . ' ' . $lang['ago'];
            }
        }

        return $lang['now'];
    }

    /**
     * Render markdown template
     * 
     * @param string $path Template path
     * @param array $params Template parameters
     * @return string Rendered content
     */
    public function markdown(string $path, array $params = []): string
    {
        if (empty($path)) {
            return '';
        }

        ob_start();
        include dirname(__DIR__, 2) . '/libraries/sample/comment/' . $path . '.php';
        $content = ob_get_contents();
        ob_clean();

        return $content;
    }

    /**
     * Save comment photos
     * 
     * @param int $commentId Comment ID
     * @param array $photos Photo file names
     */
    private function savePhotos(int $commentId, array $photos): void
    {
        if (empty($photos) || !isset($_FILES['review-file-photo'])) {
            return;
        }

        $myFile = $_FILES['review-file-photo'];
        $fileCount = count($myFile['name']);

        for ($i = 0; $i < $fileCount; $i++) {
            if (in_array($myFile['name'][$i], $photos, true)) {
                $_FILES['file-uploader-temp'] = [
                    'name' => $myFile['name'][$i],
                    'type' => $myFile['type'][$i],
                    'tmp_name' => $myFile['tmp_name'][$i],
                    'error' => $myFile['error'][$i],
                    'size' => $myFile['size'][$i],
                ];

                $fileName = $this->func->uploadName($myFile['name'][$i]);
                $photo = $this->func->uploadImage('file-uploader-temp', '.jpg|.png|.jpeg', ROOT . UPLOAD_PHOTO_L, $fileName);

                if ($photo) {
                    $this->repository->createPhoto($commentId, $photo);
                }
            }
        }
    }

    /**
     * Save comment video
     * 
     * @param int $commentId Comment ID
     */
    private function saveVideo(int $commentId): void
    {
        global $config;

        $data = [];

        // Upload poster
        if ($this->func->hasFile('review-poster-video')) {
            $fileName = $this->func->uploadName($_FILES['review-poster-video']['name']);
            $photo = $this->func->uploadImage(
                'review-poster-video',
                $config['website']['video']['poster']['extension'],
                ROOT . UPLOAD_PHOTO_L,
                $fileName
            );
            if ($photo) {
                $data['photo'] = $photo;
            }
        }

        // Upload video
        if ($this->func->hasFile('review-file-video')) {
            $fileName = $this->func->uploadName($_FILES['review-file-video']['name']);
            $video = $this->func->uploadImage(
                'review-file-video',
                implode('|', $config['website']['video']['extension']),
                ROOT . UPLOAD_VIDEO_L,
                $fileName
            );
            if ($video) {
                $data['video'] = $video;
            }
        }

        if (!empty($data)) {
            $data['id_parent'] = $commentId;
            $this->repository->createVideo($data);
        }
    }
}

