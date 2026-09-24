<?php
use frontend\modules\loyalty\models\CollectRewardCalculation;

/**
 * Codeception unit test template for a business logic model.
 * File name format: @app/tests/unit/[<MODULE-NAME>_[<SUB-MODULE-NAME>_[..]]][MODEL-NAME]Test.php
 * e.g. Loyalty_CollectRewardCalculationTest.php, Loyalty_Manager_CollectRewardRuleTest.php
 */
class Loyalty_CollectRewardCalculationTest extends Codeception\Test\Unit
{
    public function _fixtures()
    {
        return [];
    }

    protected function before()
    {
    }

    protected function after()
    {
    }

    public function testHappyCalculation()
    {
        $form = Yii::createObject(CollectRewardCalculation::class);
        $form->setAttributes([
            'attribute1' => 'value1',
            'attribute2' => 'value2',
        ]);
        $result = $form->execute();
        codecept_debug($form->getErrorSummary(true));
        verify($result)->true();
        // Add further assertions on $form->toArray() as needed.
    }
}
