<?php

declare(strict_types=1);

namespace Rector\CakePHP;

use Nette\Utils\Strings;

/**
 * @inspired https://github.com/cakephp/upgrade/blob/756410c8b7d5aff9daec3fa1fe750a3858d422ac/src/Shell/Task/AppUsesTask.php
 */
final class FullyQualifiedClassNameResolver
{
    /**
     * A map of old => new for use statements that are missing
     *
     * @var string[]
     */
    public $implicitMap = [
        'App' => 'Cake\Core\App',
        'AppController' => 'App\Controller\AppController',
        'AppHelper' => 'App\View\Helper\AppHelper',
        'AppModel' => 'App\Model\AppModel',
        'Cache' => 'Cake\Cache\Cache',
        'CakeEventListener' => 'Cake\Event\EventListener',
        'CakeLog' => 'Cake\Log\Log',
        'CakePlugin' => 'Cake\Core\Plugin',
        'CakeTestCase' => 'Cake\TestSuite\TestCase',
        'CakeTestFixture' => 'Cake\TestSuite\Fixture\TestFixture',
        'Component' => 'Cake\Controller\Component',
        'ComponentRegistry' => 'Cake\Controller\ComponentRegistry',
        'Configure' => 'Cake\Core\Configure',
        'ConnectionManager' => 'Cake\Database\ConnectionManager',
        'Controller' => 'Cake\Controller\Controller',
        'Debugger' => 'Cake\Error\Debugger',
        'ExceptionRenderer' => 'Cake\Error\ExceptionRenderer',
        'Helper' => 'Cake\View\Helper',
        'HelperRegistry' => 'Cake\View\HelperRegistry',
        'Inflector' => 'Cake\Utility\Inflector',
        'Model' => 'Cake\Model\Model',
        'ModelBehavior' => 'Cake\Model\Behavior',
        'Object' => 'Cake\Core\Object',
        'Router' => 'Cake\Routing\Router',
        'Shell' => 'Cake\Console\Shell',
        'View' => 'Cake\View\View',
        // Also apply to already renamed ones
        'Log' => 'Cake\Log\Log',
        'Plugin' => 'Cake\Core\Plugin',
        'TestCase' => 'Cake\TestSuite\TestCase',
        'TestFixture' => 'Cake\TestSuite\Fixture\TestFixture',
    ];

    /**
     * This value used to be directory
     * So "/" in path should be "\" in namespace
     */
    public function resolveFromPseudoNamespaceAndShortClassName(string $pseudoNamespace, string $shortClass): string
    {
        $pseudoNamespace = $this->normalizeFileSystemSlashes($pseudoNamespace);

        // A. is knowinly renamed class?
        if (isset($this->implicitMap[$shortClass])) {
            return $this->implicitMap[$shortClass];
        }

        // Chop Lib out as locations moves those files to the top level.
        // But only if Lib is not the last folder.
        if (Strings::match($pseudoNamespace, '#\\\\Lib\\\\#')) {
            $pseudoNamespace = Strings::replace($pseudoNamespace, '#\\\\Lib#');
        }

        // B. is Cake native class?
        $cakePhpVersion = 'Cake\\' . $pseudoNamespace . '\\' . $shortClass;
        if (class_exists($cakePhpVersion) || interface_exists($cakePhpVersion)) {
            return $cakePhpVersion;
        }

        // C. is not plugin nor lib custom App class?
        if (Strings::contains($pseudoNamespace, '\\') && ! Strings::match($pseudoNamespace, '#(Plugin|Lib)#')) {
            return 'App\\' . $pseudoNamespace . '\\' . $shortClass;
        }

        return $pseudoNamespace . '\\' . $shortClass;
    }

    private function normalizeFileSystemSlashes(string $pseudoNamespace): string
    {
        return Strings::replace($pseudoNamespace, '#(/|\.)#', '\\');
    }
}
