## Application
By default, using Yii2 advanced template, the applications are `backend`, `frontend`, and `console`.
The `common` is not considered as application.
Application can be added, e.g. in mall there's application `api`.
In this document, alias `@app` will be used to point to "current" Yii application.
But, while generating the code, `@app` should be replaced by `backend`, `frontend`, etc.

## Module
Is group of features that has similar purpose. For example loyalty feature.
In loyalty feature, user can do collect, redeem, manage loyalty rules, etc.
The module folder format is `@app[/modules/<MODULE-NAME>[/modules/<SUB-MODULE-NAME>[..]]]`

## Service
Is a class that handle non business logic. Such as storing file to cloud storage, sending request to vendor APIs, etc.

## Business Logic Model
Will be used as a class that handle one and only one business model.
The model must be in `@app[/modules/<MODULE-NAME>[/modules/<SUB-MODULE-NAME>[..]]]/models/` folder.

The model can be used by controller whithin the same module, and not its submodule.
E.g. model `@app/modules/loyalty/models/CollectRewardCalculation.php` is only used in a contoller inside folder `@app/modules/loyalty/controllers/` and test unit class.

The model should have a test unit, the name format is 
`@app/tests/unit/[<MODULE-NAME>_[<SUB-MODULE-NAME>_[..]]][MODEL-NAME]Test.php`.
Examples of test unit name are `@app/tests/unit/Loyalty_CollectRewardCalculationTest.php`, and `@app/tests/unit/Loyalty_Manager_CollectRewardRuleTest.php`.

The model will have one public function that execute the logic, name it `execute()`.

The model can depend on services. These services should be injected through `__construct()` function.

## Throwing exception
All http errors should be thrown from controller, not model. All errors that are thrown from model is wrapped / enveloped by try-catch in the main function.

Example and format of the model class:
```php
namespace frontend\modules\loyalty\models;
use yii\base\Model;
class CollectRewardCalculation extends Model
{
    // attributes are generated based on what input required for a business logic to run
    public $attribute1;
    public $attribute2;
    public function rules()
    {
        // rules are generated based on the attributes, ai should decide the validator for each attributes
        return [];
    }
    public function execute()
    {
        // validate first
        if (!$this->validate()) {
            return false;
        }
        // bussiness logic execution
        // large process should be separated in a private method
        try {
            // each subprocess should throw yii\base\Exception if it failed
            $this->subProcess1();
            $this->subProcess2();
            $this->subProcess3();
        } catch (Exception $exc) {
            $this->addError("result", $exc->getMessage());
        }
        return !$this->hasErrors();
    }
    public function toArray()
    {
        return []; // define what should be return as http response payload/body
    }
}
```
### Test unit of a bussiness logic model
It's just example, but the format should be like this.
```php
use frontend\modules\loyalty\models\CollectRewardCalculation;
class Loyalty_CollectRewardCalculationTest extends Codeception\Test\Unit
{
    public function _fixtures() { ... }
    protected function before() { ... }
    protected function after() { ... }
    public function testHappyCalculation()
    {
        $form = Yii::createObject(CollectRewardCalculation::class);
        $form->setAttributes([...]);
        $result = $form->execute();
        codecept_debug($form->getErrorSummary(true));
        verify($result)->true();
        // other verifications if any
    }
}
```

## Controller
Is group of actions where each actions will use one business logic model.
Controller name should be at most 2 words since it will be used as path. Name the container as sub feature.
E.g. in `loyalty` module, since collect is part of a loyalty, `@app/modules/loyalty/controllers/CollectController.php`

One action will handle/use one business logic model.
The action is guarded by permission `@app[_<module-name>[_<sub-module-name>[..]]]:collect::calculate`.
E.g. for model `@app\modules\loyalty\models\CollectRewardCalculation` the action in the controller `CollectController` would be:
```php
namespace frontend\modules\loyalty\controllers;
use frontend\modules\loyalty\models\CollectRewardCalculation;
use frontend\modules\loyalty\models\CollectSubmittor;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\rest\Controller;
class CollectController extends Controller
{
    public function behaviors()
    {
        return [
            "access" => [
                "class" => AccessControl::class,
                "rules" => [
                    [
                        "allow" => true,
                        "roles" => ["frontend_loyalty:collect::calculate"],
                        "actions" => ["calculate"],
                    ],
                    [
                        "allow" => true,
                        "roles" => ["frontend_loyalty:collect::submit"],
                        "actions" => ["submit"],
                    ],
                ],
            ],
            "verbs" => [
                "class" => VerbFilter::class,
                "actions" => [
                    "calculate" => ["POST"],
                    "submit" => ["POST"],
                ],
            ],
        ];
    }
    public function actionCalculate()
    {
        $form = Yii::createObject(CollectRewardCalculation::class);
        $form->load($this->request->post(), ""); // empty string since it's rest API
        //$form->load(Yii::$app->request->post(), ""); // for old yii2
        $form->execute();
        return $form;
    }
    public function actionSubmit()
    {
        $form = Yii::createObject(CollectSubmittor::class);
        $form->load(Yii::$app->request->post(), "");
        $form->execute();
        return $form;
    }
}
```