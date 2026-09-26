@php
  $settings = $settings ?? \App\Models\Setting::getCached();
  $ga4Id = trim((string) ($settings->ga4_measurement_id ?? ''));
  $googleAdsId = trim((string) ($settings->google_ads_id ?? ''));
  $metaPixelId = trim((string) ($settings->meta_pixel_id ?? ''));
  $tiktokPixelId = trim((string) ($settings->tiktok_pixel_id ?? ''));
  $primaryGtagId = $ga4Id !== '' ? $ga4Id : $googleAdsId;
@endphp

@if($primaryGtagId !== '')
  <script async src="https://www.googletagmanager.com/gtag/js?id={{ $primaryGtagId }}"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    @if($ga4Id !== '')
    gtag('config', '{{ $ga4Id }}');
    @endif
    @if($googleAdsId !== '')
    gtag('config', '{{ $googleAdsId }}');
    @endif
  </script>
@endif

@if($metaPixelId !== '')
  <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '{{ $metaPixelId }}');
    fbq('track', 'PageView');
  </script>
@endif

@if($tiktokPixelId !== '')
  <script>
    !function (w, d, t) {
      w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];
      ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie"];
      ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};
      for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);
      ttq.load=function(e,n){var r="https://analytics.tiktok.com/i18n/pixel/events.js";
      ttq._i=ttq._i||{};ttq._i[e]=[];ttq._i[e]._u=r;ttq._t=ttq._t||{};ttq._t[e]=+new Date;
      ttq._o=ttq._o||{};ttq._o[e]=n||{};n=d.createElement("script");
      n.type="text/javascript";n.async=!0;n.src=r+"?sdkid="+e+"&lib="+t;
      e=d.getElementsByTagName("script")[0];e.parentNode.insertBefore(n,e)};
      ttq.load('{{ $tiktokPixelId }}');
      ttq.page();
    }(window, document, 'ttq');
  </script>
@endif

@if(!empty($settings?->custom_head_scripts))
  {!! $settings->custom_head_scripts !!}
@endif
