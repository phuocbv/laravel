<?php
namespace App\Http\Controllers\Admin;

use App\Email;
use App\Http\Controllers\Controller;
use App\LineItem;
use App\Order;
use App\Setting;
use App\User;
use Carbon\Carbon;
use DTS\eBaySDK\Fulfillment\Services\FulfillmentService;
use DTS\eBaySDK\Fulfillment\Types\GetOrdersRestRequest;
use DTS\eBaySDK\OAuth\Services\OAuthService;
use DTS\eBaySDK\OAuth\Types\RefreshUserTokenRestRequest;
use GuzzleHttp\Client;
use Google_Client;
use Google_Service_Sheets;
use Google_Service_Sheets_ValueRange;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use PHPShopify\ShopifySDK;


define('STDIN',fopen("php://stdin","r"));

class DashboardController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){

        $user = Auth::user();
        if($user->active == 0){
            return redirect('/home');
        }else {
            return view('admin.index');
        }


        $client = $this->getClient();
        $service = new Google_Service_Sheets($client);

        // Prints the names and majors of students in a sample spreadsheet:
        // https://docs.google.com/spreadsheets/d/1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms/edit
        $spreadsheetId = '1KRcg0BROUZI7xk_ACvOvBuevYCTuHZ5W4INi8-Shww0';
        //$spreadsheetId = '1X1_S-OxibeTaN8TDfReaA37acgfYl_JO9-Gl_nLRyyg';
        $range = 'ChanDoi!A1:K1';


        $values = [
            ["wait", "", "Address here 2", "Item Name here 2", "", "", "", "", ""],
        ];
        $body = new Google_Service_Sheets_ValueRange([
            'values' => $values
        ]);
        $params = [
            'valueInputOption' => 'RAW'
        ];
        $result = $service->spreadsheets_values->append($spreadsheetId, $range,
            $body, $params);

        dd($result->getUpdates()->getUpdatedRange());




        $client = $this->getClient();
        $service = new Google_Service_Sheets($client);

        $spreadsheetId = '1KRcg0BROUZI7xk_ACvOvBuevYCTuHZ5W4INi8-Shww0';
        $range = 'A1:I12';
        $response = $service->spreadsheets_values->get($spreadsheetId, $range);
        $values = $response->getValues();

        if (empty($values)) {
            print "No data found.\n";
        } else {
            print "Name, Major:\n";
            foreach ($values as $row) {
                // Print columns A and E, which correspond to indices 0 and 4.
                printf("%s, %s\n", $row[0], $row[4]);
            }
        }

        $user = Auth::user();
        if($user->active == 0){
            return redirect('/home');
        }else {
            return view('admin.index');
        }




