@php
  $items = [
    ['label' => 'Dashboard',  'route' => 'admin.dashboard',      'active' => ['admin.dashboard'],   'icon' => 'home'],
    ['label' => 'Orders',     'route' => 'admin.orders.index',   'active' => ['admin.orders.*'],    'icon' => 'orders'],
    ['label' => 'WhatsApp Leads', 'route' => 'admin.whatsapp-leads.index', 'active' => ['admin.whatsapp-leads.*'], 'icon' => 'whatsapp'],
    ['label' => 'Buyers',     'route' => 'admin.buyers.index',   'active' => ['admin.buyers.*'],    'icon' => 'users'],
    ['label' => 'Users',      'route' => 'admin.users.index',    'active' => ['admin.users.*'],     'icon' => 'users', 'super' => true],
    ['label' => 'Brands',     'route' => 'admin.brands.index',   'active' => ['admin.brands.*'],    'icon' => 'brands'],
    ['label' => 'Products',   'route' => 'admin.products.index', 'active' => ['admin.products.*'],  'icon' => 'products'],
    ['label' => 'Categories', 'route' => 'admin.categories.index','active'=> ['admin.categories.*'], 'icon' => 'categories'],
    ['label' => 'Promotions', 'route' => 'admin.promotions.index','active'=> ['admin.promotions.*'], 'icon' => 'promotions'],
    ['label' => 'Campaigns',  'route' => 'admin.campaigns.index','active'=> ['admin.campaigns.*'],  'icon' => 'campaigns'],
    ['label' => 'Reports',    'route' => 'admin.reports.index',  'active' => ['admin.reports.*'],   'icon' => 'reports'],
    ['label' => 'M-Pesa Tools','route' => 'admin.mpesa.tools',   'active'=> ['admin.mpesa.*'],      'icon' => 'mpesa', 'super' => true],
    ['label' => 'Pages',      'route' => 'admin.pages.index',    'active' => ['admin.pages.*'],     'icon' => 'documents'],
    ['label' => 'Slides',     'route' => 'admin.slides.index',   'active' => ['admin.slides.*'],    'icon' => 'slides'],
    ['label' => 'Settings',   'route' => 'admin.settings.edit',  'active' => ['admin.settings.*'],  'icon' => 'settings', 'super' => true],
  ];
@endphp

<nav class="space-y-1">
  @foreach($items as $item)
    @if(($item['super'] ?? false) && !auth()->user()->isSuperAdmin())
      @continue
    @endif
    @php($isActive = collect($item['active'])->contains(fn($p) => request()->routeIs($p)))
    <a href="{{ route($item['route']) }}"
       @if($isActive) aria-current="page" @endif
       class="flex items-center gap-2 px-3 py-2 rounded text-sm {{ $isActive ? 'bg-gray-100 text-gray-900 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">
      <span class="shrink-0 {{ $isActive ? 'text-gray-900' : 'text-gray-500' }}">
        @switch($item['icon'])
          @case('home')
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>
            @break
          @case('orders')
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" /></svg>
            @break
          @case('whatsapp')
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 20.25a9 9 0 1 1 8.272-2.894L21 21l-4.144-4.144A8.963 8.963 0 0 1 8.625 20.25Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 10.5a.75.75 0 0 1 1.5 0v.25c0 .414.336.75.75.75h.25a.75.75 0 0 1 0 1.5h-.25A2.25 2.25 0 0 1 9.75 11v-.5Z" /></svg>
            @break
          @case('products')
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" /></svg>
            @break
          @case('users')
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" /></svg>
            @break
          @case('documents')
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5A3.375 3.375 0 0 0 10.125 2.25H5.625A3.375 3.375 0 0 0 2.25 5.625v12.75A3.375 3.375 0 0 0 5.625 21.75h9.75A3.375 3.375 0 0 0 18.75 18.375V16.5m0 0h-4.875A2.625 2.625 0 0 1 11.25 13.875V9"/></svg>
            @break
          @case('brands')
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75V21h15V9.75"/></svg>
            @break
          @case('categories')
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" /></svg>
            @break
          @case('promotions')
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l.259-1.295A1.125 1.125 0 0 1 9.607 2.25h4.786a1.125 1.125 0 0 1 1.098.955L15.75 4.5m-7.5 0h7.5m-7.5 0H5.97c-.62 0-1.123.504-1.12 1.124.01 1.88.3 8.26 1.218 11.1.26.792.964 1.376 1.795 1.486 2.064.276 4.149.276 6.213 0 .831-.11 1.535-.694 1.795-1.486.918-2.84 1.208-9.22 1.218-11.1.003-.62-.5-1.124-1.12-1.124H15.75m-6 4.125h4.5m-4.5 3h3" /></svg>
            @break
          @case('campaigns')
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 3.75h3m-8.25 3h13.5m-12 0 .6 11.393A2.25 2.25 0 0 0 9.596 20.25h4.808a2.25 2.25 0 0 0 2.246-2.107l.6-11.393M9.75 10.5h4.5M9.75 14.25h3" /></svg>
            @break
          @case('mpesa')
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 6.75C3 5.784 3.784 5 4.75 5h14.5C20.216 5 21 5.784 21 6.75v10.5A1.75 1.75 0 0 1 19.25 19H4.75A1.75 1.75 0 0 1 3 17.25V6.75Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M3 9h18M7.5 14.25h3m0 0H9.75m.75 0v1.5m2.25-1.5h1.5a1.5 1.5 0 0 1 0 3H12.75m0-3v3" /></svg>
            @break
          @case('slides')
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" /></svg>
            @break
          @case('settings')
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
            @break
          @case('reports')
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h12.75M3.75 3h-1.5M3.75 3h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5m-12 0v1.875A2.625 2.625 0 0 0 8.625 21h6.75A2.625 2.625 0 0 0 18 18.375V16.5m-9 0h9M9.75 8.25l1.5 1.5 3-3" /></svg>
            @break
        @endswitch
      </span>
      <span>{{ $item['label'] }}</span>
    </a>
  @endforeach

  <div class="pt-3 mt-3 border-t">
    <a href="{{ route('products.index') }}" class="flex items-center gap-2 px-3 py-2 rounded text-sm text-gray-600 hover:bg-gray-50">
      <span class="shrink-0 text-gray-500"><svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 0 1 1.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.56.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.893.149c-.425.07-.765.383-.93.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 0 1-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.397.165-.71.505-.781.93l-.149.893c-.09.543-.56.94-1.11.94H11.45c-.55 0-1.02-.397-1.11-.94l-.149-.893c-.07-.425-.383-.765-.78-.93-.398-.165-.854-.143-1.204.107l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 0 1-.12-1.449l.527-.738c.25-.35.273-.806.108-1.203-.165-.397-.505-.71-.93-.781l-.894-.149C3.94 13.02 3.543 12.55 3.543 12v-1.094c0-.55.397-1.02.94-1.11l.894-.149c.424-.07.764-.383.93-.78.164-.398.142-.854-.108-1.204l-.527-.737a1.125 1.125 0 0 1 .12-1.45l.773-.773a1.125 1.125 0 0 1 1.45-.12l.738.527c.35.25.806.272 1.203.107.397-.165.71-.505.781-.93l.149-.893Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg></span>
      <span>Back to Store</span>
    </a>
  </div>
</nav>
