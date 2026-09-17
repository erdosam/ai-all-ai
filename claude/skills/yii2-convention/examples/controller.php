<?php
namespace frontend\modules\loyalty\controllers;

use frontend\modules\loyalty\models\CollectRewardCalculation;
use frontend\modules\loyalty\models\CollectSubmittor;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\rest\Controller;

/**
 * Controller template. One action = one business logic model.
 * Controller name is at most 2 words and doubles as the URL path segment.
 * Permission format: @app[_<module-name>[_<sub-module-name>[..]]]:<controller-name>::<action-name>
 */
class CollectController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['frontend_loyalty:collect::calculate'],
                        'actions' => ['calculate'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['frontend_loyalty:collect::submit'],
                        'actions' => ['submit'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'calculate' => ['POST'],
                    'submit' => ['POST'],
                ],
            ],
        ];
    }

    public function actionCalculate()
    {
        $form = Yii::createObject(CollectRewardCalculation::class);
        $form->load($this->request->post(), ''); // Empty scenario string since it's a REST API.
        // $form->load(Yii::$app->request->post(), ''); // for old Yii2 versions without $this->request.
        $form->execute();
        return $form;
    }

    public function actionSubmit()
    {
        $form = Yii::createObject(CollectSubmittor::class);
        $form->load($this->request->post(), '');
        $form->execute();
        return $form;
    }
}
