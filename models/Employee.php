<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "Employee".
 *
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $username
 * @property string $password
 * @property string $position
 */
class Employee extends ActiveRecord implements IdentityInterface
{
    // public $hashPassword  = false;
     public $authKey;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Employee';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'email', 'username', 'password', 'position'], 'required'],
            [['name', 'username', 'password'], 'string', 'max' => 100, 'min'=> 4],
            [['email'],'email'],
            [['position'],'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'email' => 'Email',
            'username' => 'Username',
            'password' => 'Password',
            'position' => 'Position',
        ];
    }
    
    public static function findIdentity($id) {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null) {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    public function getId() {
        return $this->id;
    }

    public static function findByUsername($username) {
        return static::findOne(['username' => $username]);
    }

    public function validatePassword($password) {
        return Yii::$app->security->validatePassword($password, $this->password);
    }
    
    public function getAuthKey()
    {
        return $this->authKey;
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
                $this->password = Yii::$app->security->generatePasswordHash($this->password, 10);
            return true;
        } else {
            return false;
        }
    }
    
    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            $auth = Yii::$app->authManager;
            if ($this->position == "owner") {
                $role = $auth->getRole('owner');
            } elseif ($this->position == "manager") {
                $role = $auth->getRole('manager');
            } else {
                $role = $auth->getRole('worker');
            }
            $auth->assign($role, $this->id);
        }

        parent::afterSave($insert, $changedAttributes);
    }

}
