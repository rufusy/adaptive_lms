<?php
/**
 * @author Rufusy Idachi <idachirufus@gmail.com>
 */

/**
 * @var string $id
 *  @var string $group
 */

use app\models\StudentCharacteristic;

$characteristics = StudentCharacteristic::find()->alias('sc')
    ->select(['sc.value', 'sc.characteristicId'])
    ->joinWith(['characteristic ch'])
    ->where(['sc.studentId' => $id])
    ->orderBy(['ch.id' => SORT_ASC])
    ->asArray()->all();
?>

<!-- Main content -->
<section class="content">
    <div class="row">
        <!-- /.col-->
        <?php if($group === 'student'):?>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Characteristics</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body p-0">
                    <table class="table table-sm">
                        <thead>
                        <tr>
                            <th style="width: 10px">#</th>
                            <th>Name</th>
                            <th>Value</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                            $count = 1;
                            foreach ($characteristics as $characteristic):
                        ?>
                        <tr>
                            <td><?= $count; ?></td>
                            <td><?= $characteristic['characteristic']['name'];?></td>
                            <td><?= $characteristic['value'];?></td>
                        </tr>
                        <?php $count++; endforeach;?>
                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
            <!-- /.card -->
        </div>
        <?php else:?>
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-exclamation-triangle"></i>
                        Alert
                    </h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="alert alert-info alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h5><i class="icon fas fa-info"></i></h5>
                        Tutors have no learning characteristics
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
        <?php endif;?>
    </div>
    <!-- ./row -->
</section>
<!-- /.content -->
