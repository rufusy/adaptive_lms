<?php
/**
 * @author Rufusy Idachi <idachirufus@gmail.com>
 */

/* @var $this yii\web\View */
/* @var app\models\LoginForm $loginForm */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
?>
<div class="login-box">
    <div class="login-logo">
        <div class="text-center">
            <img src="<?=Yii::getAlias('@web');?>/img/brain.jpg" class="brand-image img-fluid img-thumbnail ">
        </div>
        <a href="#"><b>Cogni</b>Learn</a>
    </div>
    <div class="card">
        <div class="card-body login-card-body">
            <?php
            $form = ActiveForm::begin([
                'action' => Url::to(['/site/process-login']),
            ]);

            echo $form->field($loginForm, 'username')
                ->textInput(['class' => 'form-control'])
                ->label('Username')
                ->hint('Students to use their registration numbers. Tutors to use their registered emails.');

            echo $form->field($loginForm, 'password')
                ->textInput([
                    'type' => 'password',
                    'class' => 'form-control'
                ])
                ->label('Password')
                ->hint('Type the word password');
            ?>
            <div class="row">
                <div class="col-8">
                    <?= Html::a('Download user manual', ['/site/download-manual'], ['target'=>'_blank']) ?>
                </div>
                <div class="col-4">
                    <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        <!-- /.login-card-body -->
    </div>
</div>
<!-- /.login-box -->
