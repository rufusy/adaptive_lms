<?php
/**
 * @var yii\web\View $this
 * @var string $title
 * @var app\models\UserGroup $listOfRoles[]
 * @var app\models\User $user
 */

use kartik\select2\Select2;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
?>

<?php
$form = ActiveForm::begin([
    'action' => Url::to(['/users/update']),
]);

echo $form->field($user, 'id')->hiddenInput()->label(false)
?>

<div class="loader"></div>

<div class="form-group">
    <?=
    $form->field($user, 'username')
        ->textInput([
            'class' => 'form-control',
            'placeholder' => 'name',
        ])
        ->label('Username *');
    ?>
</div>

<div class="form-group">
    <?php
    try {
        echo $form->field($user, 'userGroupId')->widget(Select2::class, [
            'data' => $listOfRoles,
            'class' => 'form-control',
            'options' => [
                'placeholder' => 'User role',
            ],
            'pluginOptions' => [
                'allowClear' => true
            ]
        ])->label('User role');
    } catch (Exception $e) {
        echo $e->getMessage();
    }
    ?>
</div>

<button type="submit" id="update-user-btn" class="btn">Update</button>
<?php ActiveForm::end(); ?>

<?php
$updateUserJs = <<< JS
$('#update-user-btn').click(function(e){
    $('.loader').html(loader);
});
JS;
$this->registerJs($updateUserJs, yii\web\View::POS_READY);


