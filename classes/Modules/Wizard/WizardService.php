<?php

namespace Xentral\Modules\Wizard;

use erpAPI;
use Xentral\Components\Database\Database;
use Xentral\Modules\User\Service\UserConfigService;
use Xentral\Modules\Wizard\Exception\InvalidArgumentException;
use Xentral\Modules\Wizard\Exception\NotFoundException;
use Xentral\Modules\Wizard\Exception\WizardExceptionInterface;

final class WizardService
{
    /** @var Database $db */
    private $db;

    /** @var UserConfigService $userConfig */
    private $userConfig;

    /**
     * @deprecated
     * @var erpAPI $erpApi
     */
    private $erpApi;

    /**
     * @param Database          $db
     * @param UserConfigService $userConfig
     * @param erpAPI            $erpApi
     */
    public function __construct(Database $db, UserConfigService $userConfig, erpAPI $erpApi)
    {
        $this->db = $db;
        $this->userConfig = $userConfig;
        $this->erpApi = $erpApi;
    }

    /**
     * @param int $userId
     *
     * @return array
     */
    public function getActiveWizardKeys($userId)
    {
        $userId = $this->ensureUserId($userId);

        $wizards = $this->db->fetchCol(
            'SELECT w.key 
             FROM wizard AS w 
             WHERE w.user_id = :user_id AND w.active = 1 
             ORDER BY w.created_at ASC',
            ['user_id' => $userId]
        );

        if (!is_array($wizards)) {
            return [];
        }

        return $wizards;
    }

    /**
     * @param int $userId
     *
     * @return string|null
     */
    public function getActiveWizardKey($userId)
    {
        $userId = $this->ensureUserId($userId);

        return $this->userConfig->tryGet('active_wizard', $userId);
    }

    /**
     * @param string $wizardKey
     * @param int    $userId
     *
     * @return void
     */
    public function setActiveWizardKey($wizardKey, $userId)
    {
        $wizardKey = $this->ensureWizardKey($wizardKey);
        $userId = $this->ensureUserId($userId);

        $this->userConfig->set('active_wizard', $wizardKey, $userId);
    }


    /**
     * @param string $wizardKey
     * @param string $stepKey
     * @param int    $userId
     *
     * @return void
     */
    public function setStepVisited($wizardKey, $stepKey, $userId)
    {
        $wizardKey = $this->ensureWizardKey($wizardKey);
        $stepKey = $this->ensureStepKey($stepKey);
        $userId = $this->ensureUserId($userId);

        if ($serializedCompletedWizardSteps = $this->userConfig->tryGet('completed_wizard_steps', $userId)) {
            $completedWizardSteps = unserialize($serializedCompletedWizardSteps, ['allowed_classes' => false]);
        } else {
            $completedWizardSteps = [];
        }

        $completedWizardSteps[$wizardKey][] = $stepKey;

        $this->userConfig->set('completed_wizard_steps', serialize($completedWizardSteps), $userId);

        // if the count of the completed steps are the same as the completed steps, delete the active_wizard key
        $wizard = $this->getWizardFromFile($wizardKey);
        $completableSteps = [];
        foreach ($wizard['step_groups'] as $group) {
            foreach ($group['sub_groups'] as $subGroup) {
                $completableSteps[] = $subGroup;
            }
        }
        if (count($completableSteps) === count($completedWizardSteps[$wizardKey])) {
            $this->userConfig->delete('active_wizard', $userId);
        }
    }

    /**
     * @param string $wizardKey
     * @param string $stepKey
     * @param int    $userId
     *
     * @return bool
     */
    public function hasStepVisited($wizardKey, $stepKey, $userId): bool
    {
        $wizardKey = $this->ensureWizardKey($wizardKey);
        $stepKey = $this->ensureStepKey($stepKey);
        $userId = $this->ensureUserId($userId);

        $value = $this->userConfig->tryGet('wizard_' . $wizardKey . '_step_' . $stepKey, $userId);

        return (int)$value === 1;
    }

