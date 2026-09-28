<?php
namespace frontend\modules\loyalty\models;

use yii\base\Model;
use yii\base\Exception;
use frontend\modules\loyalty\services\RewardService;

/**
 * Use case (business logic model) template.
 * One class = one non-CRUD business process behind one API endpoint.
 * Replace `frontend` with the target application (backend/frontend/console/...)
 * and adjust the module path.
 */
class CollectRewardCalculation extends Model
{
    // Attributes are generated based on what input the business logic needs.
    // These stay public (and out of the constructor) so load()/rules() can
    // populate and validate them from the request.
    public $attribute1;
    public $attribute2;

    // Injected services use PHP8 constructor property promotion; $config is
    // forwarded to parent::__construct() for Yii::createObject()/Yii::configure().
    public function __construct(private RewardService $rewardService, $config = [])
    {
        parent::__construct($config);
    }

    public function rules()
    {
        // Rules are generated based on the attributes; pick the validator
        // that matches each attribute's expected type/constraints.
        return [
            [['attribute1', 'attribute2'], 'required'],
        ];
    }

    public function execute()
    {
        if (!$this->validate()) {
            return false;
        }

        // Business logic execution. Split large processes into private
        // methods; each subprocess should throw yii\base\Exception (never an
        // HTTP exception — that's the controller's responsibility) on failure.
        try {
            $this->subProcess1();
            $this->subProcess2();
            $this->subProcess3();
        } catch (Exception $exc) {
            $this->addError('result', $exc->getMessage());
        }

        return !$this->hasErrors();
    }

    public function toArray()
    {
        return []; // Define what should be returned as the HTTP response payload/body.
    }

    private function subProcess1()
    {
    }

    private function subProcess2()
    {
    }

    private function subProcess3()
    {
    }
}
