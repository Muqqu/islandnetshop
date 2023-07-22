<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\Admin\Addon\WalletInterface;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    protected $wallet;

    public function __construct(WalletInterface $wallet){
        $this->wallet = $wallet;
    }

    public function walletRechargeRequests(Request $request){
        try {
            $wallet_recharge_requests = $this->wallet->paginate(get_pagination('pagination'), 'wallet_recharge', $request);
            return view('admin.wallet.wallet-recharge-requests', compact('wallet_recharge_requests'));
        }catch (\Exception $e) {
            Toastr::error('Something went wrong, please try again.');
            return redirect()->back();
        }
    }

    public function walletApproveRequest($id){

        if ($this->wallet->walletApproveRequest($id)):
            $response['message'] = __('Approved Successfully');
            $response['status']  = 'success';
            $response['title']   = __('Success');
            return response()->json($response);
        else:
            $response['message'] = __('Something went wrong, please try again');
            $response['status']  = 'error';
            $response['title']   = __('Ops..!');
            return response()->json($response);
        endif;
    }
    public function walletRejectRequest($id){

        if ($this->wallet->walletRejectRequest($id)):
            $response['message'] = __('Reject Successfully');
            $response['status']  = 'success';
            $response['title']   = __('Success');
            return response()->json($response);
        else:
            $response['message'] = __('Something went wrong, please try again');
            $response['status']  = 'error';
            $response['title']   = __('Ops..!');
            return response()->json($response);
        endif;
    }


}
