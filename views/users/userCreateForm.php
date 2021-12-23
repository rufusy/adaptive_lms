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
    'action' => Url::to(['/users/store']),
]);
?>
<div class="loader"></div>
<?=
$form->field($user, 'username')
    ->textInput([
        'class' => 'form-control',
        'placeholder' => 'Username',
    ])
    ->label('Username *');
?>
<div id="create-league-select2">
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
<button type="submit" id="create-user-btn" class="btn">Save</button>
<?php ActiveForm::end(); ?>

<?php
$createUserJs = <<< JS
$('#create-user-btn').click(function(e){
    $('.loader').html(loader);
});
JS;
$this->registerJs($createUserJs, yii\web\View::POS_READY);

