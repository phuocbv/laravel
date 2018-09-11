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
use DTS\eBaySDK\Fulfillment\Types\ShippingFulfillment;
use DTS\eBaySDK\OAuth\Services\OAuthService;
use DTS\eBaySDK\OAuth\Types\GetUserTokenRestRequest;
use DTS\eBaySDK\OAuth\Types\RefreshUserTokenRestRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use PHPShopify\ShopifySDK;


class LineItemController extends Controller
{


    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getLineitemById(Request $request)
    {
        $data = $request->only('lineItem_id');
        $lineItem = LineItem::find($data['lineItem_id']);
        return response()->json([
            'status' => 'success',
            'data' => $lineItem
        ]);
    }

    public function updateLineItem(Request $request) {
        $data = $request->only('price', 'quantity', 'lineItemId');
        $lineItem = LineItem::find($data['lineItemId']);
        $lineItem->price = $data['price'];
        $lineItem->quantity = $data['quantity'];
        $lineItem->save();

        return response()->json([
            'status' => 'success',
            'data' => $lineItem
        ]);
    }
}