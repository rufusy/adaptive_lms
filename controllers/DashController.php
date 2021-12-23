<?php

namespace app\controllers;

use yii\filters\AccessControl;

class DashController extends BaseController
{
    /**
     * Configure controller behaviours
     * @return array[]
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Displays the dashboard.
     */
    public function actionIndex(): string
    {
        return $this->render('index',[
            'title'=> $this->createPageTitle('Dashboard')
        ]);
    }
}