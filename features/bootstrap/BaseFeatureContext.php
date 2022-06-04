<?php

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Testwork\Hook\Scope\AfterSuiteScope;
use Illuminate\Support\Facades\Artisan;

/**
 * Defines application features from the specific context.
 */
class BaseFeatureContext extends AbstractFeatureContext
{
    /**
     * @Then /^I get behat token$/
     */
    public function iGetAuthTokenFromTheResponse()
    {
        return $this->getBehatToken();
    }

    /** @BeforeScenario */
    public function before(BeforeScenarioScope $scope)
    {
        Artisan::call('migrate:fresh');
    }

    /**
     * @AfterSuite
     */
    public static function deleteTestData(AfterSuiteScope $scope)
    {
//        Artisan::call('migrate:rollback');
    }

    /**
     * @When I send request to :path
     * @When I send request to :path using HTTP :method
     */
    public function sendRequestTo($path, $method = null)
    {
        $this->requestPath(self::API_VERSION_PREFIX . $path, $method);
    }
}
