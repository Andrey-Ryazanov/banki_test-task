<?
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\Json;
use app\models\Image;
use yii\web\Response;

class ApiController extends Controller
{

    /**
     * Возвращает JSON-представление всех изображений.
     *
     * @return string JSON-представление всех изображений.
     */
    public function actionIndex()
    {
        $images = Image::find()->all();
        $options = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE;
        $json = Json::encode($images, $options);

        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->response->headers->add('Content-Type', 'application/json; charset=UTF-8');
        return $json;
    }

    /**
     * Возвращает JSON-представление изображения с указанным ID.
     *
     * @param int $id ID изображения для получения.
     * @return string JSON-представление изображения.
     */
    public function actionView($id)
    {
        $image = Image::findOne($id);
        $options = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE;
        if ($image !== null) {
            $json = Json::encode($image, $options);
        } 
        else {
            $error = ['error' => 'Image not found'];
            $json = Json::encode($error, $options);
        }
        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->response->headers->add('Content-Type', 'application/json; charset=UTF-8');
        return $json;
    }
}
