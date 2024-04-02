<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\helpers\Inflector;
use yii\helpers\ArrayHelper;

class ImageForm extends Model
{
    public $id;
    public $filename;
    public $created_at;
    public $imageFiles;

    public $maxFiles = 5;
    public $extensions = 'png, jpg, jpeg';

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['imageFiles'], 'file', 
                'maxFiles' => $this->maxFiles, 
                'extensions' => $this->extensions, 
                'wrongExtension' => 'Разрешено загружать файлы только с расширениями: ' . $this->extensions . '.',
                'tooMany' => 'Вы не можете загружать более ' . $this->maxFiles . ' файлов одновременно.', 
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'filename' => 'Имя файла',
            'created_at' => 'Дата загрузки',
        ];
    }

    /**
     * Загружает изображения и сохраняет информацию о них в базе данных.
     * @return boolean успешно ли загружены все изображения и сохранена информация в базе данных
     */
    public function uploadImage()
    {
        $uploadsPath = Yii::getAlias('@webroot') . '/uploads/images/';
        
        if ($this->validate()) {
            foreach ($this->imageFiles as $file) {
                $baseName = $file->baseName;
                $transliteratedName = Inflector::transliterate($baseName);
                $extension = $file->extension;
                $fileName = strtolower($transliteratedName) . '.' . $extension;
                $filePath = $uploadsPath . $fileName;
        
                // Проверяем уникальность имени файла
                while (file_exists($filePath)) {
                    $randomString = Yii::$app->security->generateRandomString(8);
                    $fileName = strtolower($randomString) . '.' . $extension;
                    $filePath = $uploadsPath . $fileName;
                }
        
                if ($file->saveAs($filePath)) {
                    // Сохраняем информацию в базу данных
                    $image = new Image();
                    $image->filename = basename($filePath);
                    $image->created_at = Yii::$app->formatter->asDatetime(time()); // Форматируем текущее время
                    if (!$image->save()) {
                        Yii::error("Ошибка сохранения информации о файле в базе данных: " . ArrayHelper::getValue($image->errors, 'filename.0'));
                        return false;
                    }
                } 
                else {
                    Yii::error("Ошибка сохранения файла: " . $file->error);
                    return false;
                }
            }
            return true;
        } 
        else {
            Yii::error("Ошибка валидации формы: " . json_encode($this->errors));
            return false;
        }
    }  

    /**
     * Удаляет изображение из базы данных и файловой системы.
     * @param int $id Идентификатор изображения для удаления
     * @return bool Успешно ли прошло удаление
     */
    public static function deleteImage($id)
    {
        $image = Image::findOne($id);
        if ($image !== null) {
            $filePath = Yii::getAlias('@webroot/uploads/images/') . $image->filename;
            if (file_exists($filePath)) {
                // Удаляем файл из директории
                unlink($filePath);
            }

            // Удаляем запись из базы данных
            return $image->delete();
        }
        return false;
    }
}
