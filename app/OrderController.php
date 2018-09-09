<?php
namespace App\Http\Controllers\Admin;

use App\Email;
use App\Http\Controllers\Controller;
use App\LineItem;
use App\Order;
use App\User;
use Carbon\Carbon;
use DTS\eBaySDK\Fulfillment\Services\FulfillmentService;
use DTS\eBaySDK\Fulfillment\Types\GetOrdersRestRequest;
use DTS\eBaySDK\Fulfillment\Types\GetShippingFulfillmentsRestRequest;
use DTS\eBaySDK\Fulfillment\Types\ShippingFulfillment;
use DTS\eBaySDK\OAuth\Services\OAuthService;
use DTS\eBaySDK\OAuth\Types\GetUserTokenRestRequest;
use DTS\eBaySDK\OAuth\Types\RefreshUserTokenRestRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use PHPShopify\ShopifySDK;


class OrderController extends Controller
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

        $user = Auth::user();

        $mailacc = Input::get('mailacc');
        $user_id = Input::get('user_id');
        $buyer_name = Input::get('buyer_name');
        $address = Input::get('address');
        $order_status = Input::get('status');
        $start = Input::get('start_date');

        $ids = explode(',', Input::get('ids'));
        if ($start != '') {
            $start_date = Carbon::createFromFormat('d/m/Y H:i:s', $start . ' 00:00:01');
        }
        $end = Input::get('end_date');
        if ($end != '') {
            $end_date = Carbon::createFromFormat('d/m/Y H:i:s', $end . ' 23:59:59');
        }

        if ($user->group_id == 1) {

            if ($mailacc != '') {
                $query = Order::query();
                if ($mailacc > 0) {
                    $query->where('mailacc', $mailacc);
                }
                if ($user_id > 0) {
                    $query->where('user_id', $user_id);
                }
                if ($buyer_name != '') {
                    $query->where('buyer_name', 'like', '%' . $buyer_name . '%');
                }
                if ($address != '') {
                    $query->where('buyer_address', 'like', '%' . $address . '%');
                }
                if ($order_status > 0) {
                    $query->where('order_status', $order_status);
                }
                if ($start != '') {
                    $query->where('order_at', '>', $start_date);
                }
                if ($end != '') {
                    $query->where('order_at', '<', $end_date);
                }
                if($ids[0] != ""){
                    $query->whereIn('id', $ids);
                }
                $orders_full = $query->orderBy('order_at', 'desc')->with('email', 'user', 'lineitems');
            } else {
                $orders_full = Order::orderBy('order_at', 'desc')->with('email', 'user', 'lineitems');
            }
            //$orders_np = $orders_full->get();
            $orders = $orders_full->paginate(20);
            $orders->setPath( route('admin.order.index'));

            $members = User::where('group_id', 3)->orWhere('group_id', 1)->get();
            $emails = Email::where('active', 1)->get();
            return view('admin.order.admin', compact('orders', 'members', 'emails'));
        }


        $emails = Email::all();
        $orders = Order::with('lineitems')->orderBy('created_at', 'desc')->get();

        return view('admin.order.admin', compact('orders', 'emails'));

    }


    public function destroy(){

        $id = request('id');
        $order = Order::find($id);
        $order->delete();
        $message = "Order deleted successfully.";
        Session::flash('message', $message);
        return "success";
    }

    public function loadOrders(){


        $shopify_accs = Email::where('shop_id', 1)->where('active', 1)->get();
        foreach ($shopify_accs as $shopify_acc){
            $this->loadShopifyOrders($shopify_acc);
        }

        $ebay_accs = Email::where('shop_id', 2)->where('active', 1)->get();
        foreach ($ebay_accs as $ebay_acc){
            $this->loadEbayOrder($ebay_acc);
        }





        Session::flash('message', 'Load orders completed! ');
        return "success";
    }

    public function loadEbayOrder(Email $mailacc){
        $updateToken = $this->updateUserToken($mailacc);
        if($updateToken){
            $mailacc = Email::find($mailacc->id);
        }

        $service = new FulfillmentService([
            'authorization' => $mailacc->ebay_access_token
        ]);
        $request = new GetOrdersRestRequest();

        $response = $service->getOrders($request);

        if ($response->getStatusCode() !== 200) {
            dd( $response->error.': '.$response->error_description);
        } else {
            foreach ($response->orders as $ebayOrder){
                $order_shop_order_id = $ebayOrder->orderId;

                $checkOrderExit = Order::where('shop_order_id', $order_shop_order_id)->first();

                if(!$checkOrderExit){
                    $order_maillacc = $mailacc->id;
                    $order_shop_id = $mailacc->shop_id;
                    $ebay_contact = $ebayOrder->fulfillmentStartInstructions[0]->shippingStep->shipTo;
                    $order_buyer_name = $ebay_contact->fullName;
                    $order_buyer_username = $ebayOrder->buyer->username;
                    if($ebay_contact->contactAddress->addressLine2 != null){
                        $address_line = $ebay_contact->contactAddress->addressLine1."\r\n".$ebay_contact->contactAddress->addressLine2;
                    } else {
                        $address_line = $ebay_contact->contactAddress->addressLine1;
                    }

                    $order_buyer_address =
                        $order_buyer_name ."\r\n"
                        .$address_line."\r\n"
                        .$ebay_contact->contactAddress->city."\r\n"
                        .$ebay_contact->contactAddress->stateOrProvince." ".$ebay_contact->contactAddress->postalCode." ".$ebay_contact->contactAddress->countryCode."\r\n"
                        .$ebay_contact->primaryPhone->phoneNumber;
                    $order_payment_status = $ebayOrder->orderPaymentStatus;

                    $order_fulfillment_status = $ebayOrder->orderFulfillmentStatus;


                    $ful_request = new GetShippingFulfillmentsRestRequest();
                    $ful_request->orderId = $order_shop_order_id;
                    $fuls_respone = $service->getShippingFulfillments($ful_request);

                    $order_fulfillments = [];
                    foreach ($fuls_respone->fulfillments as $fulfillment){
                        $ful['id'] = $fulfillment->fulfillmentId;
                        $ful['tracking_number'] = $fulfillment->shipmentTrackingNumber;
                        $ful['line_item_id'] = $fulfillment->lineItems[0]->lineItemId;
                        $ful['quantity'] = $fulfillment->lineItems[0]->quantity;
                        $order_fulfillments[] = $ful;
                    }



                    $order = new Order();
                    $order->mailacc = $order_maillacc;
                    $order->shop_id = $order_shop_id;
                    $order->buyer_name = $order_buyer_name;
                    $order->buyer_username = $order_buyer_username;
                    $order->buyer_address = $order_buyer_address;
                    $order->shop_payment_status = $order_payment_status;
                    $order->shop_order_id = $order_shop_order_id;
                    $order->fulfillment_status = $order_fulfillment_status;
                    $order->fulfillments =  $order_fulfillments;
                    $order->user_id = 1;
                    $order->order_at = Carbon::createFromTimeString($ebayOrder->creationDate);
                    $order->save();

                    foreach ($ebayOrder->lineItems as $lineItem){
                        $line = new LineItem();
                        $line->order_id = $order->id;
                        $line->item_shop_id = $lineItem->lineItemId;
                        $line->title = $lineItem->title;
                        $line->quantity = $lineItem->quantity;
                        $line->price = $lineItem->lineItemCost->value;
                        $line->fulfillment_status = $lineItem->lineItemFulfillmentStatus;
                        $line->save();
                    }

                }



            }

        }
    }

    public function updateUserToken(Email $mailacc){
        $now = Carbon::now();
        if($now > $mailacc->ebay_token_expired){
            $service = new OAuthService([
                'credentials' => [
                    'appId'  => env('appId', 'TruongLo-cmanager-PRD-c8bb87cdf-2733efc5'),
                    'certId'  => env('certId', 'PRD-8bb87cdf0ff3-e345-430d-9659-f028'),
                    'devId'  => env('devId', '6368ba48-9555-4847-a46f-b3ab9a4c994b'),
                ],
                'ruName'  => env('ruName', 'Truong_Loi-TruongLo-cmanag-pzjuhk')
            ]);

            $response = $service->refreshUserToken(new RefreshUserTokenRestRequest([
                'refresh_token' => $mailacc->ebay_refresh_token,
                'scope' => [
                    'https://api.ebay.com/oauth/api_scope/sell.fulfillment',
                    'https://api.ebay.com/oauth/api_scope/sell.fulfillment.readonly'
                ]
            ]));

            if ($response->getStatusCode() !== 200) {
                dd( $response->error.': '.$response->error_description);
            } else {
                $mailacc->ebay_access_token = $response->access_token;
                $mailacc->ebay_token_expired = Carbon::now()->addMinutes(90);
                $mailacc->save();
                return true;
            }
        } else {
            return false;
        }
    }

    public function loadShopifyOrders(Email $mailacc){
        $config = array(
            'ShopUrl' => $mailacc->shopify_hostname,
            'ApiKey' => $mailacc->shopify_key,
            'Password' => $mailacc->shopify_pass
        );

        $shopify = new ShopifySDK($config);

        $last7day = Carbon::now()->subDays(7)->format('Y-m-d\TH:i:sP');
        $shopify_orders = $shopify->Order->get([
            'financial_status' => 'paid',
            'status' => 'any',
            'created_at_min' => $last7day
        ]);



        foreach ($shopify_orders as $orderShopify){

            $order_shop_order_id = $orderShopify['id'];
            $checkOrderExit = Order::where('shop_order_id', $order_shop_order_id)->first();
            if(!$checkOrderExit){

                $order_maillacc = $mailacc->id;
                $order_shop_id = $mailacc->shop_id;
                $order_buyer_name = $orderShopify['customer']['first_name'].' '.$orderShopify['customer']['last_name'];
                $order_buyer_email = $orderShopify['email'];
                if($orderShopify['shipping_address']['address2'] != ""){
                    $address_line = $orderShopify['shipping_address']['address1']."\r\n".$orderShopify['shipping_address']['address2'];
                } else {
                    $address_line = $orderShopify['shipping_address']['address1'];
                }

                $order_buyer_address =
                    $orderShopify['shipping_address']['name'] ."\r\n"
                    .$address_line."\r\n"
                    .$orderShopify['shipping_address']['city']."\r\n"
                    .$orderShopify['shipping_address']['province']." ".$orderShopify['shipping_address']['zip']." ".$orderShopify['shipping_address']['country_code']."\r\n"
                    .$orderShopify['shipping_address']['phone'];


                $order_shop_payment_status  = $orderShopify['financial_status'];

                $order_fulfillments = [];
                foreach ($orderShopify['fulfillments'] as $fulfillment){
                    $ful['id'] = $fulfillment['id'];
                    $ful['tracking_number'] = $fulfillment['tracking_number'];
                    $ful['line_item_id'] = $fulfillment['line_items'][0]['id'];
                    $ful['quantity'] = $fulfillment['line_items'][0]['quantity'];
                    $order_fulfillments[] = $ful;
                }

                $order_fulfillment_status = $orderShopify['fulfillment_status'];
                $order_shop_name = $orderShopify['name'];

                $order = new Order();
                $order->mailacc = $order_maillacc;
                $order->shop_id = $order_shop_id;
                $order->shop_name = $order_shop_name;
                $order->buyer_name = $order_buyer_name;
                $order->buyer_email = $order_buyer_email;
                $order->buyer_address = $order_buyer_address;
                $order->shop_payment_status  = $order_shop_payment_status;
                $order->shop_order_id = $order_shop_order_id;
                $order->fulfillments =  $order_fulfillments;
                $order->fulfillment_status = $order_fulfillment_status;
                $order->user_id = 1;
                $order->order_at = Carbon::createFromTimeString($orderShopify['created_at']);
                $order->save();

                foreach ($orderShopify['line_items'] as $lineItem){
                    $line = new LineItem();
                    $line->order_id = $order->id;
                    $line->item_shop_id = $lineItem['id'];
                    $line->title = $lineItem['title'];
                    $line->quantity = $lineItem['quantity'];
                    $line->price = $lineItem['price'];
                    $line->fulfillment_status = $lineItem['fulfillment_status'];
                    $line->fulfillable_quantity = $lineItem['fulfillable_quantity'];

                    $line->save();
                }
            }
        }
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

    public function changeTrack(Request $request)
    {
        $lineItem = LineItem::find($request->pk);
        $lineItem->tracking = trim($request->value);
        $lineItem->save();

        $data = $this->checkTrackSrv($lineItem->tracking);
        $lineItem->tracking_status = $data;
        $lineItem->save();


        $company_name = get_tracking_company($lineItem->tracking);
        if($company_name != "Other"){
            $order = Order::find($lineItem->order_id);
            if($order->shop_id == 1){
                if(strtolower($lineItem->fulfillment_status) != 'fulfilled' && $lineItem->quantity == 1){
                    $this->createShopifyFulfill($lineItem);
                    $this->updateOrderShopifyInfo($order);
                }elseif(strtolower($lineItem->fulfillment_status) == 'fulfilled' && $lineItem->quantity == 1){
                    $this->updateShopifyFulfill($lineItem);
                    $this->updateOrderShopifyInfo($order);
                }
            }
        }
        $data['tracking'] = $lineItem->tracking;
        return $data;
    }

    public function addTrackingWithQty(Request $request){
        $lineItem = LineItem::find($request->lineid);
        $tracking = trim($request->tracking);
        $quantity = $request->qty;
        $company_name = get_tracking_company($tracking);
        if($company_name != "Other"){
            $order = Order::find($lineItem->order_id);
            if($order->shop_id == 1){
                if(strtolower($lineItem->fulfillment_status) != 'fulfilled' && $lineItem->quantity > 1 && $quantity <= $lineItem->fulfillable_quantity ){
                    $this->createShopifyFulfill($lineItem, $quantity);
                    $this->updateOrderShopifyInfo($order);
                }
            }
        }

        $data['msg'] = 'success';
        return $data;
    }

    public function createShopifyFulfill($lineItem, $qty = 1){
        $order = Order::find($lineItem->order_id);
        $mailacc = Email::find($order->mailacc);
        $config = array(
            'ShopUrl' => $mailacc->shopify_hostname,
            'ApiKey' => $mailacc->shopify_key,
            'Password' => $mailacc->shopify_pass
        );

        $shopify = new ShopifySDK($config);

        $location_id = Cache::remember('location_id_'.$mailacc->id, 10080, function() use ($shopify){
            $locations = $shopify->Location->get();
            return $locations[0]['id'];
        });

        $company_name = get_tracking_company($lineItem->tracking);
        if($company_name == "Ontrac"){
            $fulfill = array(
                "location_id" => $location_id,
                "tracking_number" => $lineItem->tracking,
                "tracking_company" => "Other",
                "tracking_url" => "https://www.ontrac.com/trackingresults.asp?tracking_number=".$lineItem->tracking,
                "line_items" => [
                    [
                        "id" => $lineItem->item_shop_id,
                        "quantity" => $qty
                    ]
                ],
                "notify_customer" => false
            );
        } else {
            $fulfill = array(
                "location_id" => $location_id,
                "tracking_number" => $lineItem->tracking,
                "tracking_company" => $company_name,
                "line_items" => [
                    [
                        "id" => $lineItem->item_shop_id,
                        "quantity" => $qty
                    ]
                ],
                "notify_customer" => false
            );
        }

        $data = $shopify->Order($order->shop_order_id)->Fulfillment->post($fulfill);

        return $data;
    }

    public function updateShopifyFulfill($lineItem, $fulfill_id = null){
        $order = Order::find($lineItem->order_id);
        $mailacc = Email::find($order->mailacc);
        $config = array(
            'ShopUrl' => $mailacc->shopify_hostname,
            'ApiKey' => $mailacc->shopify_key,
            'Password' => $mailacc->shopify_pass
        );

        $shopify = new ShopifySDK($config);

        if($fulfill_id == null){
            $fulfill_id = $order->fulfillments[0]['id'];
        }
        $company_name = get_tracking_company($lineItem->tracking);
        if($company_name == "Ontrac"){
            $fulfill = array(
                "tracking_number" => $lineItem->tracking,
                "tracking_company" => "Other",
                "tracking_url" => "https://www.ontrac.com/trackingresults.asp?tracking_number=".$lineItem->tracking,
                "id" => $fulfill_id,
                "notify_customer" => false
            );
        } else {
            $fulfill = array(
                "tracking_number" => $lineItem->tracking,
                "tracking_company" => $company_name,
                "id" => $fulfill_id,
                "notify_customer" => false
            );
        }

        $data = $shopify->Order($order->shop_order_id)->Fulfillment($fulfill_id)->put($fulfill);

        return $data;
    }

    public function checkOneTrack(Request $request)
    {
        $lineItem = LineItem::find($request->id);
        $tracking = trim($lineItem->tracking);
        $data = $this->checkTrackSrv($tracking);
        $lineItem->tracking_status = $data;
        $lineItem->save();
        return $data;
    }

    public function checkTrackSession(Request $request)
    {
        Session::put('list_id', $request->line_ids);
        Session::put('list_tracking', $request->line_trackings);
        Session::put('total_track', count($request->line_ids));
        $data['status'] = 1;
        return $data;
    }

    public function checkTrackCron()
    {
        $list_id = session('list_id');
        $list_tracking = session('list_tracking');
        if (sizeof($list_id) == 0) {
            $data['jobsleft'] = 0;
        } else {
            $line_id = array_shift($list_id);
            $line_tracking = array_shift($list_tracking);
            $data = $this->checkCronTrack($line_id, $line_tracking);
            Session::put('list_id', $list_id);
            Session::put('list_tracking', $list_tracking);
            $data['curent_track_id'] = $line_id;
            $data['next_track_id'] = array_shift($list_id);
        }

        return $data;
    }

    public function checkCronTrack($order_id, $order_tracking)
    {
        $lineItem = LineItem::find($order_id);
        $tracking = $order_tracking;

        $data = $this->checkTrackSrv($tracking);

        $lineItem->tracking_status = $data;
        $lineItem->save();
        return $data;

    }

    public function checkTrackSrv($tracking)
    {

        $url = get_tracking_url($tracking);


        $des = "None";
        $status = "None";

        //UPS.com
        $check_ups = strpos($url, "ups.com");
        if($check_ups != false){
            $data_html = file_get_contents($url);
            $html = str_get_html($data_html);
            $track_process = $html->find("a[id=tt_spStatus]");
            if(sizeof($track_process) > 0){
                $status = trim($track_process[0]->plaintext);
                $des = trim($track_process[0]->plaintext);
            }
        }


        //Fedex
        $check_fedex = strpos($url, "fedex.com");
        if($check_fedex != false){

            $cf_post['action'] = 'trackpackages';
            $cf_post['format'] = 'json';
            $cf_post['locale'] = 'en_US';
            $cf_post['version'] = 1;
            $cf_post['data'] = '{"TrackPackagesRequest":{"appType":"WTRK","appDeviceType":"DESKTOP","uniqueKey":"","processingParameters"
:{},"trackingInfoList":[{"trackNumberInfo":{"trackingNumber":"'.$tracking.'","trackingQualifier":"","trackingCarrier"
:""}}]}}';


            $data_fd = postPage('https://www.fedex.com/trackingCal/track', $cf_post);
            $data_json = json_decode($data_fd, true);
            $tracking = $data_json['TrackPackagesResponse'];

            if($data_json['TrackPackagesResponse']['successful'] == true){
                $status = $tracking['packageList']['0']['keyStatusCD'];
                $des = $tracking['packageList']['0']['keyStatus'];
            }

        }


        if (stripos($des, 'return') != false) {
            $status = 'RT';
            $des = 'RETURNED';
            $order = Order::where('tracking', $tracking)->first();
            $order->order_status = 1;
            $order->save();
        }
        if ($status == 'Delivered') {
            $status = 'DL';
        }

        $data['status'] = $status;
        $data['des'] = $des;
        return $data;

    }

    public function changeNotes(Request $request){
        $user = Auth::user();
        $order = Order::find($request->pk);
        if (($user->group_id == 1) || ($user->group_id == 2) || ($order->user_id == $user->id)) {
            $order->notes = $request->value;
            $order->save();
        }
    }

    public function changeStatus(Request $request)
    {
        $user = Auth::user();
        $order = Order::find($request->pk);
        if (($user->group_id < 3) || ($order->user_id == $user->id)) {
            $order->order_status = $request->value;
            $order->save();
        }

    }

    public function assignOrder(Request $request)
    {
        $order = Order::find($request->pk);
        $order->user_id = $request->value;
        $order->save();
    }

    public function assignAll(Request $request)
    {
        $idArray = $request->ar_id;
        $user_id = $request->assign_member_id;
        foreach ($idArray as $id) {
            $order = Order::find($id);
            $order->user_id = $user_id;
            $order->save();
            $message = "Okie, Assign Done";

        }
        return redirect(route('admin.order.index'))->with('message', $message);
    }

    public function updateOrderShopifyInfo($order){

        $order = Order::find($order->id);

        $mailacc = Email::find($order->mailacc);
        $config = array(
            'ShopUrl' => $mailacc->shopify_hostname,
            'ApiKey' => $mailacc->shopify_key,
            'Password' => $mailacc->shopify_pass
        );

        $shopify = new ShopifySDK($config);

        $orderShopify = $shopify->Order($order->shop_order_id)->get();

        $order->shop_payment_status  = $orderShopify['financial_status'];
        $order->fulfillment_status = $orderShopify['fulfillment_status'];


        $order_fulfillments = [];
        foreach ($orderShopify['fulfillments'] as $fulfillment){
            $ful['id'] = $fulfillment['id'];
            $ful['tracking_number'] = $fulfillment['tracking_number'];
            $ful['line_item_id'] = $fulfillment['line_items'][0]['id'];
            $ful['quantity'] = $fulfillment['line_items'][0]['quantity'];
            $order_fulfillments[] = $ful;
        }

        $order->fulfillments =  $order_fulfillments;
        $order->save();


        foreach ($orderShopify['line_items'] as $lineItem){
            $line = LineItem::where('item_shop_id', $lineItem['id'])->first();
            if($line){
                $line->fulfillment_status = $lineItem['fulfillment_status'];
                $line->fulfillable_quantity = $lineItem['fulfillable_quantity'];
                $line->save();
            }
        }
    }


}