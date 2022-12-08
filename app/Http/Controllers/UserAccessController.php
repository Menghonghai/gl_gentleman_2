<?php

namespace App\Http\Controllers;

use App\Models\History;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\UserPermission;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use PDO;

class UserAccessController extends Controller
{
    private $path = "App\Http\Controllers\\";

    //



    public function apiAuth(Request $request)
    {
        //
        $login = request()->input('username');
        $credentials =  [
            'name' => function ($query) use ($login) {
                $query->whereraw("(name=? or email=?)", [$login, $login]);
                // ->orwhere('email', $login);
            },
            'password' => request()->input('password'),
            'userstatus' => function ($query) {
                $query->where('userstatus', '<>', 'no')->where('trash', '<>', 'yes');
            },
            // 'group_id' => function ($query) {
            //     $query->where('group_id', '=', 2);
            // },
            'permission_id' => function ($query) {
                $query->where('permission_id', '<', 4);
            },

        ];

        //


        if (\Auth::attempt($credentials)) {

            $user = auth()->user();
            $token = $user->api_token;
            if (empty($user->api_token)) {
                $token = $user->createToken('myapptoken')->plainTextToken;
                $user->api_token = $token;
                $user->save();
            }
            $permission_id = $user->permission_id;
            $user_permission = UserPermission::getpermission($permission_id);
            $permission_setting = ($user_permission) ? json_decode($user_permission->levelsetting) : [];
            $userinfo = [
                'id' => $user->id,
                'permission_id' => $user->permission_id,
                'name' => $user->name,
                'email' => $user->email,
                'fullname' => $user->fullname,
                'province_id' => $user->province_id,
                'district_id' => $user->district_id,
                'commune_id' => $user->commune_id,
                'branch_id' => $user->branch_id,
                'wh_id' => $user->wh_id,
                'permission_setting' => $permission_setting
            ];
            return response()
                ->json(
                    [
                        "type" => "success",
                        "status" => true,
                        "message" => 'Login is successfully',
                        "token" => $token,
                        "user" => $userinfo
                    ],
                    200
                );
        } else {
            return response()
                ->json(
                    [
                        "type" => "error",
                        'status' => false,
                        "message" => 'Login is false',
                        "data" => []
                    ],
                    422
                );
        }
    }

    public function index(Request $request, $obj = 'home', $act = 'index', $id = 0, $title = '')
    {

        langConfig($request);
        $dflang = df_lang();



        $d_path = $this->path . "HomeController";
        $a_path = $this->path . ucfirst($obj) . "Controller"; //ArticleController

        $users = auth()->user();
        // if (!$users) {
        //     $credentials = [
        //         'email' => 'admin@gmail.com',
        //         'password' => '123456',
        //     ];
        //     if (auth()->attempt($credentials)) {
        //         $users = auth()->user();
        //     }
        // }

        if (empty($users->api_token)) {
            $token = $users->createToken('myapptoken')->plainTextToken;
            $users->api_token = $token;
            $users->save();
        }


        $permission_id = $users->permission_id;
        $user_permission = UserPermission::getpermission($permission_id);
        $permission_setting = ($user_permission) ? json_decode($user_permission->levelsetting) : [];
        $userinfo = ['id' => $users->id, 'permission_id' => $users->permission_id, 'name' => $users->name, 'pwd' => $users->password ?? '', 'branch_id' => $users->branch_id, 'wh_id' => $users->wh_id, 'permission_setting' => $permission_setting];

        $routeinfo = ['obj' => $obj, 'act' => $act, 'id' => $id, 'title' => $title];
        \View::share('userinfo', $userinfo);
        try {
            $request->session()->put('routeinfo', $routeinfo);
        } catch (\Exception $e) {
            goto a;
        }
        a:

        // dd($users->toArray());
        $args = [
            //$request,
            'userinfo' => array_merge($users->toArray(), $userinfo),
            'routeinfo' => $routeinfo
        ];

        \View::share(['dflang' => $dflang, 'count' => $count ?? '', 'user_info' => $userinfo, 'args' => $args]);
        if (class_exists($a_path)) {
            $a_class = new $a_path($args);/*acees class*/

            if (method_exists($a_class, $act)) {

                $protectme = $a_class->getters('protectme');
                // dd($userinfo);
                $gonext = check_user_permission($obj, $act, $protectme, $userinfo);
                if ($gonext) {
                    if ($id)
                        $getact = $a_class->$act($request, $id);
                    else {
                        $getact = $a_class->$act($request);
                    }
                    // dd($getact, $userinfo, $act, $a_class->getters("obj_info"), $request->ip());




                    if (is_object($getact) && method_exists($getact, 'getContent')) {
                        $getact_toobj = json_decode($getact->getContent());
                        if (isset($getact_toobj->type) && $getact_toobj->type == 'url') {
                            if (isset($getact_toobj->route->url)) {
                                // dd(route);
                                return \Redirect::to($getact_toobj->route->url)
                                    ->with((array)$getact_toobj);
                            }
                        }
                    }

                    return $getact;
                } else {
                    if (is_axios()) {
                        return response()
                            ->json(
                                [
                                    "type" => "error",
                                    "message" => 'You have no permission to access this!',
                                ],
                                422
                            );
                    }
                    return view('nopermission');
                }
            }
        }

        if (is_axios()) {

            return response()
                ->json(
                    [
                        "type" => "error",
                        "message" => 'Page/Action you try to access is not found!',
                    ],
                    422
                );
        }
        return view('notfound');
    }
}
