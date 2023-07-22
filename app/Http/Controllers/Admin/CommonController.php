<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\ProductImport;
use App\Models\Brand;
use App\Models\Category;
use App\Repositories\Interfaces\Admin\CommonInterface;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use PDF;

class CommonController extends Controller
{
    private $common;

    public function __construct(CommonInterface $common)
    {
        $this->common = $common;
    }

    public function delete($id)
    {
        if (isDemoServer()):
            $response['message']    = __('This function is disabled in demo server.');
            $response['title']      = __('Ops..!');
            $response['status']     = 'error';
            return response()->json($response);
        endif;
        $urlArray       = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $segments       = explode('/', $urlArray);
        $numSegments    = count($segments);
        $currentSegment = $segments[$numSegments - 2];
        if ($status = $this->common->delete($currentSegment, $id)):
            if ($status === 'used'):
                $response['message'] = __('Unable to delete because this type is already used');
                $response['status']  = 'error';
                $response['title']   = __('Ops..!');
            else:
                $response['message'] = __('Deleted Successfully!');
                $response['status']  = 'success';
                $response['title']   = __('Deleted');
            endif;
            return response()->json($response);
        else:
            $response['message'] = __('Something went wrong, please try again');
            $response['status']  = 'error';
            $response['title']   = __('Ops..!');
            return response()->json($response);
        endif;
    }

    public function statusChange(Request $request)
    {
        if (isDemoServer()):
            $response['message']    = __('This function is disabled in demo server.');
            $response['title']      = __('Ops..!');
            $response['status']     = 'error';
            return response()->json($response);
        endif;
        if($this->common->statusChange($request['data'])):
            $response['message'] = __('Updated Successfully');
            $response['title']   = __('Success');
            $response['status']   = 'success';
            return response()->json($response);
        else:
            $response['message'] = __('Something went wrong, please try again');
            $response['title']   = __('Ops..!');
            $response['status']   = 'error';
            return response()->json($response);
        endif;

    }
    public function editInfo($page_name, $param1 = null)
    {
        $otherLinks         = null;
        if ($param1) :
            $otherLinks     = explode('/', $param1);
        endif;

        return view("admin.modals.$page_name", compact('otherLinks'));
    }

    public function importSample(Request $request, $type)
    {
        $filename = 'excel/'.$type.'-import-sample.xlsx';
        if (file_exists(public_path($filename))):
            $filepath = public_path($filename);
            return response()->download($filepath);
        else:
            return back()->with('danger',__('file_not_found'));
        endif;
    }

    public function download(Request $request, $type)
    {
        try {
            if ($type == 'category'):
                $items  = Category::where('status',1)->get();
            elseif ($type == 'brand'):
                $items  = Brand::where('status',1)->get();
            else:
                return back()->with('danger',__('file_not_found'));
            endif;
            $pdf    = PDF::loadView('admin.common.category-brand', [
                'items' => $items,
                'title' => __($type),
            ]);

            return $pdf->download($type . '.pdf');
        } catch (\Exception $e) {
            return back()->with('danger',__('Something went wrong'));
        }
    }

    public function productImportPost(Request $request)
    {
        $extension = request()->file('file')->getClientOriginalExtension();

        if ($extension != 'xlsx' && $extension != 'xls' && $extension != 'csv'):
            return back()->with('danger', __('file_type_not_supported'));
        endif;

        $file = request()->file('file')->store('import');
        $import = new ProductImport();
        $import->import($file);

        unlink(storage_path('app/'.$file));
        Toastr::success(__('successfully_imported'));
        return back();
    }
}
