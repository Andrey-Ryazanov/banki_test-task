<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;

use app\models\ImageForm;
use app\models\Image;

use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

use ZipArchive;


class ImageController extends Controller
{
    /**
     * Действие для отображения формы загрузки изображений.
     * @return mixed Результат выполнения действия
     */
    public function actionUpload()
    {
        $model = new ImageForm();

        if (Yii::$app->request->isPost) {
            $model->imageFiles = UploadedFile::getInstances($model, 'imageFiles');
            // Проверяем, были ли загружены файлы
            if (!empty($model->imageFiles)) {
                if ($model->uploadImage()) {
                    Yii::$app->session->setFlash('success', 'Изображения успешно загружены.');
                    return $this->redirect(['index']);
                }
            } 
            else {
                Yii::$app->session->setFlash('error', 'Файлы для загрузки не были выбраны.');
            }
        }

        return $this->render('upload', ['model' => $model]);
    }

    /**
     * Действие для отображения списка загруженных изображений.
     * @return string Результат выполнения действия
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Image::find()->orderBy(['created_at' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Действие для скачивания архива с загруженным изображением.
     * @param string $filename Имя файла для скачивания
     * @return mixed Результат выполнения действия
     */
    public function actionDownloadImage($filename)
    {
        $imagePath = Yii::getAlias('@webroot/uploads/images/') . $filename;
        if (file_exists($imagePath)) {
            $zip = new ZipArchive();
            $zipName = 'image_' . $filename . '.zip';
            if ($zip->open($zipName, ZipArchive::CREATE) === true) {
                $zip->addFile($imagePath, $filename);
                $zip->close();
                Yii::$app->response->sendFile($zipName)->send();
                unlink($zipName); // Удаляем временный zip-файл после отправки
            } 
            else {
                Yii::$app->session->setFlash('error', 'Не удалось создать архив.');
                return $this->redirect(['index']);
            }
        } 
        else {
            Yii::$app->session->setFlash('error', 'Изображение не найдено.');
            return $this->redirect(['index']);
        }
    }

    /**
     * Отображает оригинальное изображение.
     * @param string $filename имя файла изображения
     * @return mixed возвращает файл изображения или вызывает исключение, если файл не найден
     * @throws \yii\web\NotFoundHttpException если файл изображения не найден
     */
    public function actionViewImage($filename)
    {
        $imagePath = Yii::getAlias('@webroot') . '/uploads/images/' . $filename;
        if (file_exists($imagePath)) {
            return Yii::$app->response->sendFile($imagePath, $filename);
        } 
        else {
            throw new NotFoundHttpException('Изображение не найдено.');
        }
    }

    public function actionDelete($id)
    {
        if (ImageForm::deleteImage($id)) {
            Yii::$app->session->setFlash('success', 'Изображение успешно удалено.');
        } else {
            Yii::$app->session->setFlash('error', 'Изображение не найдено.');
        }

        return $this->redirect(['index']);
    }

}
