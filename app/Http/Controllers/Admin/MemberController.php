<?php
namespace App\Http\Controllers\Admin;

use App\Email;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

define('STDIN',fopen("php://stdin","r"));

class MemberController extends Controller
{


    public function __construct()
    {
        $this->middleware('auth');
    }

    protected $rules = [
        'name'     => 'required|string|max:255',
        'username' => 'required|string|max:255',
        'email'    => 'required|string|email|max:255',
    ];

    public function index(){

        $members = User::all();
        return view('admin.member.index', compact('members'));
    }

    public function getCreate(){
        $emails = Email::where('active',1)->get();
        return view('admin.member.create', compact('emails'));
    }

    public function getEdit($id){
        $user = User::find($id);
        $emails = Email::where('active',1)->get();
        return view('admin.member.edit', compact('user', 'emails'));
    }

    public function postCreate(Request $request){


        $this->validate($request, $this->rules);
        $user = new User();
        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->notes = $request->notes;
        $user->group_id = $request->group_id;

        if($request->group_id == 2){
            $user->role = $request->role;
        }
        $user->save();
        return redirect()->route('admin.member')->with('message','Okie');
    }

    public function update(Request $request){
        $this->validate($request, $this->rules);

        $user = User::find($request->id);
        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        if($request->password != ''){
            $user->password = bcrypt($request->password);
        }

        $user->notes = $request->notes;
        if($user->group_id > 1){
            $user->group_id = $request->group_id;
            if($request->group_id == 2){
                $user->role = $request->role;
            }
        }

        $user->save();
        return redirect()->route('admin.member')->with('message','User Saved Successfully');
    }

}