@extends('layouts.index')

@section('content')
    <div class="section-wrapper mg-t-20">
        <label class="section-title">@if($domain)
                Avalaible domains from {{$domain->domain}}
            @endif</label>

        <a href="{{route('domains')}}" class="btn btn-primary btn-block mg-b-10">Back</a>
        <div class="table-responsive">
            <table class="table table-striped mg-b-0">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Domain</th>
                    <th style="width: 30%;">From</th>
                    <th>Date</th>
                    <th style="text-align: right">Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($avalaibleDomains as $key => $domain)
                    <tr>

                        <th scope="row" @if($domain->status==\App\AvalaibleDomains\DomainStatus::REJECTED)
                            style="background: #dc3545"
                            @elseif($domain->status==\App\AvalaibleDomains\DomainStatus::CONFIRM)
                                style="background: #1CAF9A"
                            @endif>{{$domain->id}}</th>
                        <td>
                            <a href="https://app.ahrefs.com/site-explorer/overview?backlinksChartMode=metrics&backlinksChartPerformanceSources=domainRating%7C%7CurlRating&backlinksCompetitorsSource=%22UrlRating%22&backlinksRefdomainsSource=%22RefDomainsNew%22&bestFilter=all&brandedTrafficChartMetric=organic-traffic&brandedTrafficSource=target-brand&chartGranularity=monthly&chartInterval=month6&competitors=&countries=&country=all&entitiesCategory=organisations&generalChartBrandedTraffic=non-branded%7C%7Cother-brands%7C%7Ctarget-brand&generalChartMode=metrics&generalChartPerformanceSources=crawledPages%7C%7CdomainRating%7C%7Cimpressions%7C%7CorganicPages%7C%7CorganicTraffic%7C%7CorganicTrafficValue%7C%7CpaidTraffic%7C%7CrefDomains%7C%7CurlRating&generalCompetitorsSource=%22OrganicTraffic%22&generalCountriesSource=organic-traffic&generalEntitiesChartMetric=Traffic&generalPagesByTrafficChartMode=Percentage&generalPagesByTrafficSource=Pages%7C%7CTraffic&highlightChanges=1m&intentsMainSource=informational&keywordsSource=all&mode=subdomains&organicChartBrandedTraffic=non-branded%7C%7Cother-brands%7C%7Ctarget-brand&organicChartMode=metrics&organicChartPerformanceSources=impressions%7C%7CorganicTraffic%7C%7CorganicTrafficValue&organicCompetitorsSource=%22OrganicTraffic%22&organicCountriesSource=organic-traffic&organicEntitiesChartMetric=Traffic&organicPagesByTrafficChartMode=Percentage&organicPagesByTrafficSource=Pages%7C%7CTraffic&overviewSerpChartMode=Own&overviewSerpChartSpec=AIOverview%7C%7CAdwordsBottom%7C%7CAdwordsTop%7C%7CDiscussions%7C%7CFeaturedSnippet%7C%7CImagePack%7C%7CKnowledgeCard%7C%7CKnowledgePanel%7C%7CLocalPack%7C%7CPaidSiteLinks%7C%7CPeopleAlsoAsk%7C%7CShoppingAds%7C%7CShoppingOrganic%7C%7CSitelinks%7C%7CThumbnail%7C%7CTopStories%7C%7CTweets%7C%7CVideoPreview%7C%7CVideos&overviewSerpManyChartSpec=Own%7C%7CTotal&overview_tab=general&paidSearchPaidKeywordsByTopPositionsChartMode=Percentage&paidTrafficSources=cost%7C%7Ctraffic&target={{$domain->domain}}&topLevelDomainFilter=all&topOrganicKeywordsMode=normal&topOrganicPagesMode=normal&trafficType=Organic&volume_type=average"
                               target="_blank">{{$domain->domain}} @if($domain->is_premium)
                                    <span class="btn-warning px-2 py-1 font-weight-bold rounded"
                                          style="font-size: 10px">premium</span>
                                @endif</a></td>
                        <td style="width: 30%;"><a href="{{$domain->from}}" target="_blank">{{$domain->from}}</a></td>
                        <td>{{$domain->created_at}}</td>
                        <td align="right">
                            <div class="dropdown mg-sm-l-10 mg-t-10 mg-sm-t-0">
                                <button class="btn btn-outline-primary dropdown-toggle" type="button"
                                        id="dropdownMenuButton2" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">
                                    Status
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton2">
                                    <a class="dropdown-item"
                                       href="{{route('domain.change.status', [$domain->id, \App\AvalaibleDomains\DomainStatus::UNKNOWN])}}">UNKNOWN</a>
                                    <a class="dropdown-item"
                                       href="{{route('domain.change.status', [$domain->id, \App\AvalaibleDomains\DomainStatus::CONFIRM])}}">CONFIRM</a>
                                    <a class="dropdown-item"
                                       href="{{route('domain.change.status', [$domain->id, \App\AvalaibleDomains\DomainStatus::REJECTED])}}">REJECTED</a>
                                </div><!-- dropdown-menu -->
                            </div><!-- dropdown -->
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{$avalaibleDomains->links()}}
        </div><!-- bd -->
    </div><!-- section-wrapper -->
@endsection
