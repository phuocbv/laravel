<?php
namespace App\Http\Controllers\Admin;

use App\Email;
use App\Http\Controllers\Controller;
use App\User;
use Carbon\Carbon;
use DTS\eBaySDK\Fulfillment\Types\ShippingFulfillment;
use DTS\eBaySDK\OAuth\Services\OAuthService;
use DTS\eBaySDK\OAuth\Types\GetUserTokenRestRequest;
use DTS\eBaySDK\OAuth\Types\RefreshUserTokenRestRequest;
use Illuminate\Http\Request;


class EmailController extends Controller
{


    public function __construct()
    {
        $this->middleware('auth');
    }

    protected $rules = [
        'name'     => 'required|string|max:255',
        'email'    => 'required|string|email|max:255|unique:users',
    ];

    public function index(){




        $emails = Email::all();
        return view('admin.email.index', compact('emails'));


        $service = new OAuthService([
            'credentials' => [
                'appId'  => 'AliceRus-cshop-PRD-18bb87cdf-4e23ab19',
                'certId' => 'PRD-8bb87cdfe803-6733-4203-abe9-7270',
                'devId'  => '66b30289-3df3-4fe9-8a3c-edfebab13b16',
            ],
            'ruName'      => 'Alice_Russell-AliceRus-cshop--enpfpqt'
        ]);


        /*

        $service = new OAuthService([
            'credentials' => [
                'appId'  => 'TruongLo-cmanager-PRD-c8bb87cdf-2733efc5',
                'certId' => 'PRD-8bb87cdf0ff3-e345-430d-9659-f028',
                'devId'  => '6368ba48-9555-4847-a46f-b3ab9a4c994b',
            ],
            'ruName'      => 'Truong_Loi-TruongLo-cmanag-pzjuhk'
        ]);



        $url =  $service->redirectUrlForUser([
            'state' => 'bar',
            'scope' => [
                'https://api.ebay.com/oauth/api_scope/sell.fulfillment',
                'https://api.ebay.com/oauth/api_scope/sell.fulfillment.readonly'
            ]
        ]);

          dd($url);

          */


        $response = $service->refreshUserToken(new RefreshUserTokenRestRequest([
            'refresh_token' => 'v^1.1#i^1#r^1#p^3#I^3#f^0#t^Ul4xMF8xMToyNkYzRDZCOEFDNDMyQkU1REM5RjU4MjExNDlCMjREMF8yXzEjRV4yNjA=',
            'scope' => [
                'https://api.ebay.com/oauth/api_scope/sell.fulfillment',
                'https://api.ebay.com/oauth/api_scope/sell.fulfillment.readonly'
            ]
        ]));

        printf("\nStatus Code: %s\n\n", $response->getStatusCode());
        if ($response->getStatusCode() !== 200) {
            printf(
                "%s: %s\n\n",
                $response->error,
                $response->error_description
            );
        } else {
            printf(
                "%s\n%s\n%s\n%s\n\n",
                $response->access_token,
                $response->token_type,
                $response->expires_in,
                $response->refresh_token
            );
        }


        dd($response);

        $response = $service->getUserToken(new GetUserTokenRestRequest([
            //'code' => 'v^1.1#i^1#r^1#p^3#I^3#f^0#t^Ul41XzE6OTA4OTU1RTEwOUQzQzI4MzA0NDNEMDNCMzc3NTI2N0VfMV8xI0VeMjYw'
            'code' => 'v^1.1#i^1#r^1#f^0#p^3#I^3#t^Ul41XzA6NTdBMDFCNkY3RDgyMzMwNTA4MkMxNDVENDc1MTVBMUNfMV8xI0VeMjYw'
            //'code' => 'v%5E1.1%23i%5E1%23f%5E0%23I%5E3%23r%5E1%23p%5E3%23t%5EUl41Xzk6MkQ3NDE2NDQ5Rjk4NThCNkVERTc0MjdGMDE3MUNCOTFfMV8xI0VeMjYw'
            //'code' => 'v%5E1.1%23i%5E1%23p%5E3%23I%5E3%23f%5E0%23r%5E1%23t%5EUl41XzU6M0NGNzA3Mzk0QUVCRUJFREI1OEQzN0I5RDAyNkExRkRfMl8xI0VeMjYw',


        ]));

        /*
        $response = $service->getUserToken(new GetUserTokenRestRequest([
            'code' => 'v^1.1%23i^1%23f^0%23r^1%23I^3%23p^3%23t^Ul41XzExOjlDQzNFRUIxRUQ5QjNFQTIyNTg2OUZERUVDNkU0MjVFXzBfMSNFXjI2MA%3D%3D',
            'grant_type' => 'authorization_code'
            //'code' => 'v%5E1.1%23i%5E1%23f%5E0%23I%5E3%23r%5E1%23p%5E3%23t%5EUl41Xzk6MkQ3NDE2NDQ5Rjk4NThCNkVERTc0MjdGMDE3MUNCOTFfMV8xI0VeMjYw'
            //'code' => 'v%5E1.1%23i%5E1%23p%5E3%23I%5E3%23f%5E0%23r%5E1%23t%5EUl41XzU6M0NGNzA3Mzk0QUVCRUJFREI1OEQzN0I5RDAyNkExRkRfMl8xI0VeMjYw',

        ]));

        */

        printf("\nStatus Code: %s\n\n", $response->getStatusCode());
        if ($response->getStatusCode() !== 200) {
            printf(
                "%s: %s\n\n",
                $response->error,
                $response->error_description
            );
        } else {
            printf(
                "%s\n%s\n%s\n%s\n\n",
                $response->access_token,
                $response->token_type,
                $response->expires_in,
                $response->refresh_token
            );
        }

        dd($response);



    }

