@component('mail::layout')

  @slot('header')
    {{-- @component('mail::header', ['url' => $website_url])
      {{ $website_name }}
    @endcomponent --}}
  @endslot



  {!! $template !!}



  @slot('footer')
    @component('mail::footer')


      {{-- {!! trans('campaign.email_ip_address', ['ip_address' => request()->ip()]) !!} --}}

    @endcomponent
  @endslot
@endcomponent