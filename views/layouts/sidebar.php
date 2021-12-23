<?php
/**
 * @author Rufusy Idachi <idachirufus@gmail.com>
 */

use yii\bootstrap4\Html;
use yii\helpers\Url;
?>

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?= Url::to(['/']) ?>" class="brand-link">
        <span class="brand-text font-weight-light"><?= Yii::$app->params['sitename']; ?></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="info">
                <a href="#" class="d-block" style="color:#fff;">
                    <?= Yii::$app->user->identity->username; ?>
                </a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->
                <?php if(Yii::$app->user->identity->userGroup->name === 'tutor'):?>
                <li class="nav-item">
                    <a href="<?= Url::to(['/']);?>" class="nav-link">
                        <i class="nav-icon fas fa-home"></i>
                        <p>Home</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>
                            users
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= Url::to(['/users', 'group' => 'tutor']);?>" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Tutors</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= Url::to(['/users', 'group' => 'student']);?>" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Students</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="<?= Url::to(['/courses']);?>" class="nav-link">
                        <i class="nav-icon fas fa-book-open"></i>
                        <p>Courses</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= Url::to(['/content']);?>" class="nav-link">
                        <i class="nav-icon fas fa-book-open"></i>
                        <p>All content</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= Url::to(['/content', 'id' => Yii::$app->user->identity->id]);?>" class="nav-link">
                        <i class="nav-icon fas fa-book-open"></i>
                        <p>My content</p>
                    </a>
                </li>
                <?php else:?>
                <li class="nav-item">
                    <a href="<?= Url::to(['/reading']);?>" class="nav-link">
                        <i class="nav-icon fas fa-book-open"></i>
                        <p>Reading material</p>
                    </a>
                </li>
                <?php endif;?>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>