<?php
/**
 * Created by PhpStorm.
 * User: SoS
 * Date: 13.06.2019
 * Time: 23:48
 */

namespace app\controllers;

use app\models\Cart;
use app\models\Categories;
use app\models\Keyboards;
use app\models\OrderProducts;
use app\models\Orders;
use app\models\Products;
use app\models\Settings;
use app\models\User;
use TelegramBot\Api\BotApi;
use yii\web\Controller;

class AuxiliaryController extends Controller
{
    public $basePhotoUrl = 'URL';

    const CURRENCY_RU = ' сум';
    const CURRENCY_UZ = ' so\'m';
    const SETTING_DEFAULT_ID = 1;
    const ADMIN_ID = 1;

    //  -------------------  GETTERS ----------------

    /**
     * @return mixed
     */
    public function getLang($chatID)
    {
        $user = $this->getUser($chatID);
        if ($user)
            return $user->language;
        return 'ru';
    }

    /**
     * @return mixed
     */
    public function getBack($chatID)
    {
        $user = $this->getUser($chatID);
        if ($user)
            return $user->back;
        return 'main';
    }

    /**
     * @return User
     */
    public function getNewUser()
    {
        return new User();
    }

    /**
     * @param $chatID
     * @return User
     */
    public function getUser($chatID)
    {
        return $this->getNewUser()->getUserByChatID($chatID);
    }

    /**
     * @return Keyboards
     */
    public function getKeyboards()
    {
        return new Keyboards();
    }

    /** Get Admin info [user id]
     * @return User
     */
    public function getAdmin()
    {
        return User::findOne(self::ADMIN_ID);
    }

    /** Send Category list method
     * @param BotApi $bot
     * @param $chatID
     * @param $lang
     * @param $category
     * @return void
     */
    public function sendCategoriesList($bot, $chatID, $lang, $category)
    {
        $answer = $category->{'description_' . $lang};
        $subCat = Categories::findOne(['parent_id' => $category->id]);

        if (!empty($category->image)) {
            $image = $this->basePhotoUrl . $category->image;
            $description = $category->{'image_description_' . $lang};
            $bot->sendPhoto($chatID, $image, $description);
        }

        if ($subCat) {
            $bot->sendMessage($chatID, $answer, 'HTML', null, null, $this->getKeyboards()->getCategories($lang, $category->id));
        } else {
            $bot->sendMessage($chatID, $answer, 'HTML', null, null, $this->getKeyboards()->getProducts($lang, $category->id));
        }
    }

    /** Find CategoryId | GetLast Category id
     * @param $chatID
     * @return int
     */
    public function getCategoryId($chatID)
    {
        $categoryId = $this->getUser($chatID)->getLastCategoryId();
        $category = Categories::findOne($categoryId);
        if ($category->parent_id)
            return $category->parent_id;
        return $categoryId;
    }

    /**
     * @param $price
     * @param $lang
     * @return string
     */
    public function getCurrency($price, $lang)
    {
        return $lang === 'ru'
            ? 'Цена: ' . $price . self::CURRENCY_RU
            : 'Narxi: ' . $price . self::CURRENCY_UZ;
    }

    /**
     * @param $lang
     * @return string
     */
    public function getChooseCountText($lang)
    {
        return $lang === 'ru'
            ? 'Выберите количество:'
            : 'sonini tanlang:';
    }

    /** User Cart Array
     * @param $chatID
     * @return mixed|\yii\db\ActiveRecord
     */
    public function getCartArray($chatID)
    {
        $carts = Cart::find()
            ->where(['user_id' => $chatID])
            ->asArray()
            ->all();

        $results = array_reduce($carts, function ($carry, $item) {
            if (isset($carry[$item['product_id']])) {
                $carry[$item['product_id']]['count'] += $item['count'];
                $carry[$item['product_id']]['amount'] += $item['amount'];
            } else {
                $carry[$item['product_id']] = $item;
            }
            return $carry;
        }, array());

        return $results;
    }

    /**
     * @return Settings
     */
    public function getDefaultText()
    {
        return Settings::findOne(self::SETTING_DEFAULT_ID);
    }

    //  -------------------  SETTERS ----------------

    /**
     * @param mixed $back
     * @return void
     */
    public function setBack($chatID, $back, $lang = null)
    {
        $user = $this->getUser($chatID);
        $user->back = $back;
        if ($lang)
            $user->language = $lang;
        $user->save(false);
    }

    //  -------------------  FIND ------------------

