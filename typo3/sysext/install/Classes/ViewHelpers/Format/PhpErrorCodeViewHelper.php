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

namespace TYPO3\CMS\Install\ViewHelpers\Format;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Transform PHP error code to readable text
 *
 * @internal
 */
final class PhpErrorCodeViewHelper extends AbstractViewHelper
{
    protected static array $levelNames = [
        E_ERROR => 'E_ERROR',
        E_WARNING => 'E_WARNING',
        E_PARSE => 'E_PARSE',
        E_NOTICE => 'E_NOTICE',
        E_CORE_ERROR => 'E_CORE_ERROR',
        E_CORE_WARNING => 'E_CORE_WARNING',
        E_COMPILE_ERROR => 'E_COMPILE_ERROR',
        E_COMPILE_WARNING => 'E_COMPILE_WARNING',
        E_USER_ERROR => 'E_USER_ERROR',
        E_USER_WARNING => 'E_USER_WARNING',
        E_USER_NOTICE => 'E_USER_NOTICE',
        E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
        E_DEPRECATED => 'E_DEPRECATED',
        E_USER_DEPRECATED => 'E_USER_DEPRECATED',
        // @todo Remove intermediate constant E_STRICT_DEPRECATED with TYPO3 v14. E_STRICT (2048) constant deprecated since PHP 8.4.0 RC1.
        E_STRICT_DEPRECATED => 'E_STRICT',
    ];

    public function initializeArguments(): void
    {
        $this->registerArgument('phpErrorCode', 'int', '', true);
    }

    /**
     * Render a readable string for PHP error code.
     */
    public function render(): string
    {
        $phpErrorCode = (int)$this->arguments['phpErrorCode'];
        $levels = [];
        if (($phpErrorCode & E_ALL) == E_ALL) {
            $levels[] = 'E_ALL';
            $phpErrorCode &= ~E_ALL;
        }
        foreach (self::$levelNames as $level => $name) {
            if (($phpErrorCode & $level) == $level) {
                $levels[] = $name;
            }
        }
        $output = '';
        if (!empty($levels)) {
            $output = implode(' | ', $levels);
        }
        return $output;
    }
}
