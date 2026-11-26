<?php

namespace julio101290\boilerplate\Models;

use CodeIgniter\Model;

class UserDashboardShortcutModel extends Model
{
    protected $table            = 'user_dashboard_shortcuts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'user_id',
        'title',
        'url',
        'icon',
        'display_type',
        'is_default_tab',
        'sort_order',
    ];

    /**
     * Get all configured items for a specific user.
     */
    public function getUserItems(int $userId): array
    {
        return $this->where('user_id', $userId)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
    }

    /**
     * Clear default tab flag for a user (reverts default to main Dashboard tab).
     */
    public function clearDefaultTab(int $userId): bool
    {
        return $this->where('user_id', $userId)
            ->set(['is_default_tab' => 0])
            ->update();
    }

    /**
     * Set a specific shortcut/tab as the default tab for a user.
     */
    public function setDefaultTab(int $userId, int $shortcutId): bool
    {
        $this->clearDefaultTab($userId);

        if ($shortcutId <= 0) {
            return true;
        }

        return $this->where('user_id', $userId)
            ->where('id', $shortcutId)
            ->whereIn('display_type', ['tab', 'both'])
            ->set(['is_default_tab' => 1])
            ->update();
    }
}