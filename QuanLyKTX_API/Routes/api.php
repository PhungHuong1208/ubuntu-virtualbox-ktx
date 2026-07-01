<?php
// Định nghĩa các RESTful API Routes thực sự cho hệ thống
// Trả về mảng mapping Route -> Controller@Method

return [
    'GET' => [
        // User API (RESTful)
        'api/user/profile' => 'UserController@getProfile',
        'api/user/room' => 'UserController@getRoom',
        'api/user/contracts' => 'UserController@getContracts',
        'api/user/incidents' => 'UserController@getIncidents',

        // Quản lý Phòng
        'api/rooms' => 'RoomController@index',
        'api/rooms/{id}' => 'RoomController@edit',
        'api/rooms/{id}/students' => 'RoomController@danhsach',

        // Quản lý Sinh Viên
        'api/students' => 'StudentController@index',
        'api/students/{id}' => 'StudentController@edit',

        // Quản lý Hợp Đồng
        'api/contracts' => 'ContractController@index',
        'api/contracts/{id}' => 'ContractController@edit',

        // Quản lý Thanh Toán
        'api/payments' => 'PaymentController@index',
        'api/payments/{id}' => 'PaymentController@edit',

        // Quản lý Sự Cố
        'api/incidents' => 'IncidentController@index',
        'api/incidents/{id}' => 'IncidentController@edit',
    ],
    'POST' => [
        // Quản lý Đăng Nhập
        'api/auth/login' => 'AuthController@login',
        'api/auth/logout' => 'AuthController@logout',
        
        // User API (RESTful)
        'api/user/incidents' => 'UserController@createIncident',
        
        'api/rooms' => 'RoomController@store',
        'api/students' => 'StudentController@store',
        'api/contracts' => 'ContractController@store',
        'api/payments' => 'PaymentController@store',
        'api/incidents' => 'IncidentController@store',
    ],
    'PUT' => [
        // User API (RESTful)
        'api/user/profile' => 'UserController@updateProfile',
        'api/user/password' => 'UserController@updatePassword',

        // Chú ý: Ở mô hình hybrid cũ ta dùng POST để update (qua HTML form)
        // Nhưng ở API chuẩn REST, ta dùng PUT
        'api/rooms/{id}' => 'RoomController@update',
        'api/students/{id}' => 'StudentController@update',
        'api/contracts/{id}' => 'ContractController@update',
        'api/payments/{id}' => 'PaymentController@update',
        'api/incidents/{id}' => 'IncidentController@update',
    ],
    'DELETE' => [
        // Ở API chuẩn REST, dùng HTTP DELETE
        'api/rooms/{id}' => 'RoomController@delete',
        'api/students/{id}' => 'StudentController@delete',
        'api/contracts/{id}' => 'ContractController@delete',
        'api/payments/{id}' => 'PaymentController@delete',
        'api/incidents/{id}' => 'IncidentController@delete',
    ]
];
