<?php
namespace TYPO3\Neos\Routing\Exception;

/*                                                                        *
     * This script belongs to the TYPO3 Flow package "TYPO3.Neos".            *
     *                                                                        *
     * It is free software; you can redistribute it and/or modify it under    *
     * the terms of the GNU General Public License, either version 3 of the   *
     * License, or (at your option) any later version.                        *
     *                                                                        *
     * The TYPO3 project - inspiring people to share!                         *
     *                                                                        */

/**
 * An "invalid dimension preset combination" exception
 */
class InvalidDimensionPresetCombinationException extends \TYPO3\Neos\Routing\Exception
{
    /**
     * @var integer
     */
    protected $statusCode = 404;
}
