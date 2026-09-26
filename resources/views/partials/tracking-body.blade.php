@php($settings = $settings ?? \App\Models\Setting::getCached())

@if(!empty($settings?->meta_pixel_id))
  <noscript>
    <img height="1" width="1" style="display:none"
         src="https://www.facebook.com/tr?id={{ $settings->meta_pixel_id }}&ev=PageView&noscript=1"/>
  </noscript>
@endif

@if(!empty($settings?->custom_body_scripts))
  {!! $settings->custom_body_scripts !!}
@endif
