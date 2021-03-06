.. _custom-eel-helpers:

Custom Eel Helper
=================

Eel Helpers provide methods that can be used inside of Eel expressions. That is mostly used to extend the capabilities
for data-aquisition and processing of TypoScript.

The first step is to create the EelHelper class. Every Helper has to implement the interface
``TYPO3\Eel\ProtectedContextAwareInterface``.

.. code-block:: php

	namespace Vendor\Site\Eel\Helper;

	use TYPO3\Flow\Annotations as Flow;
	use TYPO3\Eel\ProtectedContextAwareInterface;

	class ExampleHelper implements ProtectedContextAwareInterface {

		/**
		 * Wrap the incoming string in curly brackets
		 *
		 * @param $text string
		 * @return string
		 */
		public function wrapInCurlyBrackets($text) {
			return '{' . $text . '}';
		}

		/**
		 * All methods are considered safe, i.e. can be executed from within Eel
		 *
		 * @param string $methodName
		 * @return boolean
		 */
		public function allowsCallOfMethod($methodName) {
			return TRUE;
		}

	}

Afterwards the namespace of the Helper has to be registered for usage in TypoScript in the *Settings.yaml* of the package:

.. code-block:: yaml

  TYPO3:
    TypoScript:
      defaultContext:
        'Vendor.Example': 'Vendor\Site\Eel\Helper\ExampleHelper'

In TypoScript you can call the methods of the helper inside of EelExpressions::

	exampleEelValue = ${Vendor.Example.wrapInCurlyBrackets('Hello World')}
