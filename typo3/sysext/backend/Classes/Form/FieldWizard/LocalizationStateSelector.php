<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace TYPO3\CMS\Backend\Form\FieldWizard;

use TYPO3\CMS\Backend\Form\AbstractNode;
use TYPO3\CMS\Core\DataHandling\Localization\State;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Page\JavaScriptModuleInstruction;
use TYPO3\CMS\Core\Utility\StringUtility;

/**
 * Allows to define the localization state per field.
 */
class LocalizationStateSelector extends AbstractNode
{
    /**
     * Render the radio buttons if enabled
     */
    public function render(): array
    {
        $languageService = $this->getLanguageService();
        $result = $this->initializeResultArray();

        $fieldName = $this->data['fieldName'];
        $fieldId = StringUtility::getUniqueId('formengine-localization-state-selector-');
        $l10nStateFieldName = 'l10n_state';

        $localizationState = State::fromJSON(
            $this->data['tableName'],
            $this->data['databaseRow'][$l10nStateFieldName] ?? null
        );

        if (
            $localizationState === null
            || !isset($this->data['defaultLanguageRow'])
            || !isset($this->data['processedTca']['columns'][$fieldName]['config']['behaviour']['allowLanguageSynchronization'])
            || !$this->data['processedTca']['columns'][$fieldName]['config']['behaviour']['allowLanguageSynchronization']
        ) {
            return $result;
        }

        $l10nParentFieldName = $this->data['processedTca']['ctrl']['transOrigPointerField'] ?? null;
        $l10nSourceFieldName = $this->data['processedTca']['ctrl']['translationSource'] ?? null;

        $sourceLanguageTitle = '';
        $fieldValueInParentRow = '';
        $fieldValueInSourceRow = null;
        if ($l10nParentFieldName && $this->data['databaseRow'][$l10nParentFieldName] > 0) {
            if ($l10nSourceFieldName && $this->data['databaseRow'][$l10nSourceFieldName] > 0) {
                $languageField = $this->data['processedTca']['ctrl']['languageField'] ?? null;
                if ($languageField
                    && isset($this->data['sourceLanguageRow'][$languageField])
                    && $this->data['sourceLanguageRow'][$languageField] > 0
                ) {
                    $languageUidOfSourceRow = $this->data['sourceLanguageRow'][$languageField];
                    $sourceLanguageTitle = $this->data['systemLanguageRows'][$languageUidOfSourceRow]['title'] ?? '';
                    $fieldValueInSourceRow = $this->data['sourceLanguageRow'][$fieldName] ?? null;
                }
            }
            $fieldValueInParentRow = (string)$this->data['defaultLanguageRow'][$fieldName];
        }

        $fieldElementName = 'data[' . htmlspecialchars($this->data['tableName']) . ']'
            . '[' . htmlspecialchars((string)$this->data['databaseRow']['uid']) . ']'
            . '[' . htmlspecialchars($l10nStateFieldName) . ']'
            . '[' . htmlspecialchars($this->data['fieldName']) . ']';

        $html = [];
        $html[] = '<div class="t3js-l10n-state-container">';
        $html[] =   '<div class="form-legend">';
        $html[] =       $languageService->sL('LLL:EXT:core/Resources/Private/Language/locallang_wizards.xlf:localizationStateSelector.header');
        $html[] =   '</div>';
        $html[] =   '<div class="form-check">';
        $html[] =       '<input';
        $html[] =           ' id="' . $fieldId . '-custom"';
        $html[] =           ' type="radio"';
        $html[] =           ' name="' . htmlspecialchars($fieldElementName) . '"';
        $html[] =           ' class="form-check-input t3js-l10n-state-custom"';
        $html[] =           ' value="custom"';
        $html[] =           $localizationState->isCustomState($fieldName) ? ' checked="checked"' : '';
        $html[] =           ' data-original-language-value=""';
        $html[] =       '>';
        $html[] =       '<label';
        $html[] =           ' for="' . $fieldId . '-custom"';
        $html[] =           ' class="form-check-label"';
        $html[] =       '>';
        $html[] =           $languageService->sL('LLL:EXT:core/Resources/Private/Language/locallang_wizards.xlf:localizationStateSelector.customValue');
        $html[] =       '</label>';
        $html[] =   '</div>';
        $html[] =   '<div class="form-check">';
        $html[] =       '<input';
        $html[] =           ' id="' . $fieldId . '-parent"';
        $html[] =           ' type="radio"';
        $html[] =           ' name="' . htmlspecialchars($fieldElementName) . '"';
        $html[] =           ' class="form-check-input"';
        $html[] =           ' value="parent"';
        $html[] =           $localizationState->isParentState($fieldName) ? ' checked="checked"' : '';
        $html[] =           ' data-original-language-value="' . htmlspecialchars((string)$fieldValueInParentRow) . '"';
        $html[] =       '>';
        $html[] =       '<label';
        $html[] =           ' for="' . $fieldId . '-parent"';
        $html[] =           ' class="form-check-label"';
        $html[] =       '>';
        $html[] =           $languageService->sL('LLL:EXT:core/Resources/Private/Language/locallang_wizards.xlf:localizationStateSelector.defaultLanguageValue');
        $html[] =       '</label>';
        $html[] =   '</div>';
        if ($fieldValueInSourceRow !== null) {
            $html[] = '<div class="form-check">';
            $html[] =   '<input';
            $html[] =       ' id="' . $fieldId . '-source"';
            $html[] =       ' type="radio"';
            $html[] =       ' name="' . htmlspecialchars($fieldElementName) . '"';
            $html[] =       ' class="form-check-input"';
            $html[] =       ' value="source"';
            $html[] =       $localizationState->isSourceState($fieldName) ? ' checked="checked"' : '';
            $html[] =       ' data-original-language-value="' . htmlspecialchars((string)$fieldValueInSourceRow) . '"';
            $html[] =   '>';
            $html[] =   '<label';
            $html[] =       ' for="' . $fieldId . '-source"';
            $html[] =       ' class="form-check-label"';
            $html[] =   '>';
            $html[] =       sprintf($languageService->sL('LLL:EXT:core/Resources/Private/Language/locallang_wizards.xlf:localizationStateSelector.sourceLanguageValue'), htmlspecialchars($sourceLanguageTitle));
            $html[] =   '</label>';
            $html[] = '</div>';
        }
        $html[] = '</div>';

        $result['javaScriptModules'][] = JavaScriptModuleInstruction::create(
            '@typo3/backend/form-engine/field-wizard/localization-state-selector.js'
        )->instance($fieldElementName);
        $result['html'] = implode(LF, $html);
        return $result;
    }

    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