    /**
     * @param string $wizardKey
     * @param int    $userId
     *
     * @return string|null
     */
    public function getLastVisitedStep($wizardKey, $userId)
    {
        $wizardKey = $this->ensureWizardKey($wizardKey);
        $userId = $this->ensureUserId($userId);

        return $this->userConfig->tryGet('wizard_' . $wizardKey . '_last_visited_step', $userId);
    }

    /**
     * @param string $wizardKey
     * @param int    $userId
     *
     * @return array
     */
    public function getWizard($wizardKey, $userId): array
    {
        $wizardKey = $this->ensureWizardKey($wizardKey);
        $userId = $this->ensureUserId($userId);

        $wizard = $this->getWizardFromFile($wizardKey);

        return $this->prepareWizardData($wizard, $userId);
    }

    protected function getWizardFromFile(string $key)
    {
        $filePath = __DIR__ . '/www/wizards/' . $key . '.json';

        if (!file_exists($filePath)) {
            throw new NotFoundException(sprintf("Wizard with key %s not found", $key));
        }
        $wizardJson = file_get_contents($filePath);

        return json_decode($wizardJson, true);
    }

    /**
     * @param string $wizardKey
     *
     * @return string
     */
    public function getFirstWizardLink(string $wizardKey)
    {
        $wizard = $this->getWizardFromFile($wizardKey);
        $firstStepGroup = current($wizard['step_groups']);
        $firstSubGroup = current($firstStepGroup['sub_groups']);
        $firstStep = current($firstSubGroup['steps']);

        return $firstStep['link'];
    }

    public function saveLastVisitedLink(string $key, string $link, $userId)
    {
        $userId = $this->ensureUserId($userId);

        $lastVisitedWizardLinks = $this->userConfig->tryGet('last_visited_wizard_links', $userId);
        $lastVisitedWizardLinks = $lastVisitedWizardLinks !== null ?
            unserialize($lastVisitedWizardLinks, ['allowed_classes' => false])
            : [];

        $lastVisitedWizardLinks[$key] = $link;

        $this->userConfig->set('last_visited_wizard_links', serialize($lastVisitedWizardLinks), $userId);
    }

    public function isMinimizedForUser($userId)
    {
        $userId = $this->ensureUserId($userId);

        $isMinimized = $this->userConfig->tryGet('wizard_is_minimized', $userId);

        return $isMinimized === null || $isMinimized === false
            ? false
            : true;
    }

    public function setMinimizedForUser($userId, $isMinimized)
    {
        $userId = $this->ensureUserId($userId);

        $this->userConfig->set('wizard_is_minimized', $isMinimized, $userId);

        return $isMinimized;
    }

    public function cancelActiveWizardForUser($userId)
    {
        $userId = $this->ensureUserId($userId);

        // delete wizard_is_minimized key
        $this->userConfig->delete('wizard_is_minimized', $userId);

        // delete active_wizard key
        $this->userConfig->delete('active_wizard', $userId);
    }

    public function isWizardCompletedForUser($wizardKey, $userId): bool
    {
        $wizardKey = $this->ensureWizardKey($wizardKey);
        $userId = $this->ensureUserId($userId);

        $wizard = $this->getWizard($wizardKey, $userId);
        $stepCount = 0;
        $completedStepCount = 0;
        foreach ($wizard['step_groups'] as $stepGroupName => $stepGroupContent) {
            $stepCount++;

            if (isset($stepGroupContent['completed']) && $stepGroupContent['completed'] === true) {
                $completedStepCount++;
            }
        }

        return $stepCount === $completedStepCount;
    }

    public function finishWizardForUser($wizardKey, $userId)
    {
        $wizardKey = $this->ensureWizardKey($wizardKey);
        $userId = $this->ensureUserId($userId);

        $this->cancelActiveWizardForUser($userId);

        // Remove last_visited_wizard_links for this wizard key
        $lastVisitedWizardLinks = $this->userConfig->tryGet('last_visited_wizard_links', $userId);
        $lastVisitedWizardLinks = $lastVisitedWizardLinks !== null
            ? unserialize($lastVisitedWizardLinks, ['allowed_classes' => false])
            : [];
        unset($lastVisitedWizardLinks[$wizardKey]);
        $this->userConfig->set('last_visited_wizard_links', serialize($lastVisitedWizardLinks), $userId);
    }

