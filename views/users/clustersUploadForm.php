<?php
/**
 * @var yii\web\View $this
 * @var string $title
 * @var app\models\ClusterUpload $cluster
 */

use kartik\widgets\FileInput;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
?>

<?php
$form = ActiveForm::begin([
    'action' => Url::to(['/users/upload-clusters']),
    'options' => ['enctype' => 'multipart/form-data']
]);
?>

<div class="loader"></div>

<?php

try {
    echo $form->field($cluster, 'clusterFile')->widget(FileInput::class, [
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

<button type="submit" id="upload-clusters-btn" class="btn"><i class="fas fa-upload"></i> Upload</button>

<?php ActiveForm::end(); ?>

<?php
$uploadClusterJs = <<< JS
$('#upload-clusters-btn').click(function(e){
    $('.loader').html(loader);
});
JS;
$this->registerJs($uploadClusterJs, yii\web\View::POS_READY);
