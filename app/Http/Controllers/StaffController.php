<?php

namespace App\Http\Controllers;

use App\Models\Example;
use Illuminate\Http\Request;
use Validator;
use DB;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\UserPermission;
use App\Models\Location;
use App\Models\Room;
use App\Models\Slider;
use App\Models\Staff;
use App\Models\Department;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class StaffController extends Controller
{
    //
    private $obj_info = ['name' => 'staff', 'routing' => 'admin.controller', 'title' => 'Staff', 'icon' => '<i class="text-muted fa-solid fa-clipboard-user"></i>'];
    public $args;

    private $model;
    private $model_department;
    private $submodel;
    private $tablename;
    private $columns = [];
    private $fprimarykey = 'staff_id';
    private $protectme = null;

    public $dflang;
    private $rcdperpage = 15; #record per page, set zero to get all record# set -1 to use default
    private $users;

    private $koboform_id;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(array $args = [])
    {
        //$this->middleware('auth');
        // dd($args['userinfo']);
        $this->obj_info['title'] =  __('dev.staff');

        $default_protectme = config('me.app.protectme');
        $this->protectme = [
            'display' => 'yes',
            'group' => [],
            'object' => [$this->obj_info['name']],
            'method'  => [
                'index' => $default_protectme['index'],
                'create' => $default_protectme['create'],
                'edit' => $default_protectme['edit'],
                'trash' => $default_protectme['trash'],
               'totrash' => $default_protectme['totrash'],
                'status' => $default_protectme['status'],
               // 'delete' => $default_protectme['delete'],
                'restore' => $default_protectme['restore'],
            ]
        ];
        $this->args = $args;
        $this->model = new Staff;
        $this->model_department = new Department;
         $this->model_user =new User;
        $this->tablename = $this->model->gettable();
        $this->dflang = df_lang();
        // dd($this->tablename);

        /*column*/
        $tbl_columns = getTableColumns($this->tablename);
        //dd($tbl_columns);
        foreach ($tbl_columns as $column) {
            //tbl
            if (strpos($column, 'tbl') !== false) {
                array_push($this->columns, $column);
            }
        }
        natsort($this->columns);
        //dd($this->columns);
    }

    public function getters($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
        return null;
    }

    public function setters($property, $value)
    {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }
    }

    public function default()
    {
        $staff = $this->model
            ->select(
                \DB::raw($this->tablename . ".* "),
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(" . $this->tablename . ".name,'$." . $this->dflang[0] . "')) AS text"),

            )->whereRaw('trash <> "yes"')->whereRaw('status <> "no"')->get();
        $department = $this->model_department
            ->select(
                \DB::raw("tbl_department.department_id as id "),
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(tbl_department.name,'$." . $this->dflang[0] . "')) AS title"),

            )->whereRaw('trash <> "yes"')->whereRaw('status <> "no"')->get();
        return ['staff' => $staff, 'department' => $department];
    } /*../function..*/
    public function listingModel()
    {
        #DEFIND MODEL#
        return $this->model
            ->leftJoin('users', 'users.id', 'tblstaffs.blongto')
            ->leftJoin('tbl_department', 'tbl_department.department_id', 'tblstaffs.department_id')
            ->select(
                \DB::raw($this->fprimarykey . ",JSON_UNQUOTE(JSON_EXTRACT(" . $this->tablename . ".name,'$." . $this->dflang[0] . "')) AS text,
                tblstaffs.image_url,
                tblstaffs.image_id_card,
                tblstaffs.gender,
                tblstaffs.phone_number,
                tblstaffs.email,
                tblstaffs.hire_date,
                tblstaffs.job_description,
                tblstaffs.create_date,
                tblstaffs.update_date,
                tblstaffs.status As status_staff,
                JSON_UNQUOTE(JSON_EXTRACT(tbl_department.name,'$." . $this->dflang[0] . "')) AS text_department,tblstaffs.department_id As departmentid,
                users.name As username"),
            )->whereRaw('tblstaffs.trash <> "yes"');
    } /*../function..*/
    public function listingtrash()
    {
        #DEFIND MODEL#
        return $this->model
            ->leftJoin('users', 'users.id', 'tblstaffs.blongto')
            ->leftJoin('tbl_department', 'tbl_department.department_id', 'tblstaffs.department_id')
            ->select(
                \DB::raw($this->fprimarykey . ",JSON_UNQUOTE(JSON_EXTRACT(" . $this->tablename . ".name,'$." . $this->dflang[0] . "')) AS text,
                tblstaffs.image_url,
                tblstaffs.image_id_card,
                tblstaffs.gender,
                tblstaffs.phone_number,
                tblstaffs.email,
                tblstaffs.create_date,
                tblstaffs.update_date,
                tblstaffs.status As status_staff,
                JSON_UNQUOTE(JSON_EXTRACT(tbl_department.name,'$." . $this->dflang[0] . "')) AS text_department,tblstaffs.department_id As departmentid,
                users.name As username"),
            )->whereRaw('tblstaffs.trash <> "no"');
    } /*../function..*/
    //JSON_UNQUOTE(JSON_EXTRACT(title, '$.".$this->dflang[0]."'))
    public function sfp($request, $results)
    {
        #Sort Filter Pagination#

        // CACHE SORTING INPUTS
        $allowed = array('title', $this->fprimarykey);
        $sort = in_array($request->input('sort'), $allowed) ? $request->input('sort') : $this->fprimarykey;
        $order = $request->input('order') === 'asc' ? 'asc' : 'desc';
        $results = $results->orderby($sort, $order);

        // FILTERS
        $appends = [];
        $querystr = [];
        if ($request->has('txtstaff') && !empty($request->input('txtstaff'))) {
            $qry = $request->input('txtstaff');
            $results = $results->where(function ($query) use ($qry) {
                $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(" . $this->tablename . ".name,'$." . $this->dflang[0] . "')) like '%" . $qry . "%'")
                    ->orwhereRaw("tblstaffs.phone_number like '%" . $qry . "%'")
                    ->orwhereRaw("tblstaffs.email like '%" . $qry . "%'")
                    ->orwhereRaw("tblstaffs.address like '%" . $qry . "%'")
                    ->orwhereRaw("tblstaffs.pob like '%" . $qry . "%'")
                    ->orwhereRaw("users.name like '%" . $qry . "%'");
            });
            array_push($querystr, "'JSON_UNQUOTE(JSON_EXTRACT(" . $this->tablename . ".name,'$." . $this->dflang[0] . "')) ='" . $qry);
            $appends = array_merge($appends, ["'JSON_UNQUOTE(JSON_EXTRACT(" . $this->tablename . ".name,'$." . $this->dflang[0] . "'))'" => $qry]);
        }
        if ($request->has('create_date') && !empty($request->input('create_date'))) {
            $dateTime = $request->input('create_date');
            $_date = explode('|', $dateTime);
            // dd(last($_date));
            $date = date_query($request, 'tblstaffs.create_date', 'create_date', $_date[0], last($_date));
            $results = $results->whereRaw($date['query']);
            array_push($querystr, 'create_date=' . $date['request_query_string']);
            $appends = array_merge($appends, $date['appends']);
        }
        if ($request->has('gender') && !empty($request->input('gender'))) {
            $qry = $request->input('gender');
            $results = $results->where("tblstaffs.gender", $qry);

            array_push($querystr, 'tblstaffs.department_id=' . $qry);
            $appends = array_merge($appends, ['tblstaffs.department_id' => $qry]);
        }
        if ($request->has('department_id') && !empty($request->input('department_id'))) {
            $qry = $request->input('department_id');
            $results = $results->where("tblstaffs.department_id", $qry);

            array_push($querystr, 'tblstaffs.department_id=' . $qry);
            $appends = array_merge($appends, ['tblstaffs.department_id' => $qry]);
        }
        if ($request->has('status') && !empty($request->input('status'))) {
            $qry = $request->input('status');
            $results = $results->where("tblstaffs.status", $qry);

            array_push($querystr, 'tblstaffs.status=' . $qry);
            $appends = array_merge($appends, ['tblstaffs.userstatus' => $qry]);
        }
        // PAGINATION and PERPAGE
        $perpage = null;
        $perpage_query = [];
        if ($request->has('perpage')) {
            $perpage = $request->input('perpage');
            $perpage_query = ['perpage=' . $perpage];
            $appends = array_merge($appends, ['perpage' => $perpage]);
        } elseif (null !== $this->rcdperpage && $this->rcdperpage != 0) {
            $perpage = $this->rcdperpage < 0 ? config('me.app.rpp') ?? 15 : $this->rcdperpage;
        }
        if (null !== $perpage) {
            $results = $results->paginate($perpage);
        }

        $appends = array_merge(
            $appends,
            [
                'sort'      => $request->input('sort'),
                'order'     => $request->input('order')
            ]
        );

        $pagination = $results->appends(
            $appends
        );

        // dd($pagination);
        $recordinfo = recordInfo($pagination->currentPage(), $pagination->perPage(), $pagination->total());

        return [
            'results'           => $results,
            'paginationlinks'    => $pagination->links("pagination::bootstrap-4"),
            'recordinfo'    => $recordinfo,
            'sort'          => $sort,
            'order'         => $order,
            'querystr'      => $querystr,
            'perpage_query' => $perpage_query,

        ];
    } /*../function..*/
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request, $condition = [], $setting = [])
    {

        $default = $this->default();
        $staff = $default['staff'];
        $department = $default['department'];
        $department = $department->pluck('title', 'id')->toArray();
        //dd('aaa');
        $results = $this->listingmodel();
        $sfp = $this->sfp($request, $results);
        // dd($sfp);


        $create_modal = url_builder(
            $this->obj_info['routing'],
            [$this->obj_info['name'], 'modal', ''],
            [],
        );
        $submit = url_builder(
            $this->obj_info['routing'],
            [$this->obj_info['name'], 'update_slide', ''],
            [],
        );
        $create_route = url_builder(
            $this->obj_info['routing'],
            [
                $this->obj_info['name'], 'create', ''
            ],
        );

        $trash_route = url_builder(
            $this->obj_info['routing'],
            [
                $this->obj_info['name'], 'trash', ''
            ],
        );

        return view('app.' . $this->obj_info['name'] . '.index')
            ->with([
                'obj_info'  => $this->obj_info,
                'route' => [
                    'create'  => $create_route,
                    'trash' => $trash_route,
                    'create_modal' => $create_modal,
                    'submit' => $submit,
                ],
                'fprimarykey'     => $this->fprimarykey,
                'caption' => __('dev.active'),
            ])
            ->with(['staff' => $staff])
            ->with(['department' => $department])
            ->with($sfp)
            ->with($setting);
    }

    public function trash(Request $request, $condition = [], $setting = [])
    {

        $default = $this->default();
        $staff = $default['staff'];
        $department = $default['department'];
        $department = $department->pluck('title', 'id')->toArray();
        //dd('aaa');
        $results = $this->listingtrash();
        $sfp = $this->sfp($request, $results);



        $create_route = url_builder(
            $this->obj_info['routing'],
            [
                $this->obj_info['name'], 'create', ''
            ],
        );

        $trash_route = url_builder(
            $this->obj_info['routing'],
            [
                $this->obj_info['name'], 'trash', ''
            ],
        );
        $index_route = url_builder(
            $this->obj_info['routing'],
            [
                $this->obj_info['name'], 'index', ''
            ],
        );

        return view('app.' . $this->obj_info['name'] . '.trash')
            ->with([
                'obj_info'  => $this->obj_info,
                'route' => [
                    'create'  => $create_route,
                    'trash' => $trash_route,
                    // 'index_route' => $index_route,

                ],
                'fprimarykey'     => $this->fprimarykey,
                'caption' => __('btn.btn_trash'),
                'istrash' => true,
            ])
            ->with(['staff' => $staff])
            ->with(['department' => $department])
            ->with($sfp)
            ->with($setting);
    }

    public function validator($request, $isupdate = false)
    {
        $newid = ($isupdate) ? $request->input($this->fprimarykey)  : $this->model->max($this->fprimarykey) + 1;
        $update_rules = [$this->fprimarykey => 'required'];

        $rules['title-en'] = ['required'];
        // $rules['img'] = ['required'];
        $validatorMessages = [
            /*'required' => 'The :attribute field can not be blank.'*/
            'required' => __('ccms.fieldreqire'),
        ];

        return Validator::make($request->all(), $rules, $validatorMessages);
    }
    public function setinfo($request, $isupdate = false)
    {

        $newid = ($isupdate) ? $request->input($this->fprimarykey)  : $this->model->max($this->fprimarykey) + 1;
        $tableData = [];
        $data = toTranslate($request, 'title', 0, true);
        $department_id = $request->input('department_id');
        $dob=$request->input('dob');
        $email = $request->input('email');
        $gender = $request->input('gender');
        $hire_date = $request->input('hire_date');
        $salary = $request->input('salary');
        $phone_number = $request->input('phone_number');
        $pob = toTranslate($request, 'pob', 0, true);
        $address = toTranslate($request, 'address', 0, true);
        $position = toTranslate($request, 'position', 0, true);
        $job_description = toTranslate($request, 'description', 0, true);
        $images = $request->file('images');
        $images_card = $request->file('images_id_card');
        $images_cv = $request->file('images_cv');
        // dd($request->input());

        if (!$isupdate) {
            if (!empty($images)) {
                $name = $images->getClientOriginalName();
            }
            if (!empty($images_card)) {
                $name_1 = $images_card->getClientOriginalName();
            }
            if (!empty($images_cv)) {
                $name_2 = $images_cv->getClientOriginalName();
            }
        }
        //dd($data);

        $tableData = [
            'staff_id' => $newid,
            'name' => json_encode($data),
            'department_id' =>  $department_id,
            'dob' => $dob,
            'email' =>  $email,
            'gender' =>  $gender,
            'salary' =>  $salary,
            'phone_number' =>  $phone_number,
            'pob' => json_encode($pob),
            'position' => json_encode($position),
            'address' =>  json_encode($address),
            'job_description' =>  json_encode($job_description),
            'image_url' =>  $name ?? '',
            'image_id_card' =>  $name_1 ?? '',
            'cv_document' =>  $name_2 ?? '',
            'create_date' => date("Y-m-d"),
            'blongto' => $this->args['userinfo']['id'],
            'trash' => 'no',
            'status' => 'yes',
            'hire_date' =>  $hire_date,
        ];
        if ($isupdate) {
            if (!empty($images)) {
                $name = $images->getClientOriginalName();
            } else {
                $name = $request->input('old_image');
            }
            if (!empty($images_card)) {
                $name_1 = $images_card->getClientOriginalName();
            } else {
                $name_1 = $request->input('old_image1');
            }
            if (!empty($images_cv)) {
                $name_2 = $images_cv->getClientOriginalName();
            } else {

                $name_2 = $request->input('old_image2');
            }
            // dd($request->input());
            $tableData = [
                'staff_id' => $newid,
                'name' => json_encode($data),
                'department_id' =>  $department_id,
                'dob' => $dob,
                'hire_date' =>  $hire_date,
                'email' =>  $email,
                'gender' =>  $gender,
                'pob' =>  json_encode($pob),
                'position' => json_encode($position),
                'salary' =>  $salary,
                'address' =>  json_encode($address),
                'phone_number' =>  $phone_number,
                'job_description' => json_encode($job_description),
                'image_url' =>  $name ?? '',
                'image_id_card' =>  $name_1 ?? '',
                'cv_document' =>  $name_2 ?? '',
                'update_date' => date("Y-m-d"),
                'blongto' => $this->args['userinfo']['id'],
                'trash' => 'no',
                'status' => 'yes',
            ];
            $tableData = array_except($tableData, [$this->fprimarykey, 'create_date',  'trash']);
        }
        return ['tableData' => $tableData, $this->fprimarykey => $newid];
    }
    public function create()
    {
        $default = $this->default();
        $department = $default['department'];
        $department = $department->pluck('title', 'id')->toArray();

        $sumit_route = url_builder(
            $this->obj_info['routing'],
            [$this->obj_info['name'], 'store', ''],
            [],
        );
        $new = url_builder(
            $this->obj_info['routing'],
            [$this->obj_info['name'], 'create', ''],
            [],
        );

        $cancel_route = url_builder(
            $this->obj_info['routing'],
            [$this->obj_info['name'], 'index', ''],
            [],
        );
        return view('app.' . $this->obj_info['name'] . '.create')
            ->with([
                'obj_info'  => $this->obj_info,
                'route' => ['submit'  => $sumit_route, 'cancel' => $cancel_route, 'new' => $new],
                'form' => ['save_type' => 'save'],
                'fprimarykey'     => $this->fprimarykey,
                'caption' => __('dev.new'),
                'isupdate' => false,
                'department' => $department,
                // 'img_check' => true,

            ]);
    } /*../function..*/

    public function store(Request $request)
    {
        $data = [];
        $obj_info = $this->obj_info;
        $routing = url_builder($obj_info['routing'], [$obj_info['name'], 'create']);
        if ($request->isMethod('post')) {
            $validate = $this->validator($request);
            if ($validate->fails()) {
                return response()
                    ->json(
                        [
                            "type" => "validator",
                            'status' => false,
                            "message" => __('ccms.fail_save'),
                            "data" => $validate->errors()
                        ],
                        422
                    );
            }
            $data = $this->setinfo($request);
            // dd($data);
            return $this->proceed_store($request, $data, $obj_info);
        } /*end if is post*/

        return response()
            ->json(
                [
                    "type" => "error",
                    "message" => __('ccms.fail_save'),
                    "data" => []
                ],
                422
            );
    }
    /* end function*/
    function proceed_store($request, $data, $obj_info)
    {
        // dd($data['tableData']);
        $save_status = $this->model->insert($data['tableData']);
        // dd($save_status);
        if ($save_status) {
            if (!empty($request->file('images')) && !empty('images_card')) {
                // dd($data);
                $request->file('images')->storeAs('staff', $data['tableData']['image_url']);
                $request->file('images_id_card')->storeAs('idCard', $data['tableData']['image_id_card']);
                $request->file('images_cv')->storeAs('cv', $data['tableData']['cv_document']);
            }


            $savetype = strtolower($request->input('savetype'));
            $success_ms = __('ccms.suc_save');
            $callback = 'formreset';
            if (is_axios()) {
                $callback = $request->input('jscallback');
            }
            return response()
                ->json(
                    [
                        "type" => "success",
                        "status" => $save_status,
                        "message" => __('ccms.suc_save'),
                        "callback" => $callback,
                        "data" => []
                    ],
                    200
                );
            // redirect()->back();
        }
        return response()
            ->json(
                [
                    "type" => "error",
                    'status' => false,
                    "message" =>__('ccms.fail_save'),
                    "data" => []
                ],
                422
            );
    }
    public function edit(Request $request, $id = 0)
    {

        #prepare for back to url after SAVE#
        if (!$request->session()->has('backurl')) {
            $request->session()->put('backurl', redirect()->back()->getTargetUrl());
        }

        $obj_info = $this->obj_info;

        $default = $this->default();
        //change piseth
        $input = null;

        #Retrieve Data#
        if (empty($id)) {
            $editid = $this->args['routeinfo']['id'];
        } else {
            $editid = $id;
        }

        if ($request->has($this->fprimarykey)) {
            $editid = $request->input($this->fprimarykey);
        }

        $input = $this->model
            ->where($this->fprimarykey, (int)$editid)
            //change piseth
            ->get();
        //dd($input->toSql());
        if ($input->isEmpty()) {
            $routing = url_builder($obj_info['routing'], [$obj_info['name'], 'index']);
            return response()
                ->json(
                    [
                        "type" => "url",
                        'status' => false,
                        'route' => ['url' => redirect()->back()->getTargetUrl()],
                        "message" => __('ccms.not_change'),
                        "data" => ['id' => $editid]
                    ],
                    422
                );
        }

        $department = $default['department'];
        $department = $department->pluck('title', 'id')->toArray();
        // dd($department);

        $input = $input->toArray()[0];
        $x = [];
        foreach ($input as $key => $value) {
            $x[$key] = $value;
        }

        $input = $x;

        // select department if disable
        $dep = $this->model_department
            ->select(
                \DB::raw("tbl_department.department_id as id "),
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(tbl_department.name,'$." . $this->dflang[0] . "')) AS title"),

            )->where('department_id', $input['department_id'])->get();
        $dep = $dep->pluck('title', 'id')->toArray();
        $department[array_key_first($dep)] = array_values($dep)[0] ?? "";
        // end

        // dd($input);



        $name = json_decode($input['name'], true);
        $pob = json_decode($input['pob'], true);
        $job_description = json_decode($input['job_description'], true);
        $address = json_decode($input['address'], true);
        $position = json_decode($input['position'], true);
        // dd($job_description);

        $sumit_route = url_builder(
            $this->obj_info['routing'],
            [$this->obj_info['name'], 'update', ''],
            [],
        );
        $cancel_route = url_builder(
            $this->obj_info['routing'],
            [$this->obj_info['name'], 'index', ''],
            [],
        );

        // dd($input);
        return view('app.' . $this->obj_info['name'] . '.create',) //change piseth
            ->with([
                'obj_info'  => $this->obj_info,
                'route' => ['submit'  => $sumit_route, 'cancel' => $cancel_route],
                'form' => ['save_type' => 'save'],
                'fprimarykey' => $this->fprimarykey,
                'caption' => __('btn.btn_edit'),
                'isupdate' => true,
                'input' => $input,
                'name' => $name,
                'department' => $department,
                'job_description' => $job_description,
                'pob' => $pob,
                'address' => $address,
                'position' => $position,
            ]);
    } /*../end fun..*/
    public function detail(Request $request, $id = 0)
    {

        #prepare for back to url after SAVE#
        if (!$request->session()->has('backurl')) {
            $request->session()->put('backurl', redirect()->back()->getTargetUrl());
        }

        $obj_info = $this->obj_info;

        $default = $this->default();
        //change piseth
        $input = null;

        #Retrieve Data#
        if (empty($id)) {
            $editid = $this->args['routeinfo']['id'];
        } else {
            $editid = $id;
        }

        if ($request->has($this->fprimarykey)) {
            $editid = $request->input($this->fprimarykey);
        }

        $input = $this->model
            ->where($this->fprimarykey, (int)$editid)
            //change piseth
            ->get();
        //dd($input->toSql());
        if ($input->isEmpty()) {
            $routing = url_builder($obj_info['routing'], [$obj_info['name'], 'index']);
            return response()
                ->json(
                    [
                        "type" => "url",
                        'status' => false,
                        'route' => ['url' => redirect()->back()->getTargetUrl()],
                        "message" => __('ccms.not_change'),
                        "data" => ['id' => $editid]
                    ],
                    422
                );
        }
      
       

        $input = $input->toArray()[0];
        $x = [];
        foreach ($input as $key => $value) {
            $x[$key] = $value;
        }

        $input = $x;
    // select department if disable
    $dep = $this->model_department
    ->select(
        \DB::raw("tbl_department.department_id as id "),
        DB::raw("JSON_UNQUOTE(JSON_EXTRACT(tbl_department.name,'$." . $this->dflang[0] . "')) AS title"),

    )->where('department_id', $input['department_id'])->get();
    $dep = $dep->pluck('title', 'id')->toArray();
    $department[array_key_first($dep)] = array_values($dep)[0] ?? "";
    // end
$user_item = $this->model_user
->select(
    \DB::raw("users.id,users.name"),
)->whereRaw('id= "'. $input['blongto'] .'"')->get();
$user = $user_item;
$user = $user->pluck('name', 'id')->toArray();

        $name = json_decode($input['name'], true);
        $job_description = json_decode($input['job_description'], true);
        $pob = json_decode($input['pob'], true);
        $address = json_decode($input['address'], true);
        $position = json_decode($input['position'], true);
        //dd($name);

        $sumit_route = url_builder(
            $this->obj_info['routing'],
            [$this->obj_info['name'], 'update', ''],
            [],
        );
        $cancel_route = redirect()->back()->getTargetUrl();

        // dd($input);
        return view('app.' . $this->obj_info['name'] . '.detail',) //change piseth
            ->with([
                'obj_info'  => $this->obj_info,
                'route' => ['submit'  => $sumit_route, 'cancel' => $cancel_route],
                'form' => ['save_type' => 'save'],
                'fprimarykey' => $this->fprimarykey,
                'caption' => __('btn.btn_detail'),
                'isupdate' => true,
                'input' => $input,
                'name' => $name,
                'department' => $department,
                'address' => $address,
                'pob' => $pob,
                'job_description' => $job_description,
                'position' => $position,
                'user' => $user,
            ]);
    } /*../end fun..*/

    public function update(Request $request)
    {
        $obj_info = $this->obj_info;
        $routing = url_builder($obj_info['routing'], [$obj_info['name'], 'create']);
        if ($request->isMethod('post')) {
            $validator = $this->validator($request, true);
            // dd($validator);
            if ($validator->fails()) {

                $routing = url_builder($obj_info['routing'], [$obj_info['name'], 'create']);
                return response()
                    ->json(
                        [
                            "type" => "validator",
                            'status' => false,
                            'route' => ['url' => $routing],
                            "message" => __('ccms.fail_save'),
                            "data" => $validator->errors()
                        ],
                        422
                    );
            }

            $data = $this->setinfo($request, true);
            // dd($data);
            return $this->proceed_update($request, $data, $obj_info);
        } /*end if is post*/

        return response()
            ->json(
                [
                    "type" => "error",
                    "message" => __('ccms.fail_save'),
                    "data" => []
                ],
                422
            );
    }/*../end fun..*/

    function proceed_update($request, $data, $obj_info)
    {
        // dd($request->images_cv);

        $update_status = $this->model
            ->where($this->fprimarykey, $data[$this->fprimarykey])
            ->update($data['tableData']);

        if ($update_status) {
            if ($request->input('old_image') != $data['tableData']['image_url']) {
                $request->file('images')->storeAs('staff', $data['tableData']['image_url']);
                @unlink('storage/app/staff/' . $request->input('old_image'));
            }
            if ($request->input('old_image1') != $data['tableData']['image_id_card']) {
                $request->file('images_id_card')->storeAs('idCard', $data['tableData']['image_id_card']);
                @unlink('storage/app/idCard/' . $request->input('old_image1'));
            }
            if ($request->input('old_image2') != $data['tableData']['cv_document']) {
                $request->file('images_cv')->storeAs('cv', $data['tableData']['cv_document']);
                @unlink('storage/app/cv/' . $request->input('old_image2'));
            }

            $savetype = strtolower($request->input('savetype'));
            $id = $data['staff_id'];
            $rout_to = save_type_route($savetype, $obj_info, $id);
            $success_ms = __('ccms.suc_save');
            $callback = '';
            if (is_axios()) {
                $callback = $request->input('jscallback');
            }
            return response()
                ->json(
                    [
                        "type" => "success",
                        "status" => $update_status,
                        "message" => $success_ms,
                        "route" => $rout_to,
                        "callback" => $callback,
                        "data" => [
                            $this->fprimarykey => $data['staff_id'],
                            'id' => $data['staff_id']
                        ]
                    ],
                    200
                );
        }
        return response()
            ->json(
                [
                    "type" => "error",
                    'status' => false,
                    "message" => __('ccms.not_change'),
                    "data" => []
                ],
                422
            );
    }
    /* end function*/
    public function restore(Request $request, $id = 0)
    {
        $obj_info = $this->obj_info;
        #Retrieve Data#
        if (empty($id)) {
            $editid = $this->args['routeinfo']['id'];
        } else {
            $editid = $id;
        }

        //$routing = url_builder($obj_info['routing'], [$obj_info['name'], 'index']);
        $trash = $this->model->where('staff_id', $editid)->update(["trash" => "no"]);

        if ($trash) {
            return response()
                ->json(
                    [
                        "type" => "url",
                        "status" => true,
                        'route' => ['url' => redirect()->back()->getTargetUrl()],
                        "message" => __('ccms.suc_restore'),

                        "data" => []
                    ],
                    200
                );
        }
        return response()
            ->json(
                [
                    "type" => "error",
                    'status' => false,
                    'route' => ['url' => redirect()->back()->getTargetUrl()],
                    "message" => __('ccms.not_change'),
                    "data" => ['id' => $editid]
                ],
                422
            );
    }
    public function totrash(Request $request, $id = 0)
    {
        $obj_info = $this->obj_info;
        #Retrieve Data#
        if (empty($id)) {
            $editid = $this->args['routeinfo']['id'];
        } else {
            $editid = $id;
        }

        //$routing = url_builder($obj_info['routing'], [$obj_info['name'], 'index']);
        $trash = $this->model->where('staff_id', $editid)->update(["tblstaffs.trash" => "yes"]);

        if ($trash) {
            return response()
                ->json(
                    [
                        "type" => "url",
                        'status' => true,
                        'route' => ['url' => redirect()->back()->getTargetUrl()],
                        "message" => __('ccms.suc_delete'),
                        "data" => ['staff_id' => $editid]
                    ],
                    200
                );
        }
        return response()
            ->json(
                [
                    "type" => "error",
                    'status' => false,
                    'route' => ['url' => redirect()->back()->getTargetUrl()],
                    "message" => __('ccms.not_change'),
                    "data" => ['id' => $editid]
                ],
                422
            );
    }
    public function disable(Request $request, $id = 0)
    {
        $obj_info = $this->obj_info;
        #Retrieve Data#
        if (empty($id)) {
            $editid = $this->args['routeinfo']['id'];
        } else {
            $editid = $id;
        }

        //$routing = url_builder($obj_info['routing'], [$obj_info['name'], 'index']);
        $trash = $this->model->where('staff_id', $editid)->update(["tblstaffs.status" => "no"]);

        if ($trash) {
            return response()
                ->json(
                    [
                        "type" => "url",
                        'status' => true,
                        'route' => ['url' => redirect()->back()->getTargetUrl()],
                        "message" => __('ccms.suc_disable'),
                        "data" => ['staff_id' => $editid]
                    ],
                    200
                );
        }
        return response()
            ->json(
                [
                    "type" => "error",
                    'status' => false,
                    'route' => ['url' => redirect()->back()->getTargetUrl()],
                    "message" => __('ccms.not_change'),
                    "data" => ['id' => $editid]
                ],
                422
            );
    }
    public function enable(Request $request, $id = 0)
    {
        $obj_info = $this->obj_info;
        #Retrieve Data#
        if (empty($id)) {
            $editid = $this->args['routeinfo']['id'];
        } else {
            $editid = $id;
        }

        //$routing = url_builder($obj_info['routing'], [$obj_info['name'], 'index']);
        $trash = $this->model->where('staff_id', $editid)->update(["tblstaffs.status" => "yes"]);

        if ($trash) {
            return response()
                ->json(
                    [
                        "type" => "url",
                        'status' => true,
                        'route' => ['url' => redirect()->back()->getTargetUrl()],
                        "message" => __('ccms.suc_enable'),
                        "data" => ['staff_id' => $editid]
                    ],
                    200
                );
        }
        return response()
            ->json(
                [
                    "type" => "error",
                    'status' => false,
                    'route' => ['url' => redirect()->back()->getTargetUrl()],
                    "message" => __('ccms.not_change'),
                    "data" => ['id' => $editid]
                ],
                422
            );
    }
    public function status(Request $request, $id = 0)
    {
        $obj_info = $this->obj_info;
        #Retrieve Data#
        if (empty($id)) {
            $editid = $this->args['routeinfo']['id'];
        } else {
            $editid = $id;
        }
        $req_status = $request->status;
        $status = "no";
        if ($req_status == "no") {
            $status = "yes";
        }
        $trash = $this->model->where('staff_id', $editid)->update(["tblstaffs.status" => "" . $status]);
        if ($trash && $status == "yes") {
            return response()
                ->json(
                    [
                        "type" => "url",
                        'status' => true,
                        'route' => ['url' => redirect()->back()->getTargetUrl()],
                        "message" => __('ccms.suc_enable'),
                        "data" => ['staff_id' => $editid]
                    ],
                    200
                );
        }
        if ($trash && $status == "no") {
            return response()
                ->json(
                    [
                        "type" => "url",
                        'status' => true,
                        'route' => ['url' => redirect()->back()->getTargetUrl()],
                        "message" => __('ccms.suc_disable'),
                        "data" => ['staff_id' => $editid]
                    ],
                    200
                );
        }
        return response()
            ->json(
                [
                    "type" => "error",
                    'status' => false,
                    'route' => ['url' => redirect()->back()->getTargetUrl()],
                    "message" => __('ccms.not_change'),
                    "data" => ['id' => $editid]
                ],
                422
            );
    }
    public function staff(Request $request, $id = 0)
    {

        #prepare for back to url after SAVE#
        if (!$request->session()->has('backurl')) {
            $request->session()->put('backurl', redirect()->back()->getTargetUrl());
        }

        $obj_info = $this->obj_info;

        $default = $this->default();
        //change piseth
        $input = null;

        #Retrieve Data#
        if (empty($id)) {
            $editid = $this->args['routeinfo']['id'];
        } else {
            $editid = $id;
        }

        if ($request->has($this->fprimarykey)) {
            $editid = $request->input($this->fprimarykey);
        }

        $input = $this->model
            ->where($this->fprimarykey, (int)$editid)
            //change piseth
            ->get();
        //dd($input->toSql());
        if ($input->isEmpty()) {
            $routing = url_builder($obj_info['routing'], [$obj_info['name'], 'index']);
            return response()
                ->json(
                    [
                        "type" => "url",
                        'status' => false,
                        'route' => ['url' => redirect()->back()->getTargetUrl()],
                        "message" => __('ccms.not_change'),
                        "data" => ['id' => $editid]
                    ],
                    422
                );
        }
        $input = $input->toArray()[0];
        $x = [];
        foreach ($input as $key => $value) {
            $x[$key] = $value;
        }

        $input = $x;
        // dd($input);
        $department_item = $this->model_department
            ->select(
                \DB::raw("tbl_department.department_id"),
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(tbl_department.name,'$." . $this->dflang[0] . "')) AS title"),

            )->whereRaw('department_id= "' . $input['department_id'] . '"')->get();

        $department = $department_item;
        $department = $department->pluck('title', 'id')->toArray();

        $name = json_decode($input['name'], true);
        $job_description = json_decode($input['job_description'], true);
        $pob = json_decode($input['pob'], true);
        $address = json_decode($input['address'], true);
        //dd($name);

        $sumit_route = url_builder(
            $this->obj_info['routing'],
            [$this->obj_info['name'], 'update', ''],
            [],
        );
        $cancel_route = redirect()->back()->getTargetUrl();

        // dd($input);
        return view('app.' . $this->obj_info['name'] . '.staff',) //change piseth
            ->with([
                'obj_info'  => $this->obj_info,
                'route' => ['submit'  => $sumit_route, 'cancel' => $cancel_route],
                'form' => ['save_type' => 'save'],
                'fprimarykey' => $this->fprimarykey,
                'caption' => __('btn.btn_detail'),
                'isupdate' => true,
                'input' => $input,
                'name' => $name,
                'department' => $department,
                'address' => $address,
                'pob' => $pob,
                'job_description' => $job_description,
            ]);
    } /*../end fun..*/
}