<?php

namespace app\components\managers;

use Yii;
use app\models\CategoryMap;

class Manager {

    public function updateProductCategories($existingValues, $postValues, $productId) {
        $map = new CategoryMap();
        $deleteList = [];
        $newIndexList = [];
        $postNewMap = [];

        $deleteList = self::getDeleteList($existingValues, $postValues);
        $newIndexList = self::getNewIndexList($existingValues, $postValues);

        $dbTransaction = Yii::$app->db->beginTransaction();
        try {
            if (!empty($deleteList)) {
                Yii::$app->db
                        ->createCommand()
                        ->delete('CategoryMap', ['product_id' => $productId, 'category_id' => $deleteList])
                        ->execute();
            }

            foreach ($newIndexList as $value) {
                $postNewMap['CategoryMap']['product_id'] = $productId;
                $postNewMap['CategoryMap']['category_id'] = $value;
                $postNewMap['_csrf'] = Yii::$app->request->csrfToken;
                if ($map->load($postNewMap)) {
                    $map->save();
                    $map = new CategoryMap();
                }
            }
            $dbTransaction->commit();
        } catch (Exception $ex) {
            $dbTransaction->rollBack();
        }
    }

    public static function getDeleteList($existingValues, $postValues) {
        $deleteList = [];
        // Rows that we no longer need
        foreach ($existingValues as $value) {
            if (!in_array($value, $postValues)) {
                $deleteList[] = $value;
            }
        }

        return $deleteList;
    }

    public static function getNewIndexList($existingValues, $postValues) {
        $newIndexList = [];

        //Rows that we will need to create
        foreach ($postValues as $value) {
            if (!in_array($value, $existingValues)) {
                $newIndexList[] = $value;
            }
        }

        return $newIndexList;
    }

}
