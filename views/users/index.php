<?php

/**
 * @author Rufusy Idachi <idachirufus@gmail.com>
 */

/**
 * @var yii\web\View $this
 * @var app\models\User $model
 * @var yii\data\ActiveDataProvider $usersDataProvider
 * @var app\models\search\UsersSearch $userSearchModel
 * @var string $title
 * @var string $group
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
                    <li class="breadcrumb-item active">manage users</li>
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
                        'detail' => function ($model) use ($group) {
                            return Yii::$app->controller->renderPartial('studentCharacteristics', [
                                'id' => $model['id'],
                                'group' => $group
                            ]);
                        },
                        'headerOptions' => ['class' => 'kartik-sheet-style ficha'],
                        'contentOptions'=>['class'=>'kartik-sheet-style ficha'],
                        'expandOneOnly' => true,
                    ],
                    [
                        'attribute' => 'username',
                        'label' => 'Username',
                    ],
                    [
                        'attribute' => 'cluster',
                        'label' => 'Cluster',
                        'value' => function($model){
                            if(is_null($model['cluster'])){
                                return '';
                            }
                            return $model['cluster'];
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
                                return Html::button('<i class="fas fa-edit"></i>', [
                                    'title' => 'Update user',
                                    'href' => Url::to(['/users/edit', 'id' => $model['id']]),
                                    'class' => 'btn btn-sm update-user-btn'
                                ]);
                            },
                            'delete' => function ($url, $model){
                                return Html::button('<i class="fa fa-trash" aria-hidden="true"></i>', [
                                    'title' => 'Delete user',
                                    'href' => Url::to(['/users/delete']),
                                    'data-id' => $model['id'],
                                    'data-status' => 'delete',
                                    'class' => 'btn btn-sm change-status-btn'
                                ]);
                            }
                        ],
                        'hAlign' => 'center',
                    ]
                ];

                /**
                 * Add students with an Excel file
                 * Add tutors and admin with a form
                 */
                if($group === 'student'){
                    $content = Html::button('<i class="fas fa-upload"></i> Students', [
                        'title' => 'Upload students',
                        'id' => 'excel-user-btn',
                        'class' => 'btn btn-sm',
                        'href' => Url::to(['/users/create-from-excel', 'type' => 'students'])
                    ]).'&nbsp'.
                       Html::button('<i class="fas fa-upload"></i> Clusters', [
                            'title' => 'Upload students clusters',
                            'id' => 'excel-clusters-btn',
                            'class' => 'btn btn-sm',
                            'href' => Url::to(['/users/create-from-excel', 'type' => 'clusters'])
                        ]);
                }else{
                    $content = Html::button('<i class="fas fa-plus"></i> user', [
                        'title' => 'Create new user',
                        'id' => 'new-user-btn',
                        'class' => 'btn btn-block btn-sm',
                        'href' => Url::to(['/users/create']),
                    ]);
                }

                try {
                    echo GridView::widget([
                        'id' => 'users-grid',
                        'dataProvider' => $usersDataProvider,
                        'filterModel' => $userSearchModel,
                        'columns' => $gridColumns,
                        'headerRowOptions' => ['class' => 'kartik-sheet-style grid-header'],
                        'filterRowOptions' => ['class' => 'kartik-sheet-style grid-header'],
                        'pjax' => true,
                        'toolbar' => [
                            [
                                'content' => $content,
                                'options' => ['class' => 'btn-group mr-2']
                            ],
                            '{export}',
                            '{toggleData}',
                        ],
                        'toggleDataContainer' => ['class' => 'btn-group mr-2'],
                        'export' => [
                            'fontAwesome' => false,
                            'label' => 'Export users'
                        ],
                        'panel' => [
                            'heading' => 'Users',
                        ],
                        'persistResize' => false,
                        'toggleDataOptions' => ['minCount' => 20],
                        'itemLabelSingle' => 'user',
                        'itemLabelPlural' => 'users',
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

$usersJs = <<< JS
$('#users-grid-pjax').on('click', '#new-user-btn', function(e){
    createOrUpdateModal.call(this, e, 'New user');
});
      
$('#users-grid-pjax').on('click', '#excel-user-btn', function (e){
    createOrUpdateModal.call(this, e, 'Upload students');
});

$('#users-grid-pjax').on('click', '#excel-clusters-btn', function (e){
    createOrUpdateModal.call(this, e, 'Upload students clusters');
});

$('#users-grid-pjax').on('click', '.update-user-btn', function(e){
    createOrUpdateModal.call(this, e, 'Update user');
});
        
$('#users-grid-pjax').on('click', '.change-status-btn', function (e){
    changeStatus.call(this, e, 'user');
});
JS;
$this->registerJs($usersJs, yii\web\View::POS_READY);



