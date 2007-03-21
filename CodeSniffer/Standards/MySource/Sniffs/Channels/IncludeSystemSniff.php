<?php
/**
 * A test to ensure that systems are included before they are used.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer_MySource
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

require_once 'PHP/CodeSniffer/Standards/AbstractScopeSniff.php';
require_once 'PHP/CodeSniffer/Tokens.php';

/**
 * A test to ensure that systems are included before they are used.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer_MySource
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class MySource_Sniffs_Channels_IncludeSystemSniff extends PHP_CodeSniffer_Standards_AbstractScopeSniff
{


    /**
     * Constructs a Squiz_Sniffs_Scope_MethodScopeSniff.
     */
    public function __construct()
    {
        parent::__construct(array(T_FUNCTION), array(T_DOUBLE_COLON), true);

    }//end __construct()


    /**
     * Processes the function tokens within the class.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file where this token was found.
     * @param int                  $stackPtr  The position where the token was found.
     * @param int                  $currScope The current scope opener token.
     *
     * @return void
     */
    protected function processTokenWithinScope(PHP_CodeSniffer_File $phpcsFile, $stackPtr, $currScope)
    {
        $tokens = $phpcsFile->getTokens();

        // Determine the name of the class that the static function
        // is being called on.
        $classNameToken = $phpcsFile->findPrevious(T_STRING, $stackPtr);
        $className      = $tokens[$classNameToken]['content'];

        // Go searching for includeSystem calls within this function, or the
        // inclusion of .inc files, which would be library files.
        $includedClasses = array();
        for ($i = ($currScope + 1); $i < $stackPtr; $i++) {
            if (strtolower($tokens[$i]['content']) === 'includesystem') {
                $systemName        = $phpcsFile->findNext(T_CONSTANT_ENCAPSED_STRING, ($i + 1));
                $systemName        = trim($tokens[$systemName]['content'], " '");
                $includedClasses[] = strtolower($systemName);
            } else if (in_array($tokens[$i]['code'], PHP_CodeSniffer_Tokens::$includeTokens) === true) {
                $filePath = $phpcsFile->findNext(T_CONSTANT_ENCAPSED_STRING, ($i + 1));
                $filePath = $tokens[$filePath]['content'];
                $filePath = trim($filePath, " '");
                $filePath = basename($filePath, '.inc');

                $includedClasses[] = strtolower($filePath);
            }
        }//end for

        // Now go searching for includeSystem or require/include
        // calls outside our scope. If we are in a class, look outside the
        // class. If we are not, look outside the function.
        $condPtr = $currScope;
        if ($phpcsFile->hasCondition($stackPtr, T_CLASS) === true) {
            foreach ($tokens[$stackPtr]['conditions'] as $condPtr => $condType) {
                if ($condType === T_CLASS) {
                    break;
                }
            }
        }

        for ($i = 0; $i < $condPtr; $i++) {
            // Skip other scopes.
            if (isset($tokens[$i]['scope_closer']) === true) {
                $i = $tokens[$i]['scope_closer'];
                continue;
            }

            if (strtolower($tokens[$i]['content']) === 'includesystem') {
                $systemName        = $phpcsFile->findNext(T_CONSTANT_ENCAPSED_STRING, ($i + 1));
                $systemName        = trim($tokens[$systemName]['content'], " '");
                $includedClasses[] = strtolower($systemName);
            } else if (in_array($tokens[$i]['code'], PHP_CodeSniffer_Tokens::$includeTokens) === true) {
                $filePath = $phpcsFile->findNext(T_CONSTANT_ENCAPSED_STRING, ($i + 1));
                $filePath = $tokens[$filePath]['content'];
                $filePath = trim($filePath, " '");
                $filePath = basename($filePath, '.inc');

                $includedClasses[] = strtolower($filePath);
            }
        }

        if (in_array(strtolower($className), $includedClasses) === false) {
            $error = "Static method called on non-included class or system \"$className\"; include system with Channels::includeSystem() or include class with require_once";
            $phpcsFile->addError($error, $stackPtr);
        }

    }//end processTokenWithinScope()


    /**
     * Processes a token that is found within the scope that this test is
     * listening to.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file where this token was found.
     * @param int                  $stackPtr  The position in the stack where this token
     *                                        was found.
     *
     * @return void
     */
    protected function processTokenOutsideScope(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Determine the name of the class that the static function
        // is being called on.
        $classNameToken = $phpcsFile->findPrevious(T_STRING, $stackPtr);
        $className      = $tokens[$classNameToken]['content'];

        $includedClasses = array();

        // Go searching for includeSystem or require/include
        // calls outside our scope.
        for ($i = 0; $i < $stackPtr; $i++) {
            // Skip other scopes.
            if (isset($tokens[$i]['scope_closer']) === true) {
                $i = $tokens[$i]['scope_closer'];
                continue;
            }

            if (strtolower($tokens[$i]['content']) === 'includesystem') {
                $systemName        = $phpcsFile->findNext(T_CONSTANT_ENCAPSED_STRING, ($i + 1));
                $systemName        = trim($tokens[$systemName]['content'], " '");
                $includedClasses[] = strtolower($systemName);
            } else if (in_array($tokens[$i]['code'], PHP_CodeSniffer_Tokens::$includeTokens) === true) {
                $filePath = $phpcsFile->findNext(T_CONSTANT_ENCAPSED_STRING, ($i + 1));
                $filePath = $tokens[$filePath]['content'];
                $filePath = trim($filePath, " '");
                $filePath = basename($filePath, '.inc');

                $includedClasses[] = strtolower($filePath);
            }
        }

        if (in_array(strtolower($className), $includedClasses) === false) {
            $error = "Static method called on non-included class or system \"$className\"; include system with Channels::includeSystem() or include class with require_once";
            $phpcsFile->addError($error, $stackPtr);
        }

    }//end processTokenOutsideScope()


}//end class

?>