@extends('crudbooster::admin_template')

@section('content')

    @if(g('return_url'))
        <p><a href='{{g("return_url")}}'><i class='fa fa-chevron-circle-{{ trans('crudbooster.left') }}'></i> &nbsp; {{trans('crudbooster.form_back_to_list',['module'=>urldecode(g('label'))])}}</a></p>
    @endif

    <div class='callout callout-danger'>
        {!! trans('crudbooster.denied_access') !!}
    </div>
    <div class="col-lg-offset-3 col-lg-6" style="margin-top: 30px;">
        <div class="jumbotron text-center bg-white access-denied-alert" style="border-radius: 10px;">
            <div class="sa-icon sa-error animateErrorIcon" style="display: block;">
              <span style="font-size: 150px; color: #dd4b39;"><i class="fa fa-times-circle-o" aria-hidden="true"></i></span>
            </div>
            <h2>{{trans('alert.access_denied.access_is_denied')}}</h2>
            <p style="display: block;">{{trans('alert.access_denied.you_are_not_authorized_to_perform_this_action')}}</p>
            <p style="display: block;">{{trans('alert.access_denied.please_contact_admin_for_permission')}}</p>
        </div>
    </div>
@endsection
