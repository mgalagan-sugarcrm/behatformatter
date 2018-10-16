<?php
namespace elkan\BehatFormatter\Context;

use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\BeforeFeatureScope;

use Behat\Mink\Driver\Selenium2Driver;
use Behat\MinkExtension\Context\MinkContext;

/**
 * Class BehatFormatterContext
 *
 * @package elkan\BehatFormatter\Context
 */
class BehatFormatterContext extends MinkContext implements SnippetAcceptingContext
    {
    private $currentScenario;
    protected static $currentSuite;

    /**
     * Screen shot distanation folder
     * @var null|string
     */
    protected $screenShotPath;

    /**
     * BehatFormatterContext constructor.
     * @param null|string $screenShotPath
     */
    public function __construct($screenShotPath = null)
    {
        $this->screenShotPath = $screenShotPath;
    }

    /**
     * @BeforeFeature
     *
     * @param BeforeFeatureScope $scope
     *
     */
    public static function setUpScreenshotSuiteEnvironment4ElkanBehatFormatter(BeforeFeatureScope $scope)
    {
        self::$currentSuite = $scope->getSuite()->getName();
    }

    /**
     * @BeforeScenario
     */
    public function setUpScreenshotScenarioEnvironmentElkanBehatFormatter(BeforeScenarioScope $scope)
    {
        $this->currentScenario = $scope->getScenario();
    }

    /**
     * Take screen-shot when step fails.
     * Take screenshot on result step (Then)
     * Works only with Selenium2Driver.
     *
     * @AfterStep
     * @param AfterStepScope $scope
     */
    public function afterStepScreenShotOnFailure(AfterStepScope $scope)
    {
        if (empty($this->screenShotPath)) {
            return;
        }

        $currentSuite = self::$currentSuite;

        // Get screen shot on failed test
        if(!$scope->getTestResult()->isPassed())
        {
            $driver = $this->getSession()->getDriver();
            if (!$driver instanceof Selenium2Driver) {
                return;
            }

            if (!file_exists($this->screenShotPath) && !mkdir($this->screenShotPath, 0777, true)) {
                return;
            }

            //create filename string
            $fileName = $currentSuite.".".basename($scope->getFeature()->getFile()).'.'.$scope->getStep()->getLine().'.png';
            $fileName = str_replace('.feature', '', $fileName);

            $this->saveScreenshot($fileName, $this->screenShotPath);
        }
    }
}