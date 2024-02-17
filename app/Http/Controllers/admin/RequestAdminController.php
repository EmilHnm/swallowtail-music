<?php

namespace App\Http\Controllers\admin;

use App\Enum\RequestStatusEnum;
use App\Models\Request as ClientRequest;
use Illuminate\Http\Request;
use Orchid\Support\Facades\Toast;

trait RequestAdminController
{
    public function asyncPassingId(Request $request)
    {
        return [
            'id' => $request->get('id'),
        ];
    }

    public function asyncPassingModal(Request $request)
    {
        return [
            'data' => ClientRequest::with([
                'responses'
                ])->find($request->get('id')),
        ];
    }

    public function deny($id) {
        $request = ClientRequest::find($id);
        $request->status = RequestStatusEnum::DENIED;
        $request->save();
        Toast::warning('Request denied!');
        return redirect()->route('platform.communities.requests');
    }

    public function response(Request $request)
    {

        $request->validate([
            'id' => 'required|exists:requests,id',
            'content' => 'required|string',
        ]);

        $client_request = ClientRequest::find($request->id);

        if($client_request->responses()->where('responder', \Auth::user()->user_id)->exists()) {
            Toast::warning('You have already responded to this request!');
            return redirect()->route('platform.communities.requests');
        }

        $client_request->responses()->create([
            'responder' => \Auth::user()->user_id,
            'content' => $request->content,
        ]);

        Toast::success('Request responded!');
        return redirect()->route('platform.communities.requests');
    }
}
