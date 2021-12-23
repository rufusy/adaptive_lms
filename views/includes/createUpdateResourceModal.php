<?php
use yii\bootstrap4\Modal;

Modal::begin([
    'title' => '',
    'id' => 'create-update-resource-modal',
    'size' => 'modal-md',
    'options' => ['data-backdrop' => "static", 'data-keyboard' => "false"],
]);

Modal::end();