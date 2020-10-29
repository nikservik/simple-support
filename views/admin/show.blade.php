@extends('admin.layout')

@section('content')
<h1 class="page-header">
    <a href="/support" class="text-white">@lang('simplesupport::admin.listTitle')</a> 
</h1>
<h2 class="sub-header">
	<svg class="fill-current inline-block w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M6 14H4a2 2 0 0 1-2-2V4c0-1.1.9-2 2-2h12a2 2 0 0 1 2 2v2h2a2 2 0 0 1 2 2v13a1 1 0 0 1-1.7.7L16.58 18H8a2 2 0 0 1-2-2v-2zm0-2V8c0-1.1.9-2 2-2h8V4H4v8h2zm14-4H8v8h9a1 1 0 0 1 .7.3l2.3 2.29V8z"/></svg>
    <a href="/users/{{ $user->id }}">
    	{{ $user->name }}
        <span class="text-base">({{ $user->email }})</span>
    </a>
</h2>

<div class="my-6 mx-2 md:mx-10 flex">
	<div class="w-1/2 text-right pr-2">
		@if(! $messages->onFirstPage())
			<a class="selector" href="{{ $messages->previousPageUrl() }}">
				‹ @lang('simplesupport::admin.later')
			</a>
		@endif
	</div>
	<div class="w-1/2 pl-2">
		@if($messages->hasMorePages())
			<a class="selector" href="{{ $messages->nextPageUrl() }}">
				@lang('simplesupport::admin.earlier') ›
			</a>
		@endif
	</div>
</div>

<div class="mx-2 md:mx-10">
@for($index=count($messages)-1 ; $index>=0 ; $index--)
	<div class="support-message 
		@if($messages[$index]->type == 'userMessage') user @else admin @endif">
		<div class="user-info">
			@if($messages[$index]->type == 'userMessage') {{ $user->name }} @endif
			{{ $messages[$index]->created_at->addHours(3)->format('d.m.Y H:i') }} 
			@lang('simplesupport::admin.moscowTime')
		</div>
		<div class="message"> 
    		{!! $messages[$index]->message !!}
    		@if($messages[$index]->type != 'userMessage')
    			<div class="controls">
    				<a href="/support/message/{{ $messages[$index]->id }}/delete" 
    					onclick="return confirm('@lang('simplesupport::admin.confirmDelete')')">
    					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" class="inline-block fill-current text-gray-500"><path class="heroicon-ui" d="M8 6V4c0-1.1.9-2 2-2h4a2 2 0 0 1 2 2v2h5a1 1 0 0 1 0 2h-1v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V8H3a1 1 0 1 1 0-2h5zM6 8v12h12V8H6zm8-2V4h-4v2h4zm-4 4a1 1 0 0 1 1 1v6a1 1 0 0 1-2 0v-6a1 1 0 0 1 1-1zm4 0a1 1 0 0 1 1 1v6a1 1 0 0 1-2 0v-6a1 1 0 0 1 1-1z"/></svg>
    				</a>
    			</div>
    		@endif
    	</div>
    </div>
@endfor
</div>

<div class="my-4 mx-2 md:mx-10 pl-10">
	<form action="/support/dialog/{{ $user->id }}#read" method="POST">
		@csrf
		<div class="flex">
			<div class="w-full">
				<div class="text-sm text-gray-500 text-right">
					@lang('simplesupport::admin.admin')
				</div>
				<textarea class="w-full rounded-b rounded-tl px-4 py-4 border-2 focus:outline-none
					@error('message') border-red-500 @else border-gray-300 @enderror" 
					name="message" rows="3" id="read">{{ old('message') }}</textarea>
			</div>
			<div class="pt-6 ml-1">
				<button class="button circle">
					<svg class="fill-current w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M13 5.41V21a1 1 0 0 1-2 0V5.41l-5.3 5.3a1 1 0 1 1-1.4-1.42l7-7a1 1 0 0 1 1.4 0l7 7a1 1 0 1 1-1.4 1.42L13 5.4z"/></svg>
				</button>
			</div>
		</div>
	</form>
</div>

@endsection


