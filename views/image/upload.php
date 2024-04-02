<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Загрузка изображений';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-md-6">
            <?php $form = ActiveForm::begin(
                ['options' => [
                    'enctype' => 'multipart/form-data'
                ],
                ]); ?>

            <?= $form->field($model, 'imageFiles[]')->fileInput(['multiple' => true, 'accept' => 'image/png, image/jpeg, image/jpg'])->label('Выберите изображения для загрузки') ?>

            <div class="form-group">
                <?= Html::submitButton('Загрузить', ['class' => 'btn btn-primary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
