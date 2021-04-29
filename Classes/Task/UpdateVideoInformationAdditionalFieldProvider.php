<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/mediapool.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Task;

use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Scheduler\AdditionalFieldProviderInterface;

/**
 * Class UpdateVideoInformationAdditionalFieldProvider
 */
class UpdateVideoInformationAdditionalFieldProvider implements AdditionalFieldProviderInterface
{
    /**
     * Language label
     *
     * @var string
     */
    protected $ll = 'LLL:EXT:mediapool/Resources/Private/Language/locallang.xlf:scheduler.update_video_information.';

    /**
     * Gets additional fields to render in the form to add/edit a task
     *
     * @param array $taskInfo Values of the fields from the add/edit task form
     * @param \TYPO3\CMS\Scheduler\Task\AbstractTask $task The task object being edited. Null when adding a task!
     * @param \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $schedulerModule Reference to the scheduler backend module
     * @return array A two dimensional array, array('Identifier' => array('fieldId' => array('code' => '', 'label' => '', 'cshKey' => '', 'cshLabel' => ''))
     */
    public function getAdditionalFields(
        array &$taskInfo,
        $task,
        \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $schedulerModule
    ): array {
        $result = [];

        // mode
        $modes = [
            0 => LocalizationUtility::translate($this->ll . 'page_selection.0'),
            1 => LocalizationUtility::translate($this->ll . 'page_selection.1')
        ];
        $html = [];
        $html[] = '<select class="form-control" name="tx_scheduler[mediapool_video_mode]">';
        foreach ($modes as $value => $label) {
            $html[] =
                '<option value="' . $value . '"' . ($value == $task->mode ? ' selected' : '') . '>'
                . $label . '</option>';
        }
        $html[] = '</select>';
        $result['mediapool_video_mode'] = [
            'code' => implode(LF, $html),
            'label' => $this->ll . 'mode'
        ];

        // page selection
        $result['mediapool_video_page_selection'] = [
            'code' =>
                '<input class="form-control" name="tx_scheduler[mediapool_video_page_selection]" value="' .
                $task->pageSelection . '" />',
            'label' => $this->ll . 'page_selection'
        ];

        return $result;
    }

    /**
     * Validates the additional fields' values
     *
     * @param array $submittedData An array containing the data submitted by the add/edit task form
     * @param \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $schedulerModule Reference to the scheduler backend module
     * @return bool TRUE if validation was ok (or selected class is not relevant), FALSE otherwise
     */
    public function validateAdditionalFields(
        array &$submittedData,
        \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $schedulerModule
    ): bool {
        $submittedData['mediapool_video_mode'] = (int)$submittedData['mediapool_video_mode'];
        if ($submittedData['mediapool_video_mode'] === 0) {
            $submittedData['mediapool_video_page_selection'] = '';
        } else {
            // cast values inside the array
            $pageSelection = explode(',', $submittedData['mediapool_video_page_selection']);
            foreach ($pageSelection as &$uid) {
                $uid = (int)$uid;
            }
            $submittedData['mediapool_video_page_selection'] = implode(',', $pageSelection);
        }
        if (!MathUtility::isIntegerInRange($submittedData['mediapool_video_mode'], 0, 1)) {
            $schedulerModule->addMessage(
                LocalizationUtility::translate($this->ll . 'scheduler.update_video_information.unknown_mode'),
                FlashMessage::ERROR
            );
            return false;
        }
        return true;
    }

    /**
     * Takes care of saving the additional fields' values in the task's object
     *
     * @param array $submittedData An array containing the data submitted by the add/edit task form
     * @param \TYPO3\CMS\Scheduler\Task\AbstractTask $task Reference to the scheduler backend module
     */
    public function saveAdditionalFields(array $submittedData, \TYPO3\CMS\Scheduler\Task\AbstractTask $task)
    {
        $task->mode = $submittedData['mediapool_video_mode'];
        $task->pageSelection = $submittedData['mediapool_video_page_selection'];
    }
}