        return view('admin.index');
    }

    public function getSetting(){
        $configs = Setting::find(1)->configs;
        return view('admin.setting', compact('configs'));
    }

    public function postSetting(Request $request){
        $rulers = [
            'sheetid'     => 'required',
            'sheetname'    => 'required',
        ];
        $this->validate($request, $rulers);
        $configs['sheetid'] = $request->sheetid;
        $configs['sheetname'] = $request->sheetname;

        $setting = Setting::find(1);
        $setting->configs = $configs;
        $setting->save();
        Cache::forget('site_setting');

        return redirect()->route('admin.getsetting')->with('message','Okie');
    }

    public function getFromEbay(){
        $mailacc = Email::find(6);
        $updateToken = $this->updateUserToken($mailacc);
        if($updateToken){
            $mailacc = Email::find(6);
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
                    $order_buyer_address =
                        $order_buyer_name ."\r\n"
                        .$ebay_contact->contactAddress->addressLine1."\r\n"
                        .$ebay_contact->contactAddress->city."\r\n"
                        .$ebay_contact->contactAddress->stateOrProvince." ".$ebay_contact->contactAddress->postalCode." ".$ebay_contact->contactAddress->countryCode."\r\n"
                        .$ebay_contact->primaryPhone->phoneNumber;

                    $ebay_item = $ebayOrder->lineItems[0];
                    $order_item_name = $ebay_item->title;
                    $order_price = $ebay_item->lineItemCost->value;
                    $order_qty = $ebay_item->quantity;
                    $order_shop_order_status = $ebayOrder->orderFulfillmentStatus;
                    $order_shop_payment_status = $ebayOrder->orderPaymentStatus;


                    $order = new Order();
                    $order->mailacc = $order_maillacc;
                    $order->shop_id = $order_shop_id;
                    $order->buyer_name = $order_buyer_name;
                    $order->buyer_username = $order_buyer_username;
                    $order->item_name = $order_item_name;
                    $order->buyer_address = $order_buyer_address;
                    $order->price  = $order_price;
                    $order->qty = $order_qty;
                    $order->shop_order_id = $order_shop_order_id;
                    $order->shop_order_status = $order_shop_order_status;
                    $order->shop_payment_status = $order_shop_payment_status;
                    $order->user_id = 1;
                    $order->created_at = Carbon::createFromTimeString($ebayOrder->creationDate);

                    $order->save();
                }



            }


            dd(json_decode($response));
        }
    }
    public function updateUserToken($mailacc){
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

    function getClient()
    {
        $client = new Google_Client();
        $client->setApplicationName('CManager');

        $client->setScopes([Google_Service_Sheets::DRIVE, Google_Service_Sheets::DRIVE_FILE, Google_Service_Sheets::DRIVE_READONLY, Google_Service_Sheets::SPREADSHEETS, Google_Service_Sheets::SPREADSHEETS_READONLY ]);


        $client->setAuthConfig(storage_path().'/app/gg/client_secret_nek.json');
        $client->setAccessType('offline');
        $client->setApprovalPrompt('force');




        // Load previously authorized credentials from a file.
        $credentialsPath = $this->expandHomeDirectory('credentials.json');
        if (file_exists($credentialsPath)) {
            $accessToken = json_decode(file_get_contents($credentialsPath), true);
        } else {
            // Request authorization from the user.
           //$authUrl = $client->createAuthUrl();
            //printf("Open the following link in your browser:\n%s\n", $authUrl);

            //dd($authUrl);
            //print 'Enter verification code: ';
            //$authCode = trim(fgets(STDIN));
            $authCode = '4/AAAARkjEYy5_OejIz_l3lPoa5j_EByEGeKoJRwbr2iqwNzBhDQ_KB68';
            // Exchange authorization code for an access token.
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

            // Store the credentials to disk.
            if (!file_exists(dirname($credentialsPath))) {
                mkdir(dirname($credentialsPath), 0700, true);
            }
            file_put_contents($credentialsPath, json_encode($accessToken));
            printf("Credentials saved to %s\n", $credentialsPath);
        }
        $client->setAccessToken($accessToken);

        // Refresh the token if it's expired.
        if ($client->isAccessTokenExpired()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            file_put_contents($credentialsPath, json_encode($client->getAccessToken()));
        }
        return $client;


    }

    function expandHomeDirectory($path)
    {
        $homeDirectory = getenv('HOME');
        if (empty($homeDirectory)) {
            $homeDirectory = getenv('HOMEDRIVE') . getenv('HOMEPATH');
        }
        return str_replace('~', realpath($homeDirectory), $path);
    }


    public function gg(){




        /**
         * Expands the home directory alias '~' to the full path.
         * @param string $path the path to expand.
         * @return string the expanded path.
         */


        // Get the API client and construct the service object.
        $client = getClient();
        $service = new Google_Service_Sheets($client);

        // Prints the names and majors of students in a sample spreadsheet:
        // https://docs.google.com/spreadsheets/d/1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms/edit
        $spreadsheetId = '1Wrj0asYErybQG5DiyWd0iy6JLheTkG-ZtGgInbAnBiI';
        $range = 'A1:E2';
        $response = $service->spreadsheets_values->get($spreadsheetId, $range);
        $values = $response->getValues();

        if (empty($values)) {
            print "No data found.\n";
        } else {
            print "Name, Major:\n";
            foreach ($values as $row) {
                // Print columns A and E, which correspond to indices 0 and 4.
                printf("%s, %s\n", $row[0], $row[4]);
            }
        }


        $values = [
            [
                "botay.com"
            ],
        ];
        $body = new Google_Service_Sheets_ValueRange([
            'values' => $values
        ]);
        $params = [
            'valueInputOption' => 'RAW'
        ];
        $result = $service->spreadsheets_values->update($spreadsheetId, 'A3',
            $body, $params);
        printf("%d cells updated.", $result->getUpdatedCells());


        echo '<pre>', var_export($result, true), '</pre>', "\n";

        dd('here');
    }

    public function postActive(Request $request)
    {
        $table = $request->table;
        $field = $request->field;
        $id = $request->id;
        $status = $request->status;
        if ($status == 0) {
            $pub = 1;
        } else {
            $pub = 0;
        }

        DB::table($table)->where('id', $id)->update([$field => $pub]);

        $data["published"] = icon_active("'$table'", "'$field'", $id, $pub);
        return json_encode($data);
    }

    public function postPaid(Request $request)
    {
        $user = Auth::user();
        if($user->group_id == 1){
            $table = $request->table;
            $field = $request->field;
            $id = $request->id;
            $status = $request->status;
            if ($status == 0) {
                $pub = 1;
            } else {
                $pub = 0;
            }

            DB::table($table)->where('id', $id)->update([$field => $pub]);

            $data["paid"] = icon_payment("'$table'", "'$field'", $id, $pub);
        }else {
            $data["paid"] = 'you dont have permision';
        }


        return json_encode($data);
    }

}