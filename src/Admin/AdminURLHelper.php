<?php

namespace Tuezy\Admin;

use Tuezy\SecurityHelper;

/**
 * AdminURLHelper - URL building for admin
 * Helps build return URLs with filters and parameters
 */
class AdminURLHelper
{
    private array $urlParams = [];
    private string $baseUrl;

    public function __construct(string $baseUrl = 'index.php')
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * Build URL from POST data
     * 
     * @param array $dataUrl POST data
     * @param array $urlFields Fields to include in URL
     * @return string URL string
     */
    public function buildFromPost(array $dataUrl, array $urlFields = ['id_list', 'id_cat', 'id_item', 'id_sub', 'id_brand']): string
    {
        $strUrl = "";
        
        foreach ($urlFields as $field) {
            if (isset($dataUrl[$field])) {
                $value = SecurityHelper::sanitize($dataUrl[$field]);
                $strUrl .= "&" . $field . "=" . urlencode($value);
                $this->urlParams[$field] = $value;
            }
        }

        return $strUrl;
    }

    /**
     * Build URL from REQUEST
     * 
     * @param array $urlFields Fields to include
     * @param array $additionalFields Additional fields (keyword, comment_status, etc.)
     * @return string URL string
     */
    public function buildFromRequest(array $urlFields = ['id_list', 'id_cat', 'id_item', 'id_sub', 'id_brand'], array $additionalFields = []): string
    {
        $strUrl = "";

        // Standard URL fields
        foreach ($urlFields as $field) {
            if (isset($_REQUEST[$field])) {
                $value = SecurityHelper::sanitizeRequest($field);
                $strUrl .= "&" . $field . "=" . urlencode($value);
                $this->urlParams[$field] = $value;
            }
        }

        // Additional fields
        foreach ($additionalFields as $field) {
            if (isset($_REQUEST[$field])) {
                $value = SecurityHelper::sanitizeRequest($field);
                $strUrl .= "&" . $field . "=" . urlencode($value);
                $this->urlParams[$field] = $value;
            }
        }

        return $strUrl;
    }

    /**
     * Add parameter to URL
     * 
     * @param string $key Parameter key
     * @param mixed $value Parameter value
     * @return self
     */
    public function addParam(string $key, $value): self
    {
        $this->urlParams[$key] = $value;
        return $this;
    }

    /**
     * Remove parameter
     * 
     * @param string $key Parameter key
     * @return self
     */
    public function removeParam(string $key): self
    {
        unset($this->urlParams[$key]);
        return $this;
    }

    /**
     * Get URL string
     * 
     * @param string $com Component
     * @param string $act Action
     * @param string $type Type
     * @return string Full URL
     */
    public function getUrl(string $com, string $act, string $type = ''): string
    {
        $url = $this->baseUrl . "?com=" . urlencode($com) . "&act=" . urlencode($act);
        
        if (!empty($type)) {
            $url .= "&type=" . urlencode($type);
        }

        foreach ($this->urlParams as $key => $value) {
            $url .= "&" . urlencode($key) . "=" . urlencode($value);
        }

        return $url;
    }

    /**
     * Get return URL (for redirects after save)
     * 
     * @param string $com Component
     * @param string $act Action
     * @param string $type Type
     * @return string Return URL
     */
    public function getReturnUrl(string $com, string $act, string $type = ''): string
    {
        return $this->getUrl($com, $act, $type);
    }

    /**
     * Reset URL parameters
     * 
     * @return self
     */
    public function reset(): self
    {
        $this->urlParams = [];
        return $this;
    }

    /**
     * Get all parameters
     * 
     * @return array
     */
    public function getParams(): array
    {
        return $this->urlParams;
    }
}

