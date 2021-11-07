<?php

namespace Modules\Home\Http\Controllers;

use App\Http\Structs\NotifStruct;
use App\Models\BbkkpSis\SysUserFbToken;
use App\Models\BbkkpSis\SysUserNotif;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class NotificationController extends Controller
{
    public function index()
    {
        $dataNotif = SysUserNotif::where("notif_user_id", auth()->id())->orderBy('notif_is_read', 'desc')->orderBy('notif_created_at', 'desc')->paginate(20);
        return view('home::notification.index')->with(['dataNotif' => $dataNotif]);
    }

    public function open($id)
    {
        $dataNotif = SysUserNotif::where('notif_user_id', auth()->id())->findOrFail($id);
        if ($dataNotif) {
            $dataNotif->notif_is_read    = "yes";
            $dataNotif->notif_updated_at = Carbon::now();
            $dataNotif->save();

            return redirect(url($dataNotif->notif_link));
        } else {
            abort(404);
        }
    }

    public function markAllAsRead()
    {
        SysUserNotif::where('notif_user_id', auth()->id())->update(['notif_is_read' => 'yes']);
        return redirect()->back();
    }

    public function ajaxSyncToken(Request $request)
    {
        SysUserFbtoken::firstOrCreate(
            ['fbtoken_token' => $request['token'], 'fbtoken_user_id' => auth()->id()],
            ['fbtoken_agent' => $request->header('User-agent'), 'fbtoken_ip' => $request->getClientIp()]
        );

        return responseJSON(200, null, "sinkronisasi berhasil");
    }

    public function tes()
    {
        $notifStruct            = new NotifStruct();
        $notifStruct->title     = "Halo ini judul push notifikasi";
        $notifStruct->message   = auth()->user()->user_fullname . " notifikasi berhasil ya, ini adalah isi pesannya";
        $notifStruct->user_id   = auth()->id();
        $notifStruct->click_url = url('/dashboard');
        sendNotification($notifStruct);
    }
}
