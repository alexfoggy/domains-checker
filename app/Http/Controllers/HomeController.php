<?php

namespace App\Http\Controllers;

use App\AvalaibleDomain;
use App\Domain;
use App\DomainInfo;
use App\DomainToCheck;
use App\DomainToIgnore;
use App\ParsedLink;
use App\Parsing\Parsing;
use Carbon\Carbon;
use Html2Text\Html2Text;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

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

    public function domainsToCheck()
    {
        $domainsToCheck = DomainToCheck::paginate(100);

        return view('domains_to_check', get_defined_vars());
    }

    public function domainsToCheckAvalibe()
    {
        $domainsToCheck = DomainToCheck::where('status', 1)
        ->paginate(100);

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

        foreach ($domains as $domain) {
            if (filter_var($domain, FILTER_VALIDATE_EMAIL)) {
                // split on @ and return last value of array (the domain)
                $domain = explode('@', $domain)[1];
            }
            if (!DomainToCheck::where('domain', $domain)->first()) {
                DomainToCheck::create([
                    'domain' => $domain,
                    'status' => 0
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
