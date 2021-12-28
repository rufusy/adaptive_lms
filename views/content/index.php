<?php
/**
 * @author Rufusy Idachi <idachirufus@gmail.com>
 */

/**
 * @var yii\web\View $this
 * @var app\models\Content $model
 * @var yii\data\ActiveDataProvider $contentDataProvider
 * @var app\models\search\ContentSearch $contentSearchModel
 * @var string $title
 * @var app\models\Characteristic $listOfCharacteristics[]
 */

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use yii\web\ServerErrorHttpException;

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
                    <li class="breadcrumb-item active">manage content</li>
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
            <div class="col-12">
                <?php
                $gridColumns = [
                    ['class' => 'kartik\grid\SerialColumn'],
                    [
                        'class' => 'kartik\grid\ExpandRowColumn',
                        'width' => '50px',
                        'value' => function () {
                            return GridView::ROW_COLLAPSED;
                        },
                        'detail' => function ($model) {
                            return Yii::$app->controller->renderPartial('additionalContentDetails', [
                                'id' => $model['id'],
                                'description' => $model['description'],
                            ]);
                        },
                        'headerOptions' => ['class' => 'kartik-sheet-style ficha'],
                        'contentOptions'=>['class'=>'kartik-sheet-style ficha'],
                        'expandOneOnly' => true,
                    ],
                    [
                        'attribute' => 'topic',
                        'label' => 'Topic',
                        'value' => function($model){
                            return $model['topic'];
                        }
                    ],
                    [
                        'attribute' => 'course.code',
                        'label' => 'Course code',
                        'value' => function($model){
                            return $model['course']['code'];
                        }
                    ],
                    [
                        'attribute' => 'course.name',
                        'label' => 'Course name',
                        'value' => function($model){
                            return $model['course']['name'];
                        }
                    ],
                    [
                        'attribute' => 'url',
                        'label' => 'Url',
                        'format' => 'raw',
                        'value' => function($model){
                            $url = $model['url'];
                            return '<a href="' . $url . '">' . $url . '</a>';
                        }
                    ],
                    [
                        'attribute' => 'characteristic.id',
                        'label' => 'Type',
                        'filterType' => GridView::FILTER_SELECT2,
                        'filter' => $listOfCharacteristics,
                        'vAlign' => 'middle',
                        'format' => 'raw',
                        'filterWidgetOptions' => [
                            'options' => [
                                'id' => 'content-grid-types',
                                'placeholder' => '--- all ---'
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'autoclose' => true
                            ]
                        ],
                        'value' => function($model){
                            return $model['characteristic']['description'];
                        }
                    ],
                    [
                        'attribute' => 'creator.username',
                        'label' => 'Created by',
                        'value' => function($model){
                            return $model['creator']['username'];
                        }
                    ],
                    [
                        'attribute' => 'createdAt',
                        'label' => 'Created at',
                        'filterType' => GridView::FILTER_DATE_RANGE,
                        'format' => 'raw',
                        'vAlign' => 'middle',
                        'filterWidgetOptions' => [
                            'presetDropdown' => true,
                            'convertFormat' => true,
                            'includeMonthsFilter' => true,
                            'pluginOptions' => [
                                'locale' => ['format' => 'Y-m-d'],
                                'separator'=>' to '
                            ],
                            'options' => [
                                'id' => 'content-created-at-date'
                            ],
                        ],
                        'value' => function($model){
                            return Yii::$app->formatter->asDatetime($model['createdAt'], 'php:d/m/y  H:i');
                        }
                    ],
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'template' => '{update} {delete}',
                        'contentOptions' => [
                            'style'=>'white-space:nowrap;',
                            'class'=>'kartik-sheet-style kv-align-middle'
                        ],
                        'buttons' => [
                            'update' => function ($url, $model){
                                return Html::a('<i class="fas fa-edit"></i>',
                                    Url::to([
                                        '/content/edit',
                                        'id' => $model['id'],
                                    ]),
                                    [
                                        'title' => 'Update content',
                                        'class' => 'btn btn-sm update-content-btn'
                                    ]
                                );
                            },
                            'delete' => function ($url, $model){
                                return Html::button('<i class="fa fa-trash" aria-hidden="true"></i>', [
                                    'title' => 'Delete content',
                                    'href' => Url::to(['/content/delete']),
                                    'data-id' => $model['id'],
                                    'data-status' => 'delete',
                                    'class' => 'btn btn-sm change-status-btn'
                                ]);
                            }
                        ],
                        'hAlign' => 'center',
                    ]
                ];

                try {
                    echo GridView::widget([
                        'id' => 'content-grid',
                        'dataProvider' => $contentDataProvider,
                        'filterModel' => $contentSearchModel,
                        'columns' => $gridColumns,
                        'headerRowOptions' => ['class' => 'kartik-sheet-style grid-header'],
                        'filterRowOptions' => ['class' => 'kartik-sheet-style grid-header'],
                        'pjax' => true,
                        'toolbar' => [
                            [
                                'content' => Html::a('<i class="fas fa-plus"></i> content',
                                    Url::to(['/content/create',]),
                                    [
                                        'title' => 'Create new content',
                                        'class' => 'btn btn-block btn-sm',
                                    ]
                                ),
                                'options' => ['class' => 'btn-group mr-2']
                            ],
                            '{export}',
                            '{toggleData}',
                        ],
                        'toggleDataContainer' => ['class' => 'btn-group mr-2'],
                        'export' => [
                            'fontAwesome' => false,
                            'label' => 'Export content'
                        ],
                        'panel' => [
                            'heading' => 'Content',
                        ],
                        'persistResize' => false,
                        'toggleDataOptions' => ['minCount' => 20],
                        'itemLabelSingle' => 'content',
                        'itemLabelPlural' => 'content',
                    ]);
                } catch (Exception $ex) {
                    $message = 'Failed to create grid.';
                    if(YII_ENV_DEV){
                        $message = $ex->getMessage() . ' File: ' . $ex->getFile() . ' Line: ' . $ex->getLine();
                    }
                    throw new ServerErrorHttpException($message, 500);
                }
                ?>
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
</section>
<!-- /.content -->

<?php
echo $this->render('../includes/createUpdateResourceModal');
echo $this->render('../includes/appIsLoading');

$contentJs = <<< JS
$('#content-grid-pjax').on('click', '.change-status-btn', function (e){
    changeStatus.call(this, e, 'content');
});
JS;
$this->registerJs($contentJs, yii\web\View::POS_READY);


