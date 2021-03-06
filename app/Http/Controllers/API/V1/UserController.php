<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use App\Services\UserService;
use App\Http\Controllers\Controller;
use App\Http\Requests\DeviceTokenRequest;

class UserController extends Controller
{

    /**
    * @var UserService
    */
    protected $userService;

   /**
    * UserController constructor
    *
    * @param UserService $userService
    */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }


    /**
     * Register user
     *
     * @param Request $request
     * @return mixed
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $data = $request->all();
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
        ]);

        Mail::send('emails.welcome', $data, function($message) use ($data) {
            $message->from('no-reply@site.com', "Site name");
            $message->subject("Welcome to site name");
            $message->to($data['email']);
        });

        return $user;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Saving device token
     *
     * @param DeviceTokenRequest $request
     * @return \Illuminate\Http\Response
     */
    public function saveDeviceToken(DeviceTokenRequest $request)
    {
        return $this->userService->saveDeviceToken($request);
    }
}
