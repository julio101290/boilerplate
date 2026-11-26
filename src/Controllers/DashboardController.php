<?php

namespace julio101290\boilerplate\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use julio101290\boilerplate\Models\UserDashboardShortcutModel;

/**
 * Class DashboardController.
 */
class DashboardController extends BaseController
{
    protected UserDashboardShortcutModel $shortcutModel;

    public function __construct()
    {
        helper('auth');
        $this->shortcutModel = new UserDashboardShortcutModel();
    }

    public function index()
    {
        $userId   = (int) user()->id;
        $userName = user()->username;

        $allItems        = $this->shortcutModel->getUserItems($userId);
        $commonFunctions = [];
        $customTabs      = [];
        $defaultTabId    = 0; // 0 = Main Dashboard Overview tab

        foreach ($allItems as $item) {
            if (in_array($item['display_type'], ['shortcut', 'both'], true)) {
                $commonFunctions[] = $item;
            }

            if (in_array($item['display_type'], ['tab', 'both'], true)) {
                $customTabs[] = $item;
                if ((int) $item['is_default_tab'] === 1) {
                    $defaultTabId = (int) $item['id'];
                }
            }
        }

        $data = [
            'title'           => lang('Dashboard.title'),
            'userName'        => $userName,
            'commonFunctions' => $commonFunctions,
            'customTabs'      => $customTabs,
            'defaultTabId'    => $defaultTabId,
        ];

        return view('julio101290\boilerplate\Views\dashboard', $data);
    }

    /**
     * Save a dragged menu item as a common function, tab, or both.
     */
    public function saveShortcut(): ResponseInterface
    {
        $userId      = (int) user()->id;
        $title       = trim((string) $this->request->getPost('title'));
        $url         = trim((string) $this->request->getPost('url'));
        $icon        = trim((string) $this->request->getPost('icon')) ?: 'fas fa-link';
        $displayType = (string) $this->request->getPost('display_type');
        $isDefault   = (int) $this->request->getPost('is_default_tab') === 1 ? 1 : 0;

        if ($title === '' || !$this->isValidMenuUrl($url)) {
            return $this->jsonResponse(false, lang('Dashboard.msgInvalidItem'), [], 422);
        }

        if (!in_array($displayType, ['shortcut', 'tab', 'both'], true)) {
            $displayType = 'shortcut';
        }

        // Only tabs can be set as the default tab
        if ($displayType === 'shortcut') {
            $isDefault = 0;
        }

        // Check if this URL is already saved for this user; if so, update it
        $existing = $this->shortcutModel
            ->where('user_id', $userId)
            ->where('url', $url)
            ->first();

        if ($isDefault === 1) {
            $this->shortcutModel->clearDefaultTab($userId);
        }

        $payload = [
            'user_id'        => $userId,
            'title'          => $title,
            'url'            => $url,
            'icon'           => preg_replace('/[^a-zA-Z0-9\-\s]/', '', $icon),
            'display_type'   => $displayType,
            'is_default_tab' => $isDefault,
        ];

        if ($existing) {
            $this->shortcutModel->update($existing['id'], $payload);
            $savedId = (int) $existing['id'];
        } else {
            $savedId = (int) $this->shortcutModel->insert($payload);
        }

        $savedItem = $this->shortcutModel->find($savedId);

        return $this->jsonResponse(true, lang('Dashboard.msgSaved'), [
            'item' => $savedItem,
        ]);
    }

    /**
     * Delete a user's shortcut or tab.
     */
    public function deleteShortcut(int $id): ResponseInterface
    {
        $userId = (int) user()->id;
        $item   = $this->shortcutModel
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if (!$item) {
            return $this->jsonResponse(false, lang('Dashboard.msgError'), [], 404);
        }

        // Optional scope: if user removes only from shortcut or only from tab when display_type === 'both'
        $scope = (string) $this->request->getPost('scope');

        if ($item['display_type'] === 'both' && in_array($scope, ['shortcut', 'tab'], true)) {
            $newType   = $scope === 'shortcut' ? 'tab' : 'shortcut';
            $isDefault = $newType === 'shortcut' ? 0 : (int) $item['is_default_tab'];

            $this->shortcutModel->update($id, [
                'display_type'   => $newType,
                'is_default_tab' => $isDefault,
            ]);
        } else {
            $this->shortcutModel->delete($id);
        }

        return $this->jsonResponse(true, lang('Dashboard.msgDeleted'));
    }

    /**
     * Set default active tab for the current user (0 = main Dashboard overview).
     */
    public function setDefaultTab(): ResponseInterface
    {
        $userId     = (int) user()->id;
        $shortcutId = (int) $this->request->getPost('shortcut_id');

        if ($shortcutId > 0) {
            $exists = $this->shortcutModel
                ->where('id', $shortcutId)
                ->where('user_id', $userId)
                ->whereIn('display_type', ['tab', 'both'])
                ->first();

            if (!$exists) {
                return $this->jsonResponse(false, lang('Dashboard.msgError'), [], 404);
            }
        }

        $this->shortcutModel->setDefaultTab($userId, $shortcutId);

        return $this->jsonResponse(true, lang('Dashboard.msgDefaultUpdated'), [
            'defaultTabId' => $shortcutId,
        ]);
    }

    /**
     * Validate that the URL is not empty, '#', or a javascript: payload.
     */
    private function isValidMenuUrl(string $url): bool
    {
        if ($url === '' || $url === '#') {
            return false;
        }

        if (preg_match('/^(javascript:|data:|vbscript:)/i', $url)) {
            return false;
        }

        $parsed = parse_url($url);
        if (isset($parsed['fragment']) && empty($parsed['path']) && empty($parsed['query'])) {
            return false;
        }

        return true;
    }

    /**
     * Standard JSON response with refreshed CSRF token.
     */
    private function jsonResponse(bool $success, string $message, array $extra = [], int $statusCode = 200): ResponseInterface
    {
        return $this->response
            ->setStatusCode($statusCode)
            ->setJSON(array_merge([
                'success'   => $success,
                'message'   => $message,
                'csrfName'  => csrf_token(),
                'csrfHash'  => csrf_hash(),
            ], $extra));
    }
}