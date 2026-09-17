<?php
namespace frontend\modules\loyalty\models;

use yii\base\Model;
use yii\base\Exception;

/**
 * Business logic model template.
 * One model = one business operation. Replace `frontend` with the target
 * application (backend/frontend/console/...) and adjust the module path.
 */
class CollectRewardCalculation extends Model
{
    // Attributes are generated based on what input the business logic needs.
    public $attribute1;
    public $attribute2;

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
        // methods; each subprocess should throw yii\base\Exception on failure.
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
