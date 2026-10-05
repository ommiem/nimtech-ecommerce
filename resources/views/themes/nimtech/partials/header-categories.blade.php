@php
  $menus = [
    'Smartphones' => ['Foldable Phones','Google Pixel Phones','HMD Phones','Honor Phones','Infinix Phones','Itel Phones','Motorola Phones','Nothing Phones','OnePlus Phones','Oppo Phones','Poco Phones','Realme Phones','Redmi Phones','Tecno Phones','Vivo Phones','Xiaomi Phones'],
    'Samsung' => ['Samsung Phones','Galaxy Buds','Galaxy Tablets','Samsung Accessories','Galaxy Watches'],
    'Apple' => ['Apple iPhone','Apple iPad','MacBooks','AirPods','Apple Watch','Apple Pencil','iMac','Apple Accessories'],
    'Laptops' => ['HP Laptops','Dell Laptops','Lenovo Laptops','Acer Laptops','ASUS Laptops','MacBooks','Gaming Laptops','2-in-1 Touchscreen Laptops'],
    'Mobile Accessories' => ['Smartwatches','Chargers','Powerbanks','Smart Bands','Media Streamers','Phone Covers','Screen Protectors','Phone Stands'],
    'Audio' => ['Buds','Speakers','Headphones','In-Ear Headphones','Soundbars','Microphones'],
    'Gaming' => ['Gaming Consoles','Gaming Controllers','Gaming Headsets','Gaming Phones','Nintendo','PS5 Games'],
    'Storage' => ['Flash Drives','SSDs','Hard Drives','Memory Cards','USB Hubs'],
    'Tablets' => ['Amazon Tablets','Apple iPad','ElimuTab','Galaxy Tablets','Kids Tablets','Modio Tablets','reMarkable'],
    'TV Remotes' => ['Samsung TV Remotes','LG TV Remotes','Sony TV Remotes','Hisense TV Remotes','TCL TV Remotes','Vitron TV Remotes','Universal TV Remotes'],
  ];
  $menuLandingRoutes = [
    'Smartphones' => route('seo.phones'),
    'Samsung' => route('seo.samsung-kenya'),
    'Apple' => route('seo.iphone-kenya'),
    'Laptops' => route('seo.laptops'),
  ];
@endphp
<nav class="nimtech-category-shell border-b bg-white" aria-label="Product categories">
  <div class="nimtech-category-row mx-auto max-w-7xl px-4">
    @foreach($menus as $menuLabel => $items)
      <details class="nimtech-nav-dropdown">
        <summary>{{ $menuLabel }}</summary>
        <div class="nimtech-nav-menu">
          @if(isset($menuLandingRoutes[$menuLabel]))
            <a href="{{ $menuLandingRoutes[$menuLabel] }}" class="font-semibold">Shop all {{ $menuLabel }}</a>
          @endif
          @foreach($items as $item)
            <a href="{{ route('products.index', ['q' => $item]) }}">{{ $item }}</a>
          @endforeach
        </div>
      </details>
    @endforeach
  </div>
</nav>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const menus = Array.from(document.querySelectorAll('.nimtech-nav-dropdown'));
    const hoverCapable = window.matchMedia('(hover: hover) and (pointer: fine)');
    const closeOthers = active => menus.forEach(menu => { if (menu !== active) menu.open = false; });

    menus.forEach(menu => {
      menu.addEventListener('toggle', () => { if (menu.open) closeOthers(menu); });
      menu.addEventListener('mouseenter', () => {
        if (hoverCapable.matches) { closeOthers(menu); menu.open = true; }
      });
      menu.addEventListener('mouseleave', () => {
        if (hoverCapable.matches) menu.open = false;
      });
    });

    document.addEventListener('pointerdown', event => {
      if (!event.target.closest('.nimtech-nav-dropdown')) menus.forEach(menu => menu.open = false);
    });
    document.addEventListener('keydown', event => {
      if (event.key === 'Escape') menus.forEach(menu => menu.open = false);
    });
  });
</script>
