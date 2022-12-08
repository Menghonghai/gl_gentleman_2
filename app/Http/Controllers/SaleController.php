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
use App\Models\Equipment;
use App\Models\Inventory;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SaleController extends Controller
{
    //
    private $obj_info = ['name' => 'sale', 'routing' => 'admin.controller', 'title' => 'Sale', 'icon' => '<i class="fas fa-hammer text-muted"></i>'];
    public $args;

    private $model;
    private $model_inventory;
    private $model_vendor;
    private $submodel;
    private $tablename;
    private $columns = [];
    private $fprimarykey = 'equipment_id';
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
        $this->obj_info['title'] =  __('dev.sale');

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
                // 'invoice' => $default_protectme['invoice'],
               // 'ToTrash' => $default_protectme['ToTrash'],
                'status' => $default_protectme['status'],
               // 'delete' => $default_protectme['delete'],
                'restore' => $default_protectme['restore'],
            ]
        ];

        $this->args = $args;
        $this->model = new Equipment;
        $this->model_inventory = new Inventory;
        $this->model_vendor = new Vendor;
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
        $equipment = $this->model
            ->select(
                \DB::raw($this->tablename . ".* "),
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(" . $this->tablename . ".name,'$." . $this->dflang[0] . "')) AS text"),

            )->whereRaw('trash <> "yes"')->get();
        $inventory = $this->model_inventory
        ->select(
            \DB::raw("tblinventorys.inventory_id as id "),
            DB::raw("JSON_UNQUOTE(JSON_EXTRACT(tblinventorys.name,'$." . $this->dflang[0] . "')) AS title"),

        )->whereRaw('trash <> "yes"')->get();
        $vendor = $this->model_vendor
        ->select(
            \DB::raw("tblvendors.vendor_id as id"),
            DB::raw("JSON_UNQUOTE(JSON_EXTRACT(tblvendors.name,'$." . $this->dflang[0] . "')) AS title"),

        )->whereRaw('trash <> "yes"')->whereRaw('type <> "shop"')->get();
        return ['equipment' => $equipment,'inventory' => $inventory,'vendor' => $vendor];
    } /*../function..*/
    public function listingModel()
    {
        #DEFIND MODEL#
        return $this->model
            ->leftJoin('users', 'users.id', 'tblequipments.blongto')
            ->leftJoin('tblinventorys', 'tblinventorys.inventory_id', 'tblequipments.inventory_id')
            ->select(
                \DB::raw($this->fprimarykey . ",JSON_UNQUOTE(JSON_EXTRACT(" . $this->tablename . ".name,'$." . $this->dflang[0] . "')) AS text,tblequipments.location,tblequipments.seria_number,tblequipments.create_date,tblequipments.update_date,tblequipments.status As status_equipment,
                        JSON_UNQUOTE(JSON_EXTRACT(tblinventorys.name,'$." . $this->dflang[0] . "')) AS text_inventory,tblequipments.inventory_id As inventoryid,
                        users.name As username"),
            )->whereRaw('tblequipments.trash <> "yes"');
    } /*../function..*/
    public function listingtrash()
    {
        #DEFIND MODEL#
        return $this->model
            ->leftJoin('users', 'users.id', 'tblequipments.blongto')
            ->leftJoin('tblinventorys', 'tblinventorys.inventory_id', 'tblequipments.inventory_id')
            ->select(
                \DB::raw($this->fprimarykey . ",JSON_UNQUOTE(JSON_EXTRACT(" . $this->tablename . ".name,'$." . $this->dflang[0] . "')) AS text,tblequipments.location,tblequipments.seria_number,tblequipments.create_date,tblequipments.update_date,tblequipments.status As status_equipment,
                        JSON_UNQUOTE(JSON_EXTRACT(tblinventorys.name,'$." . $this->dflang[0] . "')) AS text_inventory,tblequipments.inventory_id As inventoryid,
                        users.name As username"),
            )->whereRaw('tblequipments.trash <> "no"');
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
        if ($request->has('txtequipment') && !empty($request->input('txtequipment'))) {
            $qry = $request->input('txtequipment');
            $results = $results->where(function ($query) use ($qry) {
                $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(" . $this->tablename . ".name,'$." . $this->dflang[0] . "')) like '%" . $qry . "%'")
                ->orwhereRaw($this->fprimarykey . " like '%" . $qry . "%'")
                ->orwhereRaw("tblequipments.seria_number like '%" . $qry . "%'")
                ->orwhereRaw("tblequipments.location like '%" . $qry . "%'")
                ->orwhereRaw("users.name like '%" . $qry . "%'")
                ->orwhereRaw("tblequipments.model like '%" . $qry . "%'");
            });
            array_push($querystr, "'JSON_UNQUOTE(JSON_EXTRACT(" . $this->tablename . ".name,'$." . $this->dflang[0] . "')) ='" . $qry);
            $appends = array_merge($appends, ["'JSON_UNQUOTE(JSON_EXTRACT(" . $this->tablename . ".name,'$." . $this->dflang[0] . "'))'" => $qry]);
        }
        if ($request->has('inventory_id') && !empty($request->input('inventory_id'))) {
            $qry = $request->input('inventory_id');
            $results = $results->where("tblequipments.inventory_id", $qry);
    
            array_push($querystr, 'tblequipments.inventory_id=' . $qry);
            $appends = array_merge($appends, ['tblequipments.inventory_id' => $qry]);
        }
        if ($request->has('vendor_id') && !empty($request->input('vendor_id'))) {
            $qry = $request->input('vendor_id');
            $results = $results->where("tblequipments.vendor_id", $qry);
    
            array_push($querystr, 'tblequipments.vendor_id=' . $qry);
            $appends = array_merge($appends, ['tblequipments.vendor_id' => $qry]);
        }
        if ($request->has('status') && !empty($request->input('status'))) {
            $qry = $request->input('status');
            $results = $results->where("tblequipments.status", $qry);
    
            array_push($querystr, 'utblequipments.status=' . $qry);
            $appends = array_merge($appends, ['tblequipments.userstatus' => $qry]);
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
        $equipment = $default['equipment'];
        $inventory=$default['inventory'];
        $inventory=$inventory->pluck('title','id')->toArray();

        $vendor=$default['vendor'];
        $vendor=$vendor->pluck('title','id')->toArray();
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
        $invoice_route = url_builder(
            $this->obj_info['routing'],
            [
                $this->obj_info['name'], 'invoice', ''
            ],
        );
        $checkout_route = url_builder(
            $this->obj_info['routing'],
            [
                $this->obj_info['name'], 'checkout', ''
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
                    'invoice'  => $invoice_route,
                    'checkout'  => $checkout_route,
                    'trash' => $trash_route,
                    'create_modal' => $create_modal,
                    'submit' => $submit,
                ],
                'fprimarykey'     => $this->fprimarykey,
                'caption' => __('dev.active'),
            ])
            ->with(['equipment' => $equipment])
            ->with(['inventory' => $inventory])
            ->with(['vendor' => $vendor])
            ->with($sfp)
            ->with($setting);
    }

    public function trash(Request $request, $condition = [], $setting = [])
    {

        $default = $this->default();
        $equipment = $default['equipment'];
        $inventory=$default['inventory'];
        $inventory=$inventory->pluck('title','id')->toArray();

        $vendor=$default['vendor'];
        $vendor=$vendor->pluck('title','id')->toArray();
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
            ->with(['equipment' => $equipment])
            ->with(['inventory' => $inventory])
            ->with(['vendor' => $vendor])
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
        $location=$request->input('location');
        $seria_number=$request->input('seria_number');
        $model=$request->input('model');
        $cost=$request->input('cost');
        $warranty_date=$request->input('warranty_date');
        $inventory_id=$request->input('inventory_id');
        $vendor_id=$request->input('vendor_id');
        $description = toTranslate($request, 'description', 0, true);
        $images = $request->file('images');

        if (!empty($images)) {
            $name = $images->getClientOriginalName();
           
        }
            $tableData = [
                'equipment_id' => $newid,
                'name' => json_encode($data),
                'location' =>  $location,
                'seria_number' =>  $seria_number,
                'model' =>  $model,
                'cost' =>  $cost,
                'warranty_date' =>  $warranty_date,
                'inventory_id' =>  $inventory_id,
                'vendor_id' =>  $vendor_id,
                'description' =>  json_encode($description),
                'image_url' =>  $name ?? '',
                'create_date' => date("Y-m-d"),
                'blongto' => $this->args['userinfo']['id'],
                'trash' => 'no',
                'status' => 'yes',
            ];
        if ($isupdate) {
            if (!empty($images)) {
                $name = $images->getClientOriginalName();
            } else {
                $name = $request->input('old_image');
            }
            $tableData = [
                'equipment_id' => $newid,
                'name' => json_encode($data),
                'location' =>  $location,
                'seria_number' =>  $seria_number,
                'model' =>  $model,
                'cost' =>  $cost,
                'warranty_date' =>  $warranty_date,
                'inventory_id' =>  $inventory_id,
                'vendor_id' =>  $vendor_id,
                'description' =>  $description,
                'image_url' =>  $name ?? '',
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
        $inventory=$default['inventory'];
        $inventory=$inventory->pluck('title','id')->toArray();

        $vendor=$default['vendor'];
        $vendor=$vendor->pluck('title','id')->toArray();

        
        
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
                'inventory'=>$inventory,
                'vendor'=>$vendor,
                // 'img_check' => true,

            ]);
    } /*../function..*/
    public function invoice()
    {
        $default = $this->default();
        $inventory=$default['inventory'];
        $inventory=$inventory->pluck('title','id')->toArray();

        $vendor=$default['vendor'];
        $vendor=$vendor->pluck('title','id')->toArray();

        
        
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
        return view('app.' . $this->obj_info['name'] . '.invoice')
            ->with([
                'obj_info'  => $this->obj_info,
                'route' => ['submit'  => $sumit_route, 'cancel' => $cancel_route, 'new' => $new],
                'form' => ['save_type' => 'save'],
                'fprimarykey'     => $this->fprimarykey,
                'caption' => __('dev.new'),
                'isupdate' => false,
                'inventory'=>$inventory,
                'vendor'=>$vendor,
                // 'img_check' => true,

            ]);
    } /*../function..*/
    public function checkout()
    {
        $default = $this->default();
        $inventory=$default['inventory'];
        $inventory=$inventory->pluck('title','id')->toArray();

        $vendor=$default['vendor'];
        $vendor=$vendor->pluck('title','id')->toArray();

        
        
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
        return view('app.' . $this->obj_info['name'] . '.checkout')
            ->with([
                'obj_info'  => $this->obj_info,
                'route' => ['submit'  => $sumit_route, 'cancel' => $cancel_route, 'new' => $new],
                'form' => ['save_type' => 'save'],
                'fprimarykey'     => $this->fprimarykey,
                'caption' => __('dev.new'),
                'isupdate' => false,
                'inventory'=>$inventory,
                'vendor'=>$vendor,
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
            if(!empty($request->file('images'))){
                $request->file('images')->storeAs('equipment', $data['tableData']['image_url']);
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

        $inventory=$default['inventory'];
        $inventory=$inventory->pluck('title','id')->toArray();

        $vendor=$default['vendor'];
        $vendor=$vendor->pluck('title','id')->toArray();

        $input = $input->toArray()[0];
        $x = [];
        foreach ($input as $key => $value) {
            $x[$key] = $value;
        }

        $input = $x;

        $name = json_decode($input['name'], true);
        $description = json_decode($input['description'], true);
        //dd($description);

        $sumit_route = url_builder(
            $this->obj_info['routing'],
            [$this->obj_info['name'], 'update', ''],
            [],
        );
        $cancel_route = url_builder(
            $this->obj_info['routing'],
            [$this->obj_info['name'], 'index', ''],
            [],);

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
                'inventory'=>$inventory,
                'vendor'=>$vendor,
                'vendor'=>$vendor,
                'description'=>$description,

            ]);
    } /*../end fun..*/
    // public function detail(Request $request, $id = 0)
    // {

    //     #prepare for back to url after SAVE#
    //     if (!$request->session()->has('backurl')) {
    //         $request->session()->put('backurl', redirect()->back()->getTargetUrl());
    //     }

    //     $obj_info = $this->obj_info;

    //     $default = $this->default();
    //     //change piseth
    //     $input = null;

    //     #Retrieve Data#
    //     if (empty($id)) {
    //         $editid = $this->args['routeinfo']['id'];
    //     } else {
    //         $editid = $id;
    //     }

    //     if ($request->has($this->fprimarykey)) {
    //         $editid = $request->input($this->fprimarykey);
    //     }

    //     $input = $this->model
    //         ->where($this->fprimarykey, (int)$editid)
    //         //change piseth
    //         ->get();
    //     //dd($input->toSql());
    //     if ($input->isEmpty()) {
    //         $routing = url_builder($obj_info['routing'], [$obj_info['name'], 'index']);
    //         return response()
    //             ->json(
    //                 [
    //                     "type" => "url",
    //                     'status' => false,
    //                     'route' => ['url' => redirect()->back()->getTargetUrl()],
    //                     "message" => 'Your edit is not affected',
    //                     "data" => ['id' => $editid]
    //                 ],
    //                 422
    //             );
    //     }

    //     $inventory=$default['inventory'];
    //     $inventory=$inventory->pluck('title','id')->toArray();

    //     $vendor=$default['vendor'];
    //     $vendor=$vendor->pluck('title','id')->toArray();

    //     $input = $input->toArray()[0];
    //     $x = [];
    //     foreach ($input as $key => $value) {
    //         $x[$key] = $value;
    //     }

    //     $input = $x;

    //     $name = json_decode($input['name'], true);
    //     $description = json_decode($input['description'], true);
    //     //dd($name);

    //     $sumit_route = url_builder(
    //         $this->obj_info['routing'],
    //         [$this->obj_info['name'], 'update', ''],
    //         [],
    //     );
    //     $cancel_route = url_builder(
    //         $this->obj_info['routing'],
    //         [$this->obj_info['name'], 'index', ''],
    //         [],);

    //     // dd($input);
    //     return view('app.' . $this->obj_info['name'] . '.detail',) //change piseth
    //         ->with([
    //             'obj_info'  => $this->obj_info,
    //             'route' => ['submit'  => $sumit_route, 'cancel' => $cancel_route],
    //             'form' => ['save_type' => 'save'],
    //             'fprimarykey' => $this->fprimarykey,
    //             'caption' => __('btn.btn_detail'),
    //             'isupdate' => true,
    //             'input' => $input,
    //             'name' => $name,
    //             'inventory'=>$inventory,
    //             'vendor'=>$vendor,
    //             'description'=>$description,

    //         ]);
    // } /*../end fun..*/

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
                            "message" =>__('ccms.fail_save'),
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
        // dd($data);

        $update_status = $this->model
            ->where($this->fprimarykey, $data[$this->fprimarykey])
            ->update($data['tableData']);

        if ($update_status) {
            if ($request->input('old_image') != $data['tableData']['image_url']) {
                $request->file('images')->storeAs('equipment', $data['tableData']['image_url']);
                @unlink('storage/app/equipment/' . $request->input('old_image'));
            }
            $savetype = strtolower($request->input('savetype'));
            $id = $data['equipment_id'];
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
                            $this->fprimarykey => $data['equipment_id'],
                            'id' => $data['equipment_id']
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
        $trash = $this->model->where('equipment_id', $editid)->update(["trash" => "no"]);

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
        $trash = $this->model->where('equipment_id', $editid)->update(["tblequipments.trash" => "yes"]);

        if ($trash) {
            return response()
                ->json(
                    [
                        "type" => "url",
                        'status' => true,
                        'route' => ['url' => redirect()->back()->getTargetUrl()],
                        "message" => __('ccms.suc_delete'),
                        "data" => ['equipment_id' => $editid]
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
        $trash = $this->model->where('equipment_id', $editid)->update(["status" => "no"]);

        if ($trash) {
            return response()
                ->json(
                    [
                        "type" => "url",
                        'status' => true,
                        'route' => ['url' => redirect()->back()->getTargetUrl()],
                        "message" => __('ccms.suc_disable'),
                        "data" => ['equipment_id' => $editid]
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
        $trash = $this->model->where('equipment_id', $editid)->update(["status" => "yes"]);

        if ($trash) {
            return response()
                ->json(
                    [
                        "type" => "url",
                        'status' => true,
                        'route' => ['url' => redirect()->back()->getTargetUrl()],
                        "message" => __('ccms.suc_enable'),
                        "data" => ['equipment_id' => $editid]
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
        $trash = $this->model->where('equipment_id', $editid)->update(["status" => "" . $status]);
        if ($trash && $status == "yes") {
            return response()
                ->json(
                    [
                        "type" => "url",
                        'status' => true,
                        'route' => ['url' => redirect()->back()->getTargetUrl()],
                        "message" => __('ccms.suc_enable'),
                        "data" => ['equipment_id' => $editid]
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
                        "data" => ['equipment_id' => $editid]
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

   
    public function detail()
    {
        $default = $this->default();

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
        return view('app.' . $this->obj_info['name'] . '.detail')
            ->with([
                'obj_info'  => $this->obj_info,
                'route' => ['submit'  => $sumit_route, 'cancel' => $cancel_route, 'new' => $new],
                'form' => ['save_type' => 'save'],
                'fprimarykey'     => $this->fprimarykey,
                'caption' => __('dev.new'),
                'isupdate' => false,

            ]);
    } /*../function..*/

   

    
   
}
