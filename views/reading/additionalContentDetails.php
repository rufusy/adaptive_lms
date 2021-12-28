<?php
/**
 * @author Rufusy Idachi <idachirufus@gmail.com>
 */

/**
 * @var string $id
 * @var string $description
 */

use yii\helpers\Url;
?>

<!-- Main content -->
<section class="content">
    <div class="row">
        <!-- /.col-->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-comment"></i> Description
                    </h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <?= $description; ?>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file"></i> Files
                    </h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <p class="lead">Click on a file to download</p>
                    <?php
                    $path = Yii::getAlias('@app') . '/uploads/content/' . $id . '/';
                    if(is_dir($path)) {
                        $fileNames = array_diff(scandir($path), ['.', '..']);
                    }
                    if(!empty($fileNames)):
                        foreach ($fileNames as $fileName):
                            ?>
                            <a href="<?= Url::to(['/content/download-file', 'id' => $id, 'name' => $fileName]);?>">
                                <?= $fileName; ?>
                            </a>
                            <br>
                        <?php
                        endforeach;
                    endif;
                    ?>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- ./col -->
    </div>
    <!-- ./row -->
</section>
<!-- /.content -->

