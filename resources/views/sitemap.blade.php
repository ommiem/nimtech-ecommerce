<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
  @foreach($urls as $u)
  <url>
    <loc>{{ $u['loc'] }}</loc>
    @if(!empty($u['lastmod']))<lastmod>{{ $u['lastmod'] }}</lastmod>@endif
    @if(!empty($u['image']))
    <image:image>
      <image:loc>{{ $u['image'] }}</image:loc>
      @if(!empty($u['image_title']))<image:title>{{ $u['image_title'] }}</image:title>@endif
    </image:image>
    @endif
    <changefreq>daily</changefreq>
  </url>
  @endforeach
</urlset>