    /** Find category
     * @param $text
     * @param $lang
     * @param null $parentId
     * @return \yii\db\ActiveRecord
     */
    public function findCategoryByName($text, $lang, $parentId = NULL)
    {
        $categories = Categories::find()
            ->where(['parent_id' => $parentId])
            ->all();
        foreach ($categories as $item) {
            $name = $item->icon . ' ' . $item->{'name_' . $lang};
            if ($name == $text)
                return $item;
        }
        return $categories[0];

    }

    /**
     * @param $getText
     * @param $lang
     * @param $categoryId
     * @return null |\yii\db\ActiveRecord
     */
    public function findProductByName($getText, $lang, $categoryId)
    {
        $products = Products::find()
            ->where(['category_id' => $categoryId])
            ->all();
        foreach ($products as $item) {
            $name = $item->{'name_' . $lang};
            if ($name == $getText) {
                return $item;
                break;
            }
        }
        return null;
    }

    //  -------------------  SAVE ------------------

    /** Save Last category id to User table
     * @param $chatID
     * @param $categoryId
     * @return void
     */
    public function saveLastCategoryId($chatID, $categoryId)
    {
        $user = $this->getUser($chatID);
        if ($user) {
            $user->category_id = $categoryId;
            $user->save(false);
        }
    }

    /** Save product to CART
     * @param $chatID
     * @param $product
     * @return null|object
     */
    public function saveToCart($chatID, $product)
    {
        $_product = $product['product'];
        $_count = $product['count'];
        $cart = new Cart();
        $cart->user_id = $chatID;
        $cart->product_id = $_product->id;
        $cart->count = $_count;
        $cart->price = $_product->price;
        $cart->amount = $_count * $_product->price;
        if ($cart->save(false)) {
            return $cart;
        }
        return null;
    }

    /**
     * @param $chatID
     * @param $location
     * @return void
     */
    public function saveUserLocation($chatID, $location, $address)
    {
        $user = $this->getUser($chatID);
        if($location){
            $user->lat = $location->getLatitude();
            $user->lng = $location->getLongitude();
            $user->address = 'Локация';
        } else {
            $user->lat = '';
            $user->lng = '';
            $user->address = $address;
        }
        $user->save(false);
    }

    /**
     * @param $chatID
     * @param $phone
     * @return void
     */
    public function saveUserPhone($chatID, $phone)
    {
        $user = $this->getUser($chatID);
        $user->phone = $phone->getPhonenumber();
        $user->user_id = $phone->getUserid();
        $user->save(false);
    }

    /**
     * @param $chatID
     * @param $phone
     * @return void
     */
    public function saveUserAddress($chatID, $address)
    {
        $user = $this->getUser($chatID);
        $user->address = $address;
        $user->save(false);
    }

    /** Save Orders
     * @param $chatID
     * @return null
     */
    public function saveOrders($chatID)
    {
        $cart = $this->getCartArray($chatID);
        $user = $this->getUser($chatID);
        $order = new Orders();
        $amount = array_sum(array_map(function($item) {
            return $item['amount'];
        }, $cart));
        $order->user_id = $chatID;
        $order->phone = $user->phone;
        $order->address = $user->address;
        $order->lat = $user->lat;
        $order->lng = $user->lng;
        $order->amount = $amount;
        if($order->save(false)){
            foreach ($cart as $item){
                $product = Products::findOne($item['product_id']);
                if($product)
                    $this->saveOrderProducts($order->id,$item);
            }
            Cart::deleteAll(['user_id' => $chatID]);
            return $order;
        }
        return null;
    }

    /**
     * @param $orderId
     * @param $item
     */
    private function saveOrderProducts($orderId, $item)
    {
        $orderProduct = new OrderProducts();
        $orderProduct->order_id = $orderId;
        $orderProduct->product_id = $item['product_id'];
        $orderProduct->count = $item['count'];
        $orderProduct->price = $item['price'];
        $orderProduct->amount = $item['amount'];
        $orderProduct->save();
    }


    //  -------------------  DELETE ------------------

    /** Delete Products from cart table
     * @param $chatID
     * @param $productId
     * @return void
     */
    public function deleteProductFromDate($chatID, $productId)
    {
        Cart::deleteAll([
            'product_id' => $productId,
            'user_id' => $chatID
        ]);
    }

    /**
     * @param $chatID
     * @return void
     */
    public function deleteAllCart($chatID)
    {
        Cart::deleteAll([
            'user_id' => $chatID
        ]);
    }


}