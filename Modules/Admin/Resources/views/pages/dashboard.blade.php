@extends('admin::template.base_template')
@section('content')
    <div class="row">
        @foreach($data as $key => $item)
            <div class="col-xl-3 col-sm-6 mb-4 {{!is_numeric($key) ? 'cursor-pointer' : ''}}"
                 onclick="fnShow('{{$key}}')">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold
                                     text-truncate">{{$item['title']}}</p>
                                    <h5 class="font-weight-bolder mb-0" data-toggle="counter">
                                        {{$item['value']}}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-primary shadow text-center
                                 border-radius-md">
                                    <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if(isset($userData))
        <div id="user-info" style="display: none">
            <div class="hr-sect">User Info</div>
            <div class="row">
                @foreach($userData as $ud)
                    <div class="col-xl-3 col-sm-6 mb-4">
                        <div class="card">
                            <div class="card-body p-3">
                                <div class="row">
                                    <div class="col-8">
                                        <div class="numbers">
                                            <p class="text-sm mb-0 text-capitalize font-weight-bold
                                     text-truncate">{{$ud['title']}}</p>
                                            <h5 class="font-weight-bolder mb-0" data-toggle="counter">
                                                {{$ud['value']}}
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <div class="icon icon-shape bg-gradient-primary shadow text-center
                                 border-radius-md">
                                            <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if(isset($subscriptionData))
        <div id="subscription-info" style="display: none">
            <div class="hr-sect">Subscription Info</div>
            <div class="row">
                @foreach($subscriptionData as $subsD)
                    <div class="col-xl-3 col-sm-6 mb-4">
                        <div class="card">
                            <div class="card-body p-3">
                                <div class="row">
                                    <div class="col-8">
                                        <div class="numbers">
                                            <p class="text-sm mb-0 text-capitalize font-weight-bold
                                     text-truncate">{{$subsD['title']}}</p>
                                            <h5 class="font-weight-bolder mb-0" data-toggle="counter">
                                                {{$subsD['value']}}
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <div class="icon icon-shape bg-gradient-primary shadow text-center
                                 border-radius-md">
                                            <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if(isset($mediaData))
        <div id="media-info" style="display: none">
            <div class="hr-sect">Media Info</div>
            <div class="row">
                @foreach($mediaData as $md)
                    <div class="col-xl-3 col-sm-6 mb-4">
                        <div class="card">
                            <div class="card-body p-3">
                                <div class="row">
                                    <div class="col-8">
                                        <div class="numbers">
                                            <p class="text-sm mb-0 text-capitalize font-weight-bold
                                     text-truncate">{{$md['title']}}</p>
                                            <h5 class="font-weight-bolder mb-0" data-toggle="counter">
                                                {{$md['value']}}
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <div class="icon icon-shape bg-gradient-primary shadow text-center
                                 border-radius-md">
                                            <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if(isset($categoryData))
        <div id="category-info" style="display: none">
            <div class="hr-sect">Category Info</div>
            <div class="row">
                @foreach($categoryData as $cd)
                    <div class="col-xl-3 col-sm-6 mb-4">
                        <div class="card">
                            <div class="card-body p-3">
                                <div class="row">
                                    <div class="col-8">
                                        <div class="numbers">
                                            <p class="text-sm mb-0 text-capitalize font-weight-bold
                                     text-truncate">{{$cd['title']}}</p>
                                            <h5 class="font-weight-bolder mb-0" data-toggle="counter">
                                                {{$cd['value']}}
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <div class="icon icon-shape bg-gradient-primary shadow text-center
                                 border-radius-md">
                                            <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if(isset($countryData))
        <div id="country-info" style="display: none">
            <div class="hr-sect">Country Info</div>
            <div class="row">
                @foreach($countryData as $key => $country)
                    <div class="col-xl-3 col-sm-6 mb-4">
                        <div class="card">
                            <div class="card-body p-3">
                                <div class="row">
                                    <div class="col-8">
                                        <div class="numbers">
                                            <p class="text-sm mb-0 text-capitalize font-weight-bold
                                     text-truncate">{{$key}}</p>
                                            <h5 class="font-weight-bolder mb-0" data-toggle="counter">
                                                {{$country}}
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <div class="icon icon-shape bg-gradient-primary shadow text-center
                                 border-radius-md">
                                            <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
@endsection
