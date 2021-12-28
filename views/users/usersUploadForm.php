<?php
/**
 * @var yii\web\View $this
 * @var string $title
 * @var app\models\UserGroup $listOfRoles[]
 * @var app\models\UsersUpload $user
 */

use kartik\widgets\FileInput;
use kartik\widgets\Select2;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
?>

<?php
$form = ActiveForm::begin([
    'action' => Url::to(['/users/upload']),
    'options' => ['enctype' => 'multipart/form-data']
]);
?>
<div class="loader"></div>
<?php
try {
    echo $form->field($user, 'userGroupId')->widget(Select2::class, [
        'data' => $listOfRoles,
        'hideSearch' => false,
        'class' => 'form-control',
        'options' => [
            'placeholder' => 'User role',
        ],
        'pluginOptions' => [
            'allowClear' => true
        ]
    ])->label('User role *');
} catch (Exception $e) {
    echo $e->getMessage();
}

try {
    echo $form->field($user, 'usersFile')->widget(FileInput::class, [
        'pluginOptions' => [
            'showCaption' => false,
            'showRemove' => false,
            'showUpload' => false,
            'browseClass' => 'btn',
            'browseIcon' => '<i class="fas fa-file"></i> ',
            'browseLabel' =>  'Select file',
            'allowedFileExtensions' => ['xlsx'],
        ]
    ]);
} catch (Exception $e) {
    echo $e->getMessage();
}
?>
    <button type="submit" id="upload-users-btn" class="btn"><i class="fas fa-upload"></i> Upload</button>

<?php ActiveForm::end(); ?>

<?php
$uploadUsersJs = <<< JS
$('#upload-users-btn').click(function(e){
    $('.loader').html(loader);
});
JS;
$this->registerJs($uploadUsersJs, yii\web\View::POS_READY);



