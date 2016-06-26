<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use app\models\Employee;
use app\rbac\ProfileRule;

class EmployeeController extends Controller {

    public function actionLoadEmployees() {
        
        $employeeData = [
            [
                'name' => 'Andrei',
                'email' => 'a.orehhov@bo.com',
                'position' => 'owner',
                'username' => 'devrais',
                'password' => '123456',
            ],
            [
                'name' => 'Anton',
                'email' => 'a.voronov@bo.com',
                'position' => 'manager',
                'username' => 'nepox',
                'password' => '123456',
            ],
            [
                'name' => 'Kirill',
                'email' => 'k.voktorov@bo.com',
                'position' => 'worker',
                'username' => 'kirja',
                'password' => '123456',
            ],
            [
                'name' => 'Dima',
                'email' => 'd.vinokurov@bo.com',
                'position' => 'worker',
                'username' => 'rider',
                'password' => '123456',
            ]
        ];
        
         foreach ($employeeData as $data) {
            $employee = new Employee($data);
           // $employee->hashPassword = true;
            $employee->save();
        }
        
    }
    
     public function actionPermissions()
    {
        $auth = Yii::$app->authManager;

        $updateProduct = $auth->createPermission('updateProduct');
        $updateProduct->description = 'Update a product';
        $auth->add($updateProduct);

        $createProduct = $auth->createPermission('createProduct');
        $createProduct->description = 'Create a product';
        $auth->add($createProduct);
        
        $deleteProduct = $auth->createPermission('deleteProduct');
        $deleteProduct->description = 'Delete a product';
        $auth->add($deleteProduct);
        
        $updateCategory = $auth->createPermission('updateCategory');
        $updateCategory ->description = 'Update a category';
        $auth->add($updateCategory);

        $createCategory = $auth->createPermission('createCategory');
        $createCategory->description = 'Create a category';
        $auth->add($createCategory);
        
        $deleteCategory = $auth->createPermission('deleteCategory');
        $deleteCategory->description = 'Delete a category';
        $auth->add($deleteCategory);
        
        $updateEmployee = $auth->createPermission('updateEmployee');
        $updateEmployee ->description = 'Update employee info';
        $auth->add($updateEmployee);

        $createEmployee = $auth->createPermission('createEmployee');
        $createEmployee->description = 'Hire new employee';
        $auth->add($createEmployee);
        
        $deleteEmployee = $auth->createPermission('deleteEmployee');
        $deleteEmployee->description = 'Delete employee';
        $auth->add($deleteEmployee);
    }

    public function actionRoles()
    {
        $auth = Yii::$app->authManager;

        $createProduct = $auth->getPermission('createProduct');
        $updateProduct = $auth->getPermission('updateProduct');
        $deleteProduct = $auth->getPermission(('deleteProduct'));
        $createCategory = $auth->getPermission('createCategory');
        $updateCategory = $auth->getPermission('updateCategory');
        $deleteCategory = $auth->getPermission(('deleteCategory'));
        $createEmployee = $auth->getPermission('createEmployee');
        $updateEmployee = $auth->getPermission('updateEmployee');
        $deleteEmployee = $auth->getPermission(('deleteEmployee'));

        $owner = $auth->createRole('owner');
        $auth->add($owner);
        $auth->addChild($owner, $createProduct);
        $auth->addChild($owner, $updateProduct);
        $auth->addChild($owner, $deleteProduct);
        $auth->addChild($owner, $createCategory);
        $auth->addChild($owner, $updateCategory);
        $auth->addChild($owner, $deleteCategory);
        $auth->addChild($owner, $createEmployee);
        $auth->addChild($owner, $updateEmployee);
        $auth->addChild($owner, $deleteEmployee);

        $manager = $auth->createRole('manager');
        $auth->add($manager);
        $auth->addChild($manager, $createProduct);
        $auth->addChild($manager, $updateProduct);
        $auth->addChild($manager, $deleteProduct);
        $auth->addChild($manager, $createCategory);
        $auth->addChild($manager, $updateCategory);
        
        $worker = $auth->createRole('worker');
        $auth->add($worker);
        $auth->addChild($worker, $createProduct);
        $auth->addChild($worker, $updateProduct);
        $auth->addChild($worker, $deleteProduct);

    }

}
