<?php
/**
 * @author Rufusy Idachi <idachirufus@gmail.com>
 */

/**
 * @var yii\web\View $this
 * @var string $title
 * @var app\models\Content $content
 * @var app\models\Characteristic $listOfCharacteristics
 * @var app\models\Course $listOfCourses
 */

use kartik\select2\Select2;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

$this->title = $title;
?>

    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">home</a></li>
                        <li class="breadcrumb-item active">edit match</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Small boxes (Stat box) -->
            <div class="row">
                <div class="col-md-3 col-lg-3"></div>
                <div class="col-md-6 col-lg-6">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><?= $title; ?></h3>
                        </div>
                        <!-- /.card-header -->
                        <?php
                        $form = ActiveForm::begin([
                            'action' => Url::to(['/content/update']),
                        ]);
                        ?>
                        <div class="card-body">
                            <div class="loader"></div>
                            <?php
                            try {
                                echo $form->field($content, 'id')->hiddenInput()->label(false);

                                echo $form->field($content, 'url')
                                    ->textInput([
                                        'class' => 'form-control',
                                        'placeholder' => 'url',
                                    ])
                                    ->label('Url *');

                                echo $form->field($content, 'courseId')->widget(Select2::class, [
                                    'data' => $listOfCourses,
                                    'class' => 'form-control',
                                    'options' => [
                                        'placeholder' => 'Course',
                                    ],
                                    'pluginOptions' => [
                                        'allowClear' => true
                                    ]
                                ])->label('Course *');

                                echo $form->field($content, 'type')->widget(Select2::class, [
                                    'data' => $listOfCharacteristics,
                                    'class' => 'form-control',
                                    'options' => [
                                        'placeholder' => 'Type',
                                    ],
                                    'pluginOptions' => [
                                        'allowClear' => true
                                    ]
                                ])->label('Type');

                            } catch (Exception $e) {
                                echo $e->getMessage();
                            }
                            ?>

                            <label for="Description"></label>
                            <textarea id="summernote" name="description" rows="10">
                                <?= $content->description; ?>
                            </textarea>

                        </div>
                        <div class="card-footer">
                            <button type="submit" id="create-content-btn" class="btn">Save</button>
                        </div>

                        <?php ActiveForm::end(); ?>
                    </div>
                </div>
                <div class="col-md-3 col-lg-3"></div>
            </div>
            <!-- /.row -->
        </div>
    </section>
    <!-- /.content -->

<?php
$newContentJs = <<< JS
// Summernote
$('#summernote').summernote();
JS;
$this->registerJs($newContentJs, yii\web\View::POS_READY);