    public function resetWizardForUser($wizardKey, $userId)
    {
        $this->finishWizardForUser($wizardKey, $userId);
        // Remove completed_wizard_steps for this wizard key
        $completedWizardSteps = $this->userConfig->tryGet('completed_wizard_steps', $userId);
        $completedWizardSteps = $completedWizardSteps !== null
            ? unserialize($completedWizardSteps, ['allowed_classes' => false])
            : [];
        unset($completedWizardSteps[$wizardKey]);
        $this->userConfig->set('completed_wizard_steps', serialize($completedWizardSteps), $userId);
    }

    /**
     * @param string $wizardKey
     * @param int    $userId
     *
     * @throws NotFoundException
     *
     * @return void
     */
    public function activateWizard($wizardKey, $userId)
    {
        $wizardKey = $this->ensureWizardKey($wizardKey);
        $userId = $this->ensureUserId($userId);

        $wizardId = $this->getWizardIdByKey($wizardKey, $userId);
        if ($wizardId === false) {
            throw new NotFoundException(
                sprintf(
                    'Wizard with key "%s" and user id "%s" not found.',
                    $wizardKey,
                    $userId
                )
            );
        }

        $this->db->perform(
            'UPDATE wizard SET active = 1 WHERE id = :wizard_id AND user_id = :user_id',
            ['wizard_id' => $wizardId, 'user_id' => $userId]
        );
    }

    /**
     * @param string $wizardKey
     * @param int    $userId
     *
     * @throws NotFoundException
     *
     * @return void
     */
    public function deactivateWizard($wizardKey, $userId)
    {
        $wizardKey = $this->ensureWizardKey($wizardKey);
        $userId = $this->ensureUserId($userId);

        $wizardId = $this->getWizardIdByKey($wizardKey, $userId);
        if ($wizardId === false) {
            throw new NotFoundException(
                sprintf(
                    'Wizard with key "%s" and user id "%s" not found.',
                    $wizardKey,
                    $userId
                )
            );
        }

        $this->db->perform(
            'UPDATE wizard SET active = 0 WHERE id = :wizard_id AND user_id = :user_id',
            ['wizard_id' => $wizardId, 'user_id' => $userId]
        );
    }

    /**
     * @param string $wizardKey
     * @param int    $userId
     *
     * @throws NotFoundException
     *
     * @return void
     */
    public function deleteWizard($wizardKey, $userId)
    {
        $wizardKey = $this->ensureWizardKey($wizardKey);
        $userId = $this->ensureUserId($userId);

        $wizardId = $this->getWizardIdByKey($wizardKey, $userId);
        if ($wizardId === false) {
            throw new NotFoundException(
                sprintf(
                    'Wizard with key "%s" and user id "%s" not found.',
                    $wizardKey,
                    $userId
                )
            );
        }

        $this->deleteWizardById($wizardId);
    }

    /**
     * @param int $wizardId
     *
     * @return void
     */
    public function deleteWizardById($wizardId)
    {
        $wizardId = $this->ensureWizardId($wizardId);

        $this->db->beginTransaction();
        $this->db->perform('DELETE FROM wizard_step WHERE wizard_id = :wizard_id', ['wizard_id' => $wizardId]);
        $this->db->perform('DELETE FROM wizard WHERE id = :wizard_id LIMIT 1', ['wizard_id' => $wizardId]);
        $this->db->commit();
    }

