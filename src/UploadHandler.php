<?php

namespace Tuezy;

/**
 * UploadHandler - Centralized file upload handling
 * Refactors repetitive upload logic throughout the codebase
 */
class UploadHandler
{
    private $func;
    private $d;
    private array $allowedExtensions;
    private string $uploadPath;
    private int $maxSize;

    public function __construct($func, $d, string $uploadPath = '', array $allowedExtensions = [], int $maxSize = 0)
    {
        $this->func = $func;
        $this->d = $d;
        $this->uploadPath = $uploadPath;
        $this->allowedExtensions = $allowedExtensions;
        $this->maxSize = $maxSize;
    }

    /**
     * Upload file with validation
     * 
     * @param string $fieldName Form field name
     * @param string|null $newName New file name (without extension)
     * @return string|false Uploaded file name or false on failure
     */
    public function upload(string $fieldName, ?string $newName = null)
    {
        if (!$this->func->hasFile($fieldName)) {
            return false;
        }

        // Get file name
        $fileName = $newName ?? $this->func->uploadName($_FILES[$fieldName]["name"]);

        // Upload file
        return $this->func->uploadImage(
            $fieldName,
            $this->getExtensionsString(),
            $this->uploadPath,
            $fileName
        );
    }

    /**
     * Upload and save to database
     * 
     * @param string $fieldName Form field name
     * @param string $table Table name
     * @param int $recordId Record ID to update
     * @param string $fieldNameInDb Database field name
     * @param string|null $newName New file name
     * @return bool Success status
     */
    public function uploadAndSave(string $fieldName, string $table, int $recordId, string $fieldNameInDb = 'file_attach', ?string $newName = null): bool
    {
        $uploadedFile = $this->upload($fieldName, $newName);
        
        if ($uploadedFile) {
            $updateData = [$fieldNameInDb => $uploadedFile];
            $this->d->where('id', $recordId);
            return $this->d->update($table, $updateData);
        }

        return false;
    }

    /**
     * Upload gallery image
     * 
     * @param string $fieldName Form field name
     * @param int $parentId Parent record ID
     * @param string $com Component name
     * @param string $type Type
     * @param string $kind Kind
     * @param string|null $hash Hash (for temporary uploads)
     * @return bool Success status
     */
    public function uploadGallery(string $fieldName, int $parentId, string $com, string $type, string $kind = 'man', ?string $hash = null): bool
    {
        if (!$this->func->hasFile($fieldName)) {
            return false;
        }

        $fileName = $this->func->uploadName($_FILES[$fieldName]["name"]);
        $uploadedFile = $this->func->uploadImage($fieldName, '.jpg|.png|.gif|.JPG|.PNG|.GIF', UPLOAD_PHOTO_L, $fileName);

        if (!$uploadedFile) {
            return false;
        }

        // Get max numb
        $maxNumb = $this->d->rawQueryOne(
            "select max(numb) as max_numb from #_gallery 
             where com = ? and type = ? and kind = ? and val = ? and id_parent = ?",
            [$com, $type, $kind, $type, $parentId]
        );

        $data = [
            'photo' => $uploadedFile,
            'numb' => ($maxNumb['max_numb'] ?? 0) + 1,
            'namevi' => '',
            'id_parent' => $parentId,
            'com' => $com,
            'type' => $type,
            'kind' => $kind,
            'val' => $type,
            'status' => 'hienthi',
            'date_created' => time(),
        ];

        if ($hash) {
            $data['hash'] = $hash;
        }

        return $this->d->insert('gallery', $data) !== false;
    }

    /**
     * Get allowed extensions as string
     * 
     * @return string
     */
    private function getExtensionsString(): string
    {
        if (empty($this->allowedExtensions)) {
            // Default allowed extensions
            return '.doc|.docx|.pdf|.rar|.zip|.ppt|.pptx|.DOC|.DOCX|.PDF|.RAR|.ZIP|.PPT|.PPTX|.xls|.xlsx|.jpg|.png|.gif|.JPG|.PNG|.GIF';
        }

        return implode('|', $this->allowedExtensions);
    }

    /**
     * Set allowed extensions
     * 
     * @param array $extensions Array of extensions (with or without dot)
     */
    public function setAllowedExtensions(array $extensions): void
    {
        $this->allowedExtensions = array_map(function($ext) {
            return ltrim($ext, '.');
        }, $extensions);
    }

    /**
     * Set upload path
     * 
     * @param string $path Upload path
     */
    public function setUploadPath(string $path): void
    {
        $this->uploadPath = $path;
    }

    /**
     * Set max file size
     * 
     * @param int $size Max size in bytes
     */
    public function setMaxSize(int $size): void
    {
        $this->maxSize = $size;
    }
}

