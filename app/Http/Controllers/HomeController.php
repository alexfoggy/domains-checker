<?php

namespace App\Http\Controllers;

use App\AvalaibleDomain;
use App\Domain;
use App\DomainInfo;
use App\DomainToCheck;
use App\DomainToIgnore;
use App\Helpers\Helper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class HomeController extends Controller
{

    public function index(Request $request)
    {
        $domains = DomainInfo::get();
        return view('welcome', compact('domains'));
    }

    public function create()
    {

        return view('domains.insert');
    }

    public function delete(Domain $domain)
    {

        $domain->delete();

        return redirect()->route('domains');
    }

    public function insert(Request $request)
    {
        $inputs = request()->validate([
            'domain' => 'required|string'
        ]);

        //$inputs['domain'] = parse_url($inputs['domain'])['host'];
        $inputs['active'] = 1;
        $inputs['same_site'] = $request->input('same_site');

        Domain::create($inputs);

        return redirect()->route('domains');
    }

    public function details(Domain $domain)
    {
        $avalaibleDomains = AvalaibleDomain::where('domain_id', $domain->id)->orderBy('created_at', 'DESC')->paginate(30);

        return view('statistics', compact('domain', 'avalaibleDomains'));
    }

    public function free(Domain $domain)
    {
        $avalaibleDomains = AvalaibleDomain::where('domain_id', $domain->id)->where('status', '1')->orderBy('created_at', 'DESC')->paginate(30);

        return view('statistics', compact('domain', 'avalaibleDomains'));
    }

    public function domainChangeStatus(AvalaibleDomain $domain, $status)
    {
        $domain->status = $status;
        $domain->save();

        return redirect()->back();
    }

    public function domainsToIgnoreList()
    {
        $domains = DomainToIgnore::orderBy('id', 'desc')->get();

        return view('domains_to_ignore', get_defined_vars());
    }

    public function domainsToIgnoreDelete($id)
    {
        DomainToIgnore::where('id', $id)->delete();
        return redirect()->back();
    }

    public function domainsToIgnoreCreate()
    {
        $inputs = request()->validate([
            'domain' => 'required|unique:domains_to_ignore|string'
        ]);

        DomainToIgnore::create($inputs);

        return redirect()->route('domainsToIgnore');
    }

    public function domainsToCheck(Request $request)
    {
        $query = DomainToCheck::query();

        if ($request->has('tag') && $request->tag !== '') {
            $query->where('tag', $request->tag);
        }

        if ($request->has('search') && $request->search !== '') {
            $query->where('domain', 'like', '%' . $request->search . '%');
        }

        $domainsToCheck = $query->paginate(100)->appends($request->query());
        $tags = DomainToCheck::distinct()->pluck('tag')->filter()->sort()->values();
        $domainsLast7Days = DomainToCheck::where('created_at', '>=', now()->subDays(7))->count();

        return view('domains_to_check', get_defined_vars());
    }

    public function domainsToCheckRestart(): RedirectResponse
    {
        DomainToCheck::where('status', 2)->where('is_checked', 0)->update(['status' => 0]);

        return redirect()->route('domains.to.check');
    }

    public function domainsToCheckAvalibe(Request $request)
    {
        $query = DomainToCheck::where('status', 1);

        if ($request->has('tag') && $request->tag !== '') {
            $query->where('tag', $request->tag);
        }

        if ($request->has('search') && $request->search !== '') {
            $query->where('domain', 'like', '%' . $request->search . '%');
        }

        $domainsToCheck = $query->orderBy('is_checked')->paginate(100)->appends($request->query());
        $tags = DomainToCheck::distinct()->pluck('tag')->filter()->sort()->values();
        $domainsLast7Days = DomainToCheck::where('created_at', '>=', now()->subDays(7))->count();

        return view('domains_to_check', get_defined_vars());
    }

    public function checkedUpdateStatus(Request $request)
    {
        $checkbox = $request->input('checkbox');
        if (!empty($checkbox)) {
            foreach ($checkbox as $key => $val) {
                DomainToCheck::where('id', $key)->update(['is_checked' => 1]);
            }
        }
        return back();
    }

    public function domainsFree()
    {
        $domain = '';
        $avalaibleDomains = AvalaibleDomain::where('status', 1)->orderBy('created_at', 'DESC')->paginate(40);
        return view('statistics', get_defined_vars());
    }

    public function domainsToCheckUpload(Request $request)
    {
        $domains = explode(PHP_EOL, $request->input('domains'));
        $tag = $request->input('tag', 'agency');

        foreach ($domains as $domain) {
            $clearedDomain = Helper::cleanDomain($domain);

            if (!DomainToCheck::where('domain', $clearedDomain)->first()) {
                DomainToCheck::create([
                    'domain' => $clearedDomain,
                    'status' => 0,
                    'tag' => $tag
                ]);
            }
        }

        return redirect()->route('domains.to.check');
    }

    public function priotiry($id)
    {
        Domain::where('id', $id)->update(['updated_at' => '2020-11-29 11:57:20']);
        Artisan::call('cache:clear');
        return response()->redirectTo('/');
    }
}