    /**
     * @param int $wizardId
     *
     * @return array
     */
    public function generateTemplateFromExistingWizard($wizardId)
    {
        $wizardId = $this->ensureWizardId($wizardId);

        $settings = $this->db->fetchRow(
            'SELECT w.user_id, w.active, w.key, w.title, w.skip_link_text, w.params, w.options 
             FROM wizard AS w 
             WHERE w.id = :wizard_id',
            ['wizard_id' => $wizardId]
        );
        $settings['params'] = json_decode($settings['params'], true);
        $settings['options'] = json_decode($settings['options'], true);
        $settings['active'] = (bool)$settings['active'];

        $steps = $this->db->fetchAll(
            'SELECT s.key, s.link, s.title, s.caption, s.description, s.position, s.options 
             FROM wizard_step AS s 
             WHERE s.wizard_id = :wizard_id 
             ORDER BY s.position ASC, s.created_at ASC, s.id ASC',
            ['wizard_id' => $wizardId]
        );
        $position = 1;
        foreach ($steps as &$step) {
            $step['options'] = json_decode($step['options'], true);
            $step['position'] = $position;
            $position++;
        }

        return [
            'settings' => $settings,
            'steps'    => $steps,
        ];
    }

    /**
     * Creates or updates an wizard from decoded JSON template
     *
     * @param array    $data
     * @param int|null $overwriteUserId If not null, wizard will be assigned to this user
     *
     * @return int Updated or created wizard id
     */
    public function replaceWizard($data, $overwriteUserId = null)
    {
        if (!is_array($data)) {
            throw new InvalidArgumentException('Data is not an array.');
        }
        if (empty($data['settings'])) {
            throw new InvalidArgumentException('Settings property is empty.');
        }
        if (empty($data['steps'])) {
            throw new InvalidArgumentException('Steps property is empty.');
        }

        try {
            $stepIds = [];
            $this->db->beginTransaction();
            $wizardId = $this->replaceSettings($data['settings'], $overwriteUserId);
            foreach ($data['steps'] as $step) {
                $stepIds[] = $this->replaceSteps($step, $wizardId);
            }
            // Delete removed steps
            $this->db->perform(
                'DELETE FROM wizard_step WHERE wizard_id = :wizard_id AND id NOT IN (:step_ids)',
                ['wizard_id' => $wizardId, 'step_ids' => $stepIds]
            );
            $this->db->commit();

            return $wizardId;
            //
        } catch (WizardExceptionInterface $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * @param string $wizardKey
     * @param string $stepKey
     * @param int    $userId
     * @param bool   $checked
     *
     * @throws NotFoundException
     *
     * @return void
     */
    public function setStepChecked($wizardKey, $stepKey, $userId, $checked = true)
    {
        $wizardKey = $this->ensureWizardKey($wizardKey);
        $stepKey = $this->ensureStepKey($stepKey);
        $userId = $this->ensureUserId($userId);
        $checked = (bool)$checked;

        $stepId = (int)$this->db->fetchValue(
            'SELECT s.id 
             FROM wizard_step AS s 
             INNER JOIN wizard AS w ON w.id = s.wizard_id 
             WHERE w.key = :wizard_key AND s.key = :step_key AND w.user_id = :user_id',
            ['wizard_key' => $wizardKey, 'step_key' => $stepKey, 'user_id' => $userId]
        );

        if ($stepId === 0) {
            throw new NotFoundException(
                sprintf(
                    'Step not found. wizard_key="%s", step_key="%s", user_id="%s"',
                    $wizardKey,
                    $stepKey,
                    $userId
                )
            );
        }

        $this->db->perform(
            'UPDATE wizard_step SET checked = :checked WHERE id = :step_id LIMIT 1',
            ['step_id' => $stepId, 'checked' => $checked === true ? 1 : 0]
        );
    }

    /**
     * @param string $wizardKey
     * @param int    $userId
     *
     * @throws NotFoundException
     *
     * @return array
     */
    private function loadSettings($wizardKey, $userId)
    {
        $wizardKey = $this->ensureWizardKey($wizardKey);
        $userId = $this->ensureUserId($userId);

        $settings = $this->db->fetchRow(
            'SELECT w.id, w.user_id, w.key, w.title, w.skip_link_text, w.params, w.options, w.active 
             FROM `wizard` AS `w` 
             WHERE w.key = :wizard_key AND w.user_id = :user_id',
            ['wizard_key' => $wizardKey, 'user_id' => $userId]
        );

        if ($settings === false) {
            throw new NotFoundException(
                sprintf(
                    'Wizard with key "%s" and user id "%s" not found.',
                    $wizardKey,
                    $userId
                )
            );
        }

        $settings['id'] = (int)$settings['id'];
        $settings['user_id'] = (int)$settings['user_id'];
        $settings['active'] = (int)$settings['active'] === 1;
        $settings['params'] = json_decode($settings['params'], true);
        $settings['options'] = json_decode($settings['options'], true);
        $settings['skip_link_text'] =
            !empty($settings['skip_link_text'])
                ? $settings['skip_link_text']
                : 'Wizard überspringen';

        return $settings;
    }

    /**
     * @param string $wizardKey
     * @param int    $userId
     *
     * @throws NotFoundException
     *
     * @return array
     */
    private function loadSteps($wizardKey, $userId)
    {
        $wizardKey = $this->ensureWizardKey($wizardKey);
        $userId = $this->ensureUserId($userId);

        $steps = $this->db->fetchAll(
            'SELECT 
               s.id, s.wizard_id, s.key, s.link, s.title, s.caption, 
               s.description, s.position, s.options, s.checked 
             FROM wizard_step AS s 
             INNER JOIN wizard AS w ON w.id = s.wizard_id
             WHERE w.key = :wizard_key AND w.user_id = :user_id 
             ORDER BY s.position ASC, s.created_at ASC, s.id ASC',
            ['wizard_key' => $wizardKey, 'user_id' => $userId]
        );

        if ($steps === false) {
            throw new NotFoundException(
                sprintf(
                    'Wizard steps with key "%s" and user id "%s" not found.',
                    $wizardKey,
                    $userId
                )
            );
        }

        foreach ($steps as &$step) {
            $step['id'] = (int)$step['id'];
            $step['wizard_id'] = (int)$step['wizard_id'];
            $step['checked'] = (int)$step['checked'] === 1;
            $step['options'] = json_decode($step['options'], true);
            $step['position'] = (int)$step['position'];
        }

        return $steps;
    }

    /**
     * @param array $wizard
     * @param int   $userId
     *
     * @return array
     */
    private function prepareWizardData(array $wizard, int $userId)
    {
        $wizard = $this->addCompletedSteps($wizard, $userId);
        $wizard = $this->addLastVisitedLink($wizard, $userId);
        $wizard = $this->addMissingPermissions($wizard);
        if ($this->isMinimizedForUser($userId)) {
            $wizard['minimized'] = true;
        }

        return $wizard;
    }

    private function addCompletedSteps(array $wizard, int $userId)
    {
        if ($serializedCompletedWizardSteps = $this->userConfig->tryGet('completed_wizard_steps', $userId)) {
            $completedWizardSteps = unserialize($serializedCompletedWizardSteps, ['allowed_classes' => false]);
            if (isset($completedWizardSteps[$wizard['key']])) {
                $completedWizardSteps = array_map(
                    function ($step) {
                        return explode('-', $step);
                    },
                    $completedWizardSteps[$wizard['key']]
                );
                foreach ($completedWizardSteps as [$stepGroup, $subGroup]) {
                    $wizard['step_groups'][$stepGroup]['sub_groups'][$subGroup]['completed'] = true;
                }

                // add complete to the step_group if all sub_groups are completed
                foreach ($wizard['step_groups'] as $key => $stepGroup) {
                    $subGroupCount = count($stepGroup['sub_groups']);
                    $completedSubGroups = array_filter(
                        $stepGroup['sub_groups'],
                        function ($subGroup) {
                            return $subGroup['completed'] === true;
                        }
                    );

                    if ($subGroupCount === count($completedSubGroups)) {
                        $wizard['step_groups'][$key]['completed'] = true;
                    }
                }
            }
        }

        return $wizard;
    }

    private function addLastVisitedLink(array $wizard, int $userId)
    {
        if (!$lastVisitedWizardLinks = $this->userConfig->tryGet('last_visited_wizard_links', $userId)) {
            return $wizard;
        }
        $lastVisitedWizardLinks = unserialize($lastVisitedWizardLinks, ['allowed_classes' => false]);

        if (!isset($lastVisitedWizardLinks[$wizard['key']])) {
            return $wizard;
        }
        $lastVisitedLink = $lastVisitedWizardLinks[$wizard['key']];

        foreach ($wizard['step_groups'] as $setGroupKey => $stepGroup) {
            foreach ($stepGroup['sub_groups'] as $subGroupKey => $subGroup) {
                if (!isset($subGroup['completed']) || $subGroup['completed'] === false) {
                    $wizard['step_groups'][$setGroupKey]['sub_groups'][$subGroupKey]['link'] = $lastVisitedLink;
                }
            }
        }

        return $wizard;
    }

    /**
     * @param array    $data
     * @param int|null $overwriteUserId If not null, wizard will be assigned to this user
     *
     * @return int Updated or created wizard id
     */
    private function replaceSettings($data, $overwriteUserId = null)
    {
        $wizardKey = (string)$data['key'];
        $userId = $overwriteUserId;
        if ($userId === null || (int)$userId === 0) {
            $userId = $data['user_id'];
        }

        // Check required parameter
        if (empty($wizardKey)) {
            throw new InvalidArgumentException('Required settings property "key" is missing or empty.');
        }
        if (empty($data['title'])) {
            throw new InvalidArgumentException('Required settings property "title" is missing or empty.');
        }
        if (empty($userId) || (int)$userId === 0) {
            throw new InvalidArgumentException('Required settings property "user_id" is missing or empty.');
        }

        $wizardId = $this->getWizardIdByKey($wizardKey, $userId);

        // Create new wizard
        if ($wizardId === false) {
            $insert = $this->db->insert();
            $insert
                ->cols(
                    [
                        'user_id'        => $userId,
                        'key'            => $wizardKey,
                        'active'         => (int)$data['active'],
                        'title'          => (string)$data['title'],
                        'skip_link_text' => $data['skip_link_text'],
                        'params'         => json_encode($data['params']),
                        'options'        => json_encode($data['options']),
                    ]
                )
                ->set('created_at', 'NOW()')
                ->into('wizard');
            $this->db->perform($insert->getStatement(), $insert->getBindValues());

            return $this->db->lastInsertId();
        }

        // Update existing wizard
        $update = $this->db->update();
        $update
            ->table('wizard AS w')
            ->cols(
                [
                    'key',
                    'title',
                    'skip_link_text',
                    'active',
                    'params',
                    'options',
                ]
            )
            ->where('w.id = :wizard_id')
            ->where('w.user_id = :user_id')
            ->bindValues(
                [
                    'user_id'        => $userId,
                    'wizard_id'      => $wizardId,
                    'key'            => $wizardKey,
                    'active'         => (int)$data['active'],
                    'title'          => (string)$data['title'],
                    'skip_link_text' => $data['skip_link_text'],
                    'params'         => json_encode($data['params']),
                    'options'        => json_encode($data['options']),
                ]
            )
            ->limit(1);

        $this->db->perform($update->getStatement(), $update->getBindValues());

        return $wizardId;
    }

    /**
     * @param array $data
     * @param int   $wizardId
     *
     * @return int Updated or created step id
     */
    private function replaceSteps($data, $wizardId)
    {
        // Check required parameter
        if (empty($wizardId) || (int)$wizardId === 0) {
            throw new InvalidArgumentException('Required property "wizard_id" is missing or empty.');
        }
        if (empty($data['key'])) {
            throw new InvalidArgumentException('Required property "key" is missing or empty.');
        }
        if (empty($data['link'])) {
            throw new InvalidArgumentException('Required property "link" is missing or empty.');
        }
        if (empty($data['title'])) {
            throw new InvalidArgumentException('Required property "title" is missing or empty.');
        }

        $stepId = $this->getStepIdByKey($data['key'], $wizardId);

        // Create new step
        if ($stepId === false) {
            $insert = $this->db->insert();
            $insert
                ->cols(
                    [
                        'wizard_id'   => $wizardId,
                        'key'         => $data['key'],
                        'link'        => $data['link'],
                        'title'       => $data['title'],
                        'caption'     => $data['caption'],
                        'description' => substr($data['description'], 0, 1024),
                        'position'    => (int)$data['position'],
                        'options'     => json_encode($data['options']),
                        'checked'     => 0,
                    ]
                )
                ->set('created_at', 'NOW()')// Raw value
                ->into('wizard_step');
            $this->db->perform($insert->getStatement(), $insert->getBindValues());

            return $this->db->lastInsertId();
        }

        // Update existing step
        $update = $this->db->update();
        $update
            ->table('wizard_step AS s')
            ->cols(
                [
                    'link',
                    'title',
                    'caption',
                    'description',
                    'position',
                    'options',
                    'checked',
                ]
            )
            ->where('s.id = :step_id')
            ->where('s.wizard_id = :wizard_id')
            ->bindValues(
                [
                    'step_id'     => $stepId,
                    'wizard_id'   => $wizardId,
                    'key'         => $data['key'],
                    'link'        => $data['link'],
                    'title'       => $data['title'],
                    'caption'     => $data['caption'],
                    'description' => substr($data['description'], 0, 1024),
                    'position'    => (int)$data['position'],
                    'options'     => json_encode($data['options']),
                    'checked'     => 0,
                ]
            )
            ->limit(1);

        $this->db->perform($update->getStatement(), $update->getBindValues());

        return $stepId;
    }

    /**
     * @param array $options
     * @param array $params
     *
     * @return array Prepared options
     */
    private function prepareStepOptions($options = [], $params = [])
    {
        if (!is_array($options)) {
            return [];
        }
        if (!is_array($params)) {
            return $options;
        }

        foreach ($options as &$value) {
            if (is_array($value)) {
                $value = $this->prepareStepOptions($value, $params);
            }

            // Replace parameter values
            // Example: "foo_##shop_id##_bar" will be replace with "foo_1_bar", if $param['shop_id'] = 1
            preg_match_all('/\#\#(.+?)\#\#/', $value, $matches);
            if (!empty($matches[1])) {
                foreach ($matches[1] as $index => $paramName) {
                    if (isset($params[$paramName])) {
                        $replaceCount = 1;
                        $search = $matches[0][$index];
                        $replace = $params[$paramName];
                        $value = str_replace($search, $replace, $value, $replaceCount);
                    }
                }
            }
        }

        return $options;
    }

    /**
     * @param string $moduleName
     * @param string $methodName
     * @param array  $arguments
     *
     * @return bool
     */
    private function checkModuleCallback($moduleName, $methodName, $arguments = [])
    {
        $module = $this->erpApi->LoadModul($moduleName);
        if (!empty($module) && method_exists($module, $methodName)) {
            return (bool)call_user_func_array([$module, $methodName], $arguments);
        }

        return false;
    }

    /**
     * @param string $objectName
     * @param string $actionName
     * @param int    $objectId
     * @param int    $userId
     *
     * @return bool
     */
    private function checkObjectProtocolEntry($objectName, $actionName, $objectId, $userId)
    {
        $this->ensureUserId($userId);
        $username = $this->getUserNameById($userId);

        $count = $this->db->fetchValue(
            'SELECT COUNT(o.id) AS num 
             FROM objekt_protokoll AS o 
             WHERE o.objekt = :object_name AND o.objektid = :object_id 
             AND o.action_long = :action_name AND o.bearbeiter = :username',
            [
                'object_name' => (string)$objectName,
                'action_name' => (string)$actionName,
                'object_id'   => (int)$objectId,
                'username'    => (string)$username,
            ]
        );

        return (int)$count > 0;
    }

    /**
     * @param string $wizardKey
     * @param int    $userId
     *
     * @return int|false
     */
    private function getWizardIdByKey($wizardKey, $userId)
    {
        $wizardKey = $this->ensureWizardKey($wizardKey);
        $userId = $this->ensureUserId($userId);

        $wizardId = $this->db->fetchValue(
            'SELECT w.id FROM wizard AS w WHERE w.key = :key AND w.user_id = :user_id LIMIT 1',
            ['key' => $wizardKey, 'user_id' => $userId]
        );

        if ($wizardId === false) {
            return false;
        }

        return (int)$wizardId;
    }

    /**
     * @param string $stepKey
     * @param int    $wizardId
     *
     * @return int|false
     */
    private function getStepIdByKey($stepKey, $wizardId)
    {
        $stepKey = $this->ensureStepKey($stepKey);
        $wizardId = $this->ensureWizardId($wizardId);

        $stepId = $this->db->fetchValue(
            'SELECT s.id 
             FROM wizard_step AS s
             INNER JOIN wizard AS w ON s.wizard_id = w.id
             WHERE s.key = :step_key AND s.wizard_id = :wizard_id 
             LIMIT 1',
            ['step_key' => $stepKey, 'wizard_id' => $wizardId]
        );

        if ($stepId === false) {
            return false;
        }

        return (int)$stepId;
    }

    /**
     * @param int $userId
     *
     * @return string|false
     */
    private function getUserNameById($userId)
    {
        $userId = $this->ensureUserId($userId);

        $username = $this->db->fetchValue(
            'SELECT a.name FROM `user` AS u 
             INNER JOIN adresse AS a ON u.adresse = u.id
             WHERE u.id = :user_id',
            ['user_id' => $userId]
        );

        if ($username === false) {
            return false;
        }

        return (string)$username;
    }

    /**
     * @param string $key
     *
     * @throws InvalidArgumentException
     *
     * @return string
     */
    private function ensureWizardKey($key)
    {
        if (empty($key)) {
            throw new InvalidArgumentException('Required parameter "wizardKey" is empty.');
        }

        $filePath = __DIR__ . '/www/wizards/' . $key . '.json';
        if (!file_exists($filePath)) {
            throw new NotFoundException(sprintf("Wizard with key %s not found", $key));
        }

        return (string)$key;
    }

    /**
     * @param string $key
     *
     * @throws InvalidArgumentException
     *
     * @return string
     */
    private function ensureStepKey($key)
    {
        if (empty($key)) {
            throw new InvalidArgumentException('Required parameter "stepKey" is empty.');
        }

        return (string)$key;
    }

    /**
     * @param int $userId
     *
     * @throws InvalidArgumentException
     *
     * @return int
     */
    private function ensureUserId($userId)
    {
        if ((int)$userId === 0) {
            throw new InvalidArgumentException('Required parameter "userId" is invalid.');
        }

        return (int)$userId;
    }


    /**
     * @param int $wizardId
     *
     * @throws InvalidArgumentException
     *
     * @return int
     */
    private function ensureWizardId($wizardId)
    {
        if ((int)$wizardId === 0) {
            throw new InvalidArgumentException('Required parameter "wizardId" is invalid.');
        }

        return (int)$wizardId;
    }

    /**
     * @param array $wizard
     * @param int   $userId
     *
     * @return array
     */
    private function addMissingPermissions(array $wizard)
    {
        if (!isset($wizard['required_permissions'])) {
            return $wizard;
        }

        $missingPermissions = [];
        $missingModules = [];
        foreach ($wizard['required_permissions'] as $module => $actions) {
            $missing = [];
            if (!$this->erpApi->ModulVorhanden($module)) {
                $missingModules[] = $module;
                continue;
            }
            foreach ($actions as $action) {
                if (!$this->erpApi->RechteVorhanden($module, $action)) {
                    $missing[] = $action;
                }
            }
            if (!empty($missing)) {
                $missingPermissions[$module] = $missing;
            }
        }

        unset($wizard['required_permissions']);
        $wizard['missing_modules'] = $missingModules;
        $wizard['missing_permissions'] = $missingPermissions;

        return $wizard;
    }
}
