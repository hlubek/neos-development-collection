<?php
namespace TYPO3\TYPO3CR\Eel\FlowQueryOperations;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.TYPO3CR".         *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Eel\FlowQuery\FlowQuery;
use TYPO3\Eel\FlowQuery\Operations\AbstractOperation;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;

/**
 * "has" operation working on NodeInterface. Reduce the set of matched elements
 * to those that have a child node that matches the selector or given subject.
 *
 * Accepts a selector, an array, an object, a traversable object & a FlowQuery
 * object as argument.
 */
class HasOperation extends AbstractOperation
{
    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected static $shortName = 'has';

    /**
     * {@inheritdoc}
     *
     * @var integer
     */
    protected static $priority = 100;

    /**
     * {@inheritdoc}
     *
     * @param array (or array-like object) $context onto which this operation should be applied
     * @return boolean TRUE if the operation can be applied onto the $context, FALSE otherwise
     */
    public function canEvaluate($context)
    {
        return count($context) === 0 || (isset($context[0]) && ($context[0] instanceof NodeInterface));
    }

    /**
     * {@inheritdoc}
     *
     * @param FlowQuery $flowQuery
     * @param array $arguments
     * @return void
     */
    public function evaluate(FlowQuery $flowQuery, array $arguments)
    {
        $subject = $arguments[0];
        if (!isset($subject) || empty($subject)) {
            $flowQuery->setContext(array());
            return;
        }

        $filteredContext = array();
        $context = $flowQuery->getContext();
        if (is_string($subject)) {
            foreach ($context as $contextElement) {
                $contextElementQuery = new FlowQuery(array($contextElement));
                $contextElementQuery->pushOperation('children', $arguments);
                if ($contextElementQuery->count() > 0) {
                    $filteredContext[] = $contextElement;
                }
            }
        } else {
            if ($subject instanceof \TYPO3\Eel\FlowQuery\FlowQuery) {
                $elements = $subject->get();
            } elseif ($subject instanceof \Traversable) {
                $elements = iterator_to_array($subject);
            } elseif (is_object($subject)) {
                $elements = array($subject);
            } elseif (is_array($subject)) {
                $elements = $subject;
            } else {
                throw new \TYPO3\Eel\FlowQuery\FizzleException('supplied argument for has operation not supported', 1332489625);
            }
            foreach ($elements as $element) {
                if ($element instanceof NodeInterface) {
                    $parentsQuery = new FlowQuery(array($element));
                    /** @var NodeInterface $parent */
                    foreach ($parentsQuery->parents(array())->get() as $parent) {
                        /** @var NodeInterface $contextElement */
                        foreach ($context as $contextElement) {
                            if ($contextElement->getIdentifier() === $parent->getIdentifier()) {
                                $filteredContext[] = $contextElement;
                            }
                        }
                    }
                }
            }
            $filteredContext = array_unique($filteredContext);
        }

        $flowQuery->setContext($filteredContext);
    }
}
