@extends('admin.layout')

@section('content')
    <h1 class="page-header mb-6">@lang('simplesupport::admin.listTitle')</h1>

<div class="text-center flex flex-col sm:flex-row justify-between mx-4">
    <div class="text-center">
        @if(!$list or $list == 'all')
            <span class="selector active">@lang('simplesupport::admin.listAll') ({{ $stats['all'] }})</span>
        @else 
            <a class="selector" href="/support">@lang('simplesupport::admin.listAll') ({{ $stats['all'] }})</a>
        @endif

        @if($list == 'search')
            <span class="selector active">@lang('simplesupport::admin.listSearchResults') ({{ $stats['search'] }})</span>
        @else
            @if($list == 'unread')
                <span class="selector active">@lang('simplesupport::admin.listUnread') ({{ $stats['unread'] }})</span>
            @else 
                <a class="selector" href="/support/unread">@lang('simplesupport::admin.listUnread') ({{ $stats['unread'] }})</a>
            @endif
        @endif
    </div>
    <div class="sm:ml-2">
        <form action="/support/search" role="search" style="white-space: nowrap;">
            <input class="py-1 px-3 w-auto border border-r-0 rounded-l-lg border-gray-400 focus:outline-none" 
                type="text" name="q" value="{{ $query ?? '' }}" placeholder="@lang('simplesupport::admin.placeholder')" required><button type="submit" class="py-1 px-3 border rounded-r-lg border-indigo-500 bg-indigo-500 text-white">
                <svg class="fill-current text-white h-5 inline-block" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path class="heroicon-ui" d="M16.32 14.9l5.39 5.4a1 1 0 0 1-1.42 1.4l-5.38-5.38a8 8 0 1 1 1.41-1.41zM10 16a6 6 0 1 0 0-12 6 6 0 0 0 0 12z"/></svg>
            </button>
        </form>
    </div>
</div>

@forelse ($dialogs as $dialog)
    @include('simplesupport::admin.card', ['dialog' => $dialog])
@empty
    <div class="p-4 m-4 border rounded-lg border-gray-200 text-center text-gray-700">
        @lang('simplesupport::admin.listEmpty')
    </div>
@endforelse

{{ $dialogs->links('vendor.pagination.simple') }}

@endsection
