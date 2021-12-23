<?php

use kartik\growl\Growl;

foreach (Yii::$app->session->getAllFlashes() as $flash) {
    if (!empty($flash)) {
        $type = Growl::TYPE_SUCCESS;
        $title = 'Well done!';

        if ($flash['type'] === 'danger') {
            $type = Growl::TYPE_DANGER;
            $title = 'Oh snap!';
        }

        try {
            echo Growl::widget([
                'type' => $type,
                'title' => $title,
                'icon' => 'fas fa-check-circle',
                'body' => $flash['message'],
                'showSeparator' => true,
                'delay' => 0,
                'pluginOptions' => [
                    'showProgressbar' => false,
                    'placement' => [
                        'from' => 'bottom',
                        'align' => 'right',
                    ]
                ]
            ]);
        } catch (Exception $e) {
        }
    }
}