    public function getCreate(){
        return view('admin.email.create');
    }

    public function getEdit($id){
        $email = Email::find($id);
        return view('admin.email.edit', compact('email'));
    }

    public function postCreate(Request $request){

        if($request->shop_id == 1){
            $rules = [
                'name'     => 'required|string|max:255',
                'email'    => 'required|string|email|max:255',
                'shopify_key'    => 'required|string|max:255',
                'shopify_pass'    => 'required|string|max:255',
                'shopify_hostname'    => 'required|string|max:255',
                'shopify_shared_secret'    => 'required|string|max:255'
            ];
        } else {
            $rules = [
                'name'     => 'required|string|max:255',
                'email'    => 'required|string|email|max:255',
                'ebay_access_token'    => 'required',
                'ebay_refresh_token'    => 'required'
            ];
        }

        $this->validate($request, $rules);

        $mailacc = new Email();
        $mailacc->name = $request->name;
        $mailacc->email = $request->email;
        $mailacc->notes = $request->notes;
        $mailacc->shop_id = $request->shop_id;

        if($request->shop_id == 1){
            $mailacc->shopify_key = $request->shopify_key;
            $mailacc->shopify_pass = $request->shopify_pass;
            $mailacc->shopify_hostname = $request->shopify_hostname;
            $mailacc->shopify_shared_secret = $request->shopify_shared_secret;
        } else {
            $mailacc->ebay_access_token = $request->ebay_access_token;
            $mailacc->ebay_refresh_token = $request->ebay_refresh_token;
            $mailacc->ebay_token_expired = Carbon::now()->addMinutes(90);
        }
        $mailacc->active = 1;
        $mailacc->save();
        return redirect()->route('admin.email')->with('message','Okie');
    }

    public function postEdit(Request $request){


        $mailacc = Email::find($request->email_id);

        if($mailacc->shop_id == 1){
            $rules = [
                'name'     => 'required|string|max:255',
                'email'    => 'required|string|email|max:255',
                'shopify_key'    => 'required|string|max:255',
                'shopify_pass'    => 'required|string|max:255',
                'shopify_hostname'    => 'required|string|max:255',
                'shopify_shared_secret'    => 'required|string|max:255'
            ];
        } else {
            $rules = [
                'name'     => 'required|string|max:255',
                'email'    => 'required|string|email|max:255',
                'ebay_access_token'    => 'required',
                'ebay_refresh_token'    => 'required'
            ];
        }

        $this->validate($request, $rules);

        $mailacc->name = $request->name;
        $mailacc->email = $request->email;
        $mailacc->notes = $request->notes;
        //$mailacc->shop_id = $request->shop_id;

        if($request->shop_id == 1){
            $mailacc->shopify_key = $request->shopify_key;
            $mailacc->shopify_pass = $request->shopify_pass;
            $mailacc->shopify_hostname = $request->shopify_hostname;
            $mailacc->shopify_shared_secret = $request->shopify_shared_secret;
        } else {
            $mailacc->ebay_access_token = $request->ebay_access_token;
            $mailacc->ebay_refresh_token = $request->ebay_refresh_token;
            //$mailacc->ebay_token_expired = Carbon::now()->addMinutes(90);
        }
        $mailacc->save();
        return redirect()->route('admin.email')->with('message','Saved Successfully');
    }


    public function postDelete(Request $request)
    {
        $category = Email::find($request->id);
        $category->delete();
        $message = "Email Account deleted successfully.";
        Session::flash('message', $message);
        return "success";
    }

    public function postGrandCode(){
        $service = new OAuthService([
            'credentials' => [
                'appId'  => env('appId', 'TruongLo-cmanager-PRD-c8bb87cdf-2733efc5'),
                'certId'  => env('certId', 'PRD-8bb87cdf0ff3-e345-430d-9659-f028'),
                'devId'  => env('devId', '6368ba48-9555-4847-a46f-b3ab9a4c994b'),
            ],
             'ruName'  => env('ruName', 'Truong_Loi-TruongLo-cmanag-pzjuhk')
        ]);




        $url =  $service->redirectUrlForUser([
            'state' => 'bar',
            'scope' => [
                'https://api.ebay.com/oauth/api_scope/sell.fulfillment',
                'https://api.ebay.com/oauth/api_scope/sell.fulfillment.readonly'
            ]
        ]);

        $data['status'] = 'success';
        $data['url'] = $url;

        return $data;

    }

    public function postUserToken(Request $request){
        $service = new OAuthService([
            'credentials' => [
                'appId'  => env('appId', 'TruongLo-cmanager-PRD-c8bb87cdf-2733efc5'),
                'certId'  => env('certId', 'PRD-8bb87cdf0ff3-e345-430d-9659-f028'),
                'devId'  => env('devId', '6368ba48-9555-4847-a46f-b3ab9a4c994b'),
            ],
            'ruName'  => env('ruName', 'Truong_Loi-TruongLo-cmanag-pzjuhk')
        ]);


        $grand_code = urldecode(urldecode($request->grand_code));

        $response = $service->getUserToken(new GetUserTokenRestRequest([
            'code' => $grand_code
            //'code' => 'v%5E1.1%23i%5E1%23f%5E0%23I%5E3%23r%5E1%23p%5E3%23t%5EUl41Xzk6MkQ3NDE2NDQ5Rjk4NThCNkVERTc0MjdGMDE3MUNCOTFfMV8xI0VeMjYw'
        ]));

        if ($response->getStatusCode() !== 200) {

            $data['status'] = $response->error;
            $data['msg'] = $response->error_description;

        } else {
            $data['status'] = 'success';
            $data['access_token'] = $response->access_token;
            $data['refresh_token'] = $response->refresh_token;
        }


        return $data;

    }


}