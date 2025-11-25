<?php

namespace Tuezy\Admin;

/**
 * AdminPermissionHelper - Permission management for admin
 * Handles role-based access control
 */
class AdminPermissionHelper
{
    private $func;
    private array $config;
    private array $restrictedActions = [];

    public function __construct($func, array $config)
    {
        $this->func = $func;
        $this->config = $config;
    }

    /**
     * Check if permission system is active
     * 
     * @return bool
     */
    public function isActive(): bool
    {
        return !empty($this->config['permission']['active']);
    }

    /**
     * Check if user has role (permission)
     * 
     * @return bool True if user has role (can access)
     */
    public function hasRole(): bool
    {
        return !$this->func->checkRole(); // checkRole returns true if NO role
    }

    /**
     * Check if action is restricted
     * 
     * @param string $action Action name
     * @return bool True if restricted
     */
    public function isRestricted(string $action): bool
    {
        $restrictedActions = [
            'man_admin',
            'add_admin',
            'edit_admin',
            'delete_admin',
            'man_member',
            'add_member',
            'edit_member',
            'delete_member',
            'permission_group',
            'add_permission_group',
            'edit_permission_group',
            'delete_permission_group',
        ];

        return in_array($action, $restrictedActions);
    }

    /**
     * Check if user can access action
     * 
     * @param string $action Action name
     * @return bool True if can access
     */
    public function canAccess(string $action): bool
    {
        // If permission system not active, allow all
        if (!$this->isActive()) {
            return true;
        }

        // If action is not restricted, allow
        if (!$this->isRestricted($action)) {
            return true;
        }

        // Check if user has role
        return $this->hasRole();
    }

    /**
     * Require permission for action
     * 
     * @param string $action Action name
     * @throws \RuntimeException If no permission
     */
    public function requirePermission(string $action): void
    {
        if (!$this->canAccess($action)) {
            throw new \RuntimeException("Bạn không có quyền truy cập vào khu vực này");
        }
    }

    /**
     * Set restricted actions
     * 
     * @param array $actions Array of restricted actions
     */
    public function setRestrictedActions(array $actions): void
    {
        $this->restrictedActions = $actions;
    }

    /**
     * Add restricted action
     * 
     * @param string $action Action name
     */
    public function addRestrictedAction(string $action): void
    {
        if (!in_array($action, $this->restrictedActions)) {
            $this->restrictedActions[] = $action;
        }
    }
}

