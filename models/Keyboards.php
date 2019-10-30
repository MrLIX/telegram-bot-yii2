<?php
/**
 * Created by PhpStorm.
 * User: SoS
 * Date: 06.06.2019
 * Time: 17:22
 */

namespace app\models;

use TelegramBot\Api\Types\ReplyKeyboardMarkup;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class Keyboards
{

    /** All Reply Keyboards array
     * @param $message
     * @return array
     */
    public function AllKeyboards($message)
    {
        $keyboard = [
            [
                "text" => "🇷🇺 Русский",
                "method" => "chooseLangRU"
            ],
            [
                "text" => "🇺🇿 O'zbekcha",
                "method" => "chooseLangUZ"
            ],
        ];

        $categoriesWithoutParentRu = $this->getAllCategoriesKeyboard('ru', false);
        $categoriesWithoutParentUz = $this->getAllCategoriesKeyboard('uz', false);

        $categoriesWithParentRu = $this->getAllCategoriesKeyboard('ru', true);
        $categoriesWithParentUz = $this->getAllCategoriesKeyboard('uz', true);

        $productsRu = $this->getProductsKeyboard('ru');
        $productsUz = $this->getProductsKeyboard('uz');


        array_push(...$categoriesWithoutParentRu,
            ...$categoriesWithoutParentUz,
            ...$categoriesWithParentRu,
            ...$categoriesWithParentUz,
            ...$productsRu,
            ...$productsUz

        );
        return $keyboard;
    }

    /** Find keyboard
     * @param $message
     * @return bool
     */
    public function findKeyboard($message)
    {
        $text = $message->getText();
        $all = $this->AllKeyboards($message);
        foreach ($all as $item) {
            if ($item["text"] == $text)
                return $item["method"];
        }
        return false;
    }

    /** Back keyboards
     * @param string $lang
     * @return array
     */
    private function backKeyboard($lang = 'ru')
    {
        if ($lang == 'ru') {
            return [
                ["text" => "◀ Назад"],
            ];
        } else {
            return [
                ["text" => "◀ Ortga"],
            ];
        }

    }

    /** Inline Back and Order Keyboard
     * @param $lang
     * @return array
     */
    private function getInlineBackKeyboard($lang)
    {
        if ($lang == 'ru') {
            return [
                ['callback_data' => 'backCallback', 'text' => '◀ Назад'],
                ['callback_data' => 'cart', 'text' => '📥 Корзина'],
            ];
        } else {
            return [
                ['callback_data' => 'backCallback', 'text' => '◀ Ortga'],
                ['callback_data' => 'cart', 'text' => '📥 Savat'],
            ];
        }
    }

    /**
     * @param $lang
     * @return array
     */
    private function getInlineOrderAndBackKeyboard($lang)
    {
        if ($lang == 'ru') {
            return [
                ['callback_data' => 'backCallback', 'text' => '◀ Назад'],
                ['callback_data' => 'orderCallback', 'text' => '🚖 Оформить заказ'],
            ];
        } else {
            return [
                ['callback_data' => 'backCallback', 'text' => '◀ Ortga'],
                ['callback_data' => 'orderCallback', 'text' => '🚖 Buyurtma berish'],
            ];
        }
    }

    /**
     * @param $lang
     * @return array
     */
    private function getClearInlineKeyboard($lang)
    {
        if ($lang == 'ru') {
            return [
                ['callback_data' => 'clearCart', 'text' => '🔄 Очистить корзину'],
            ];
        } else {
            return [
                ['callback_data' => 'clearCart', 'text' => '🔄 Savatchani tozalash'],
            ];
        }
    }

    /** Back keyboards
     * @param string $lang
     * @return array
     */
    public function orderKeyboard($lang = 'ru')
    {
        if ($lang == 'ru') {
            return [
                ["text" => "📥 Корзина"],
                ["text" => "🚖 Оформить заказ"],
            ];
        } else {
            return [
                ["text" => "📥 Savat"],
                ["text" => "🚖 Buyurtma berish"],
            ];
        }

    }

    /**
     *  /start
     * @return ReplyKeyboardMarkup
     */
    public function getLanguages()
    {
        return new ReplyKeyboardMarkup([
            [
                ["text" => "🇷🇺 Русский"],
                ["text" => "🇺🇿 O'zbekcha"]
            ]
        ], true, true);
    }

    /** "🇷🇺 Русский"
     * @return ReplyKeyboardMarkup
     */
    public function getChooseRu()
    {
        return new ReplyKeyboardMarkup([
            [
                ["text" => "🍲 Заказать"],
                ["text" => "📥 Корзина"],
            ],
            [
                ["text" => "✍ Оставить отзыв"]
            ],
            [
                ["text" => "◀ Назад"],
            ]
        ], true, true);
    }

    /** "🇺🇿 O'zbekcha"
     * @return ReplyKeyboardMarkup
     */
    public function getChooseUz()
    {

        return new ReplyKeyboardMarkup([
            [
                ["text" => "🍲 Buyurtma berish"],
                ["text" => "📥 Savat"],
            ],
            [
                ["text" => "✍ Izoh qoldirish"]
            ],
            [
                ["text" => "◀ Ortga"],
            ]
        ], true, true);
    }

    /** "✍ Оставить отзыв" || "✍ Izoh qoldirish"
     * @param $lang
     * @return ReplyKeyboardMarkup
     */
    public function feedback($lang)
    {
        if ($lang == 'ru') {
            return new ReplyKeyboardMarkup([
                [
                    ["text" => "Все понравилось, ⭐️⭐️⭐️⭐️⭐️"],
                ],
                [
                    ["text" => "Нормально, ⭐️⭐️⭐️⭐️"]
                ],
                [
                    ["text" => "Удовлетворительно, ⭐️⭐️⭐️"]
                ],
                [
                    [
                        "text" => "Не понравилос, ⭐️⭐️"]
                ],
                [
                    ["text" => "Хочу пожаловаться, ⭐️"]
                ],
                [
                    ["text" => "◀ Назад"]
                ]
            ], true, true);
        } else {
            return new ReplyKeyboardMarkup([
                [
                    ["text" => "Juda xam yoqdi, ⭐️⭐️⭐️⭐️⭐️"],
                ],
                [
                    ["text" => "Yaxshi, ⭐️⭐️⭐️⭐️"]
                ],
                [
                    ["text" => "O'rtacha, ⭐️⭐️⭐️"]
                ],
                [
                    ["text" => "Yoqmadi, ⭐️⭐️"]
                ],
                [
                    ["text" => "Juda yoqmadi, ⭐️"]
                ],
                [
                    ["text" => "◀ Ortga"]
                ]
            ], true, true);
        }
    }

    /** "🍲 Заказать"
     * @param string $lang
     * @return ReplyKeyboardMarkup
     */
    public function getCategories($lang = 'ru', $parentId = NULL)
    {
        $keyboard = $this->getCategoriesArray($lang, $parentId);
        $keyboard[] = $this->orderKeyboard($lang);
        $keyboard[] = $this->backKeyboard($lang);
        return new ReplyKeyboardMarkup($keyboard, true, true);

    }

    /** Categories Array
     * @param $lang
     * @param null $parentId
     * @return array
     */
    private function getCategoriesArray($lang, $parentId = NULL)
    {
        $array = [];
        $catArray = [];

        $categories = Categories::find()
            ->where(['parent_id' => $parentId, 'status' => BaseModel::STATUS_ACTIVE])
            ->orderBy(['order' => SORT_ASC])
            ->all();

        foreach ($categories as $key => $item) {
            $name = $item->icon . ' ' . $item->{'name_' . $lang};
            $array[] = [
                "text" => $name
            ];
        }
        for ($i = 0; $i < count($array); $i = $i + 2) {
            if (count($array) !== ($i + 1)) {
                $catArray[] = [
                    $array[$i],
                    $array[$i + 1]
                ];
            } else {
                $catArray[] = [
                    $array[$i]
                ];
            }
        }
        return $catArray;
    }

    /** All Categories for All Keyboard
     * @param string $lang
     * @return array
     */
    private function getAllCategoriesKeyboard($lang = 'ru', $parent)
    {
        $array = [];

        $categories = Categories::find()
            ->where(['parent_id' => NULL])
            ->andWhere(['status' => BaseModel::STATUS_ACTIVE])
            ->all();

        if ($parent) {
            $categories = Categories::find()
                ->where(['>', 'parent_id', 0])
                ->andWhere(['status' => BaseModel::STATUS_ACTIVE])
                ->all();
        }
        foreach ($categories as $key => $item) {
            $name = $item->icon . ' ' . $item->{'name_' . $lang};
            $array[] = [
                "text" => $name,
                "method" => $parent == true ? 'parentCategory' : 'oneCategory'
            ];
        }
        return $array;

    }

    /** Choose category or sub category
     * @param string $lang
     * @param $categoryId
     * @return ReplyKeyboardMarkup
     */
    public function getProducts($lang = 'ru', $categoryId)
    {
        $keyboard = $this->getProductsArray($lang, $categoryId);
        $keyboard[] = $this->orderKeyboard($lang);
        $keyboard[] = $this->backKeyboard($lang);
        return new ReplyKeyboardMarkup($keyboard, true, true);

    }

    /** Products keyboard
     * @param $lang
     * @param $categoryId
     * @return array
     */
    private function getProductsArray($lang, $categoryId)
    {
        $array = [];
        $prodArray = [];

        $products = Products::find()
            ->where(['category_id' => $categoryId, 'status' => BaseModel::STATUS_ACTIVE])
            ->orderBy(['order' => SORT_ASC])
            ->all();

        foreach ($products as $key => $item) {
            $array[] = [
                "text" => $item->{'name_' . $lang}
            ];
        }
        for ($i = 0; $i < count($array); $i = $i + 2) {
            if (count($array) !== ($i + 1)) {
                $prodArray[] = [
                    $array[$i],
                    $array[$i + 1]
                ];
            } else {
                $prodArray[] = [
                    $array[$i]
                ];
            }
        }
        return $prodArray;

    }

    /** Get All products to search keyboard
     * @param $lang
     * @return array
     */
    public function getProductsKeyboard($lang)
    {
        $array = [];
        $products = Products::find()
            ->where(['status' => BaseModel::STATUS_ACTIVE])
            ->orderBy(['order' => SORT_ASC])
            ->all();

        foreach ($products as $key => $item) {
            $array[] = [
                "text" => $item->{'name_' . $lang},
                "method" => 'products'
            ];
        }
        return $array;

    }

    /**
     * @param $product
     * @param $lang
     */
    public function oneProductKeyboard($product, $lang)
    {
        $keyboard = [
            [
                ['callback_data' => '1_' . $product->id, 'text' => '1'],
                ['callback_data' => '2_' . $product->id, 'text' => '2'],
                ['callback_data' => '3_' . $product->id, 'text' => '3']
            ],
            [
                ['callback_data' => '4_' . $product->id, 'text' => '4'],
                ['callback_data' => '5_' . $product->id, 'text' => '5'],
                ['callback_data' => '6_' . $product->id, 'text' => '6']
            ],
            [
                ['callback_data' => '7_' . $product->id, 'text' => '7'],
                ['callback_data' => '8_' . $product->id, 'text' => '8'],
                ['callback_data' => '9_' . $product->id, 'text' => '9']
            ]
        ];
        $keyboard[] = $this->getInlineBackKeyboard($lang);

        return new InlineKeyboardMarkup($keyboard);
    }

    /** All inline Keyboard
     * @return array
     */
    public function allInlineKeyboards()
    {
        $keyboard = [
            [
                "text" => "backCallback",
                "method" => "backButton"
            ],
            [
                "text" => "cart",
                "method" => "cart"
            ],
            [
                "text" => "orderCallback",
                "method" => "orders"
            ],
            [
                "text" => "clearCart",
                "method" => "clearCart"
            ]
        ];
//        array_push($keyboard);

        return $keyboard;
    }

    /** Find method
     * @param $data
     * @return bool|mixed
     */
    public function findInlineKeyboard($data)
    {
        $all = $this->allInlineKeyboards();
        foreach ($all as $item) {
            if ($item["text"] == $data)
                return $item["method"];
        }
        return false;
    }

    /**
     * @param $data
     * @return array|bool
     */
    public function findProductFromInlineKeyboard($data)
    {
        $buttons = preg_split("/_/", $data);
        if (count($buttons) == 2) {
            $product = Products::findOne($buttons[1]);
            if ($product) {
                return [
                    'count' => $buttons[0],
                    'product' => $product
                ];
            }
        } elseif (count($buttons) == 3) {
            $product = Products::findOne($buttons[1]);
            if ($product) {
                return [
                    'product' => $product,
                    'deletedId' => $product->id
                ];
            }
        }
        return false;
    }

    /**
     * @param $allCart
     * @param $lang
     * @return InlineKeyboardMarkup
     */
    public function getCartKeyboard($allCart, $lang)
    {
        $keyboard = [];
        $keyboard[] = $this->getClearInlineKeyboard($lang);
        foreach ($allCart as $item) {
            $product = Products::findOne($item['product_id']);
            if ($product)
                $keyboard[] = [
                    ['callback_data' => '0_' . $product->id . '_0', 'text' => '❌ ' . $product->{'name_' . $lang}],
                ];
        }

        $keyboard[] = $this->getInlineOrderAndBackKeyboard($lang);

        return new InlineKeyboardMarkup($keyboard);
    }

    /**
     * @param $lang
     * @return ReplyKeyboardMarkup
     */
    public function sendLocation($lang){
       $keyboard = [];
        if($lang == 'ru'){
            $keyboard[] = [
                [
                    "text" => "📍 Отправить локацию",
                    "request_location" => true
                ],
            ];
        } else {
            $keyboard[] = [
                [
                    "text" => "📍 Aniq manzilni jo'natish",
                    "request_location" => true
                ],
            ];
        }
        $keyboard[] = $this->backKeyboard($lang);
        return new ReplyKeyboardMarkup($keyboard,true,true);
    }

    /**
     * @param $lang
     * @return ReplyKeyboardMarkup
     */
    public function sendPhoneNumber($lang){
        $keyboard = [];
        if($lang == 'ru'){
            $keyboard[] = [
                [
                    "text" => "📲 Отправить телефон номер",
                    "request_contact" => true
                ],
            ];
        } else {
            $keyboard[] = [
                [
                    "text" => "📲 Telefon raqamni jo'natish",
                    "request_contact" => true
                ],
            ];
        }
        $keyboard[] = $this->backKeyboard($lang);

        return new ReplyKeyboardMarkup($keyboard,true,true);
    }

    /**
     * @param $lang
     * @return ReplyKeyboardMarkup
     */
    public function confirmOrder($lang)
    {
        $keyboard = [];
        if($lang == 'ru'){
            $keyboard[] = [
                [
                    "text" => "✅ Подтвердить заказ",
                ],
            ];
        } else {
            $keyboard[] = [
                [
                    "text" => "✅ Buyurtmani tasdiqlash",
                ],
            ];
        }
        $keyboard[] = $this->backKeyboard($lang);
        return new ReplyKeyboardMarkup($keyboard,true,true);
    }

}