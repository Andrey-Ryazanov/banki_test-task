<?php
    use yii\helpers\Html;
    use yii\grid\GridView;
    use yii\bootstrap5\Modal;

    /* @var $this yii\web\View */
    /* @var $dataProvider yii\data\ActiveDataProvider */

    $this->title = 'Изображения';

    // Регистрируем скрипт JavaScript
    $this->registerJsFile('@web/js/image-preview.js', ['depends' => [\yii\web\JqueryAsset::class]]);
?>

<div class="image-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Загрузить изображения', ['upload'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'filename',
                'label' => 'Превью',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::img(Yii::getAlias('@web/uploads/images/') . $model->filename, ['class' => 'thumbnail-image', 'style' => 'max-width:100px;']);
                },
                'enableSorting' => false, // Отключаем сортировку для этого столбца
            ],
            'filename',
            'created_at',
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => 'Действия',
                'template' => '{downloadImage} {delete}', // Добавляем кнопки скачивания и удаления
                'buttons' => [
                    'downloadImage' => function ($url, $model, $key) {
                        return Html::a('<span><i class="fas fa-download"></i></span>', 
                        ['download-image', 'filename' => $model->filename], 
                        [
                            'title' => Yii::t('yii', 'Скачать изображение'),
                            'class' => 'btn btn-primary',
                        ]);
                    },
                    'delete' => function ($url, $model, $key) {
                        return Html::a('<span><i class="fas fa-trash"></i></span>', 
                        ['delete', 'id' => $model->id], 
                        [
                            'title' => Yii::t('yii', 'Удалить изображение'),
                            'class' => 'btn btn-danger',
                            'data-confirm' => 'Вы уверены, что хотите удалить это изображение?',
                            'data-method' => 'post',
                        ]);
                    },
                ],
            ],
        ],
        'pager' => [
            'class' => yii\bootstrap5\LinkPager::class,
            'options' => [
                'class' => 'pagination justify-content-center',
            ],
        ],
        'summary' => 'Показано {begin}-{end} из {totalCount} элементов',
    ]); ?>

</div>

<!-- Модальное окно для отображения полного изображения -->
<?php
Modal::begin([
    'id' => 'imageModal',
    'title' => '<h4 class="modal-title">Оригинальное изображение</h4>',
]);

echo '<div class="modal-body text-center"><a id="imageLink" href=""><img id="fullImage" class="img-responsive" src="" alt="Оригинальное изображение" style="max-width:100%;"></a></div>'; // Добавляем ссылку вокруг изображения

Modal::end();

?>
